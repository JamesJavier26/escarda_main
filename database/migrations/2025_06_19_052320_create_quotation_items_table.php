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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');

            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->longText('item_name')->nullable();
            $table->longText('item_description')->nullable();
            $table->longtext('remarks');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('mould_id')->nullable()->constrained('moulds')->onDelete('cascade');

            $table->enum('discount_type', ['percentage','fixed'])->default('percentage')->nullable();
            $table->decimal('discount_amount',8,2)->default(0)->nullable();
            $table->integer('quantity')->default(0)->nullable();
            $table->decimal('unit_price',8,2)->default(0)->nullable();
            $table->json('price_factor')->nullable(); //R,E,L,M,D
            $table->decimal('factor_rate', 8, 4)->nullable();
            $table->unsignedInteger('per_quantity')->default(0)->nullable();
            $table->tinyInteger('quot')->default(false)->nullable();
            $table->longText('notes')->nullable();
            $table->longText('offer')->nullable();
            $table->tinyInteger('is_offer')->default(0)->nullable();
            $table->decimal('sum_charge', 15, 2)->nullable();
            $table->bigInteger('ref_item_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
