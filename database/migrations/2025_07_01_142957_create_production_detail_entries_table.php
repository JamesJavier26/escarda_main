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
            $table->unsignedTinyInteger('salang_number');
            

            // $table->time('production_time')->nullable(); 
            $table->string('time_per_salang')->nullable();
            $table->string('weight_per_salang')->nullable();
            $table->string('finished_weight_with_overflow')->nullable();
            $table->string('finished_weight_without_overflow')->nullable();
            $table->string('temperature')->nullable();
            $table->boolean('correct_size')->default(false);
            $table->boolean('correct_color')->default(false);
            $table->boolean('correct_hardness')->default(false);
            $table->string('beginning_weight')->default('0')->change();
            $table->string('ending_weight')->default('0')->change();
            $table->string('raw_material_weight_salang')->default('0')->change();
            $table->string('finished_product_weight')->default('0')->change();
            $table->string('quantity_made')->default('0')->change();
            $table->string('reject_quantity')->default('0')->change();
            $table->string('balance')->default('0')->change();

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
