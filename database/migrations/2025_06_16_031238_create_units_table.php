<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Piece", "Kilogram", "Liter"
            $table->string('singular_name'); // e.g.
            $table->string('short_name'); // e.g., "pc", "kg", "L"
            $table->string('type')->nullable(); // e.g., "weight", "volume", "length", "count"
            $table->integer('decimal_places')->default(2); 
            $table->longText('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
