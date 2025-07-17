<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // “admin” can rename group, add/remove users, etc.
            $table->enum('role', ['admin', 'member'])->default('member');

            // Last time *this* user opened the thread
            $table->timestamp('last_read_at')->nullable();

            // Optional extras you’ll usually want
            $table->boolean('is_muted')->default(false);         // silence notifications
            $table->timestamp('joined_at')->nullable();          // lets you show “joined 3 days ago”
            $table->timestamp('left_at')->nullable();            // soft‑leaving a group

            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);      // no duplicates
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_users');
        

    }
};