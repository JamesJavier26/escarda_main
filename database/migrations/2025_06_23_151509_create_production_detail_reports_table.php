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
            $table->foreignId('job_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('jo_item_id')->nullable()->constrained('job_order_items')->nullOnDelete();
            $table->foreignId('pressman_id')->constrained('users')->onDelete('cascade'); 
            $table->string('press')->nullable();
            $table->string('temperature')->nullable();
            $table->timestamp('date_production');
            $table->boolean('is_old_checked')->default(false);
            $table->boolean('is_new_checked')->default(false);
            $table->boolean('is_ready_to_use')->default(false);

            $table->boolean('ply')->default(false);
            $table->json('ply_types')->nullable();
            $table->string('ply_type_others')->nullable();

            $table->boolean('metal_insert')->default(false);
            $table->json('metal_insert_types')->nullable();
            $table->string('metal_insert_details')->nullable();

            $table->json('salang_1')->nullable();
            $table->json('salang_2')->nullable();
            $table->json('salang_3')->nullable();
            $table->string('pdr_status')->default(value: 'pending'); 
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
