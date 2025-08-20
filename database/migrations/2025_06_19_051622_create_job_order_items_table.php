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
        Schema::create('job_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('item_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('item_name')->nullable();
            $table->longText('item_description')->nullable();
            $table->foreignId('mould_id')->nullable()->constrained()->nullOnDelete();
            $table->string('new_mould_no')->nullable()->after('mould_id');
            $table->string('new_mould_name')->nullable()->after('new_mould_no');
            $table->string('note')->nullable()->after('new_mould_name');
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->string('hardness')->nullable();
            $table->string('thickness')->nullable();
            $table->string('thickness_unit')->nullable(); //mm or in
            $table->integer('quantity')->default(0)->nullable();
            $table->boolean('is_linear')->default(false);
            $table->string('total_length')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('unit_price',10, 2)->nullable();
            $table->timestamps();
            $table->foreignId('pricing_item_id')->nullable()->constrained('pricing_estimate_items')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_items');
    }
};
