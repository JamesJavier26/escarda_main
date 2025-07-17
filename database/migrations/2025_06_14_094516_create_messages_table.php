<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('user_id')          // sender
                  ->constrained()
                  ->cascadeOnDelete();

            // Text body (store raw emoji or unified shortcode)
            $table->text('body')->nullable();

            // One attachment per row; change to JSON or separate table if you need multi‑file
            $table->string('attachment')->nullable();

            // Helps the UI decide how to render
            $table->enum('message_type', ['text', 'image', 'file', 'emoji'])
                  ->default('text');

            // Self‑referencing reply/quote
            $table->foreignId('reply_to_id')
                  ->nullable()
                  ->constrained('messages')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at'); // fast “latest” queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};