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
        Schema::create('production_detail_reports', function (Blueprint $table) {
            $table->id();
            $table->string('jo_no');

            // Relationships
            $table->foreignId('job_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('jo_item_id')->nullable()->constrained('job_order_items')->nullOnDelete();
            $table->foreignId('pressman_id')->constrained('users')->onDelete('cascade'); 

            // Report details
            $table->string('press')->nullable();
            $table->timestamp('date_production');
            $table->string('production_type')->nullable();
            $table->boolean('is_linear')->default(false);

            // Ply options
            $table->boolean('ply')->default(false);
            $table->string('ply_types')->nullable();
            $table->string('ply_type_others')->nullable();

            // Metal insert options
            $table->boolean('metal_insert')->default(false);
            $table->string('metal_insert_types')->nullable();
            $table->string('metal_insert_details')->nullable();

            // Status & remarks
            $table->string('pdr_status')->default('pending'); 
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_detail_reports');
    }
};
