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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('item_categories');
            $table->string('number')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name');
            $table->text('description');
            $table->foreignId('unit_id')->constrained('units');
            $table->longText('remarks')->nullable();
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_active')->default(true);


            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
