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
        Schema::create('production_detail_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_detail_report_id')->constrained()->onDelete('cascade');
            $table->foreignId('pressman_id')->constrained('users')->onDelete('cascade'); 
            $table->string('press')->nullable();
            $table->date('production_date');

            // $table->time('production_time')->nullable(); 
            $table->unsignedTinyInteger('production_hour')->default(0);
            $table->unsignedTinyInteger('production_minute')->default(0);
            $table->unsignedTinyInteger('production_second')->default(0);
            $table->decimal('temperature', 6, 2)->nullable();

            $table->decimal('beginning_weight', 8, 2)->default(0);
            $table->decimal('ending_weight', 8, 2)->default(0);
            $table->decimal('raw_material_weight_salang', 8, 2)->default(0);
            $table->decimal('finished_product_weight', 8, 2)->default(0);
            $table->integer('quantity_made')->default(0);
            $table->integer('reject_quantity')->default(0);
            $table->decimal('balance', 8, 2)->default(0);

            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_detail_entries');
    }
};
