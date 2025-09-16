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

            // Relationships
            $table->foreignId('production_detail_report_id')->constrained()->onDelete('cascade');
            $table->foreignId('pressman_id')->constrained('users')->onDelete('cascade'); 

            // Core fields
            $table->date('production_date');
            $table->integer('qty_to_be_produced');   // for calculations
            $table->foreignId('unit_id')->nullable()->constrained('units')->after('qty_to_be_produced');
            $table->integer('daily_output');         // for calculations
            $table->integer('reject_quantity')->default(0); 
            $table->integer('day_no')->nullable();

            // Machine & process fields
            $table->string('press_no')->nullable();
            $table->string('temp_setting')->nullable();
            $table->time('start_time')->nullable();
            $table->string('process_duration')->nullable();
            $table->string('insert_details')->nullable();

            // Material tracking
            $table->string('beginning_weight_rawmat')->nullable();
            $table->string('ending_weight_rawmat')->nullable();
            $table->string('rawmat_size_salang')->nullable();
            $table->string('rawmat_weight_salang')->nullable();
            $table->string('overflow_weight_salang')->nullable();
            $table->string('actual_finish_product_weight')->nullable();

            // Computed values
            $table->integer('remaining_balance')->default(0);

            // Notes
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
