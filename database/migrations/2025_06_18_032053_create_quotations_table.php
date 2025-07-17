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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quote_no')->unique();
            $table->integer('version')->default(0); // 0 = original
            $table->foreignId('parent_id')->nullable()->constrained('quotations')->nullOnDelete(); 
            $table->foreignId('pricing_estimate_id')->nullable()->constrained('pricing_estimates')->cascadeOnDelete();
            $table->timestamp('quote_date');
            $table->foreignId(column: 'currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->string( 'quote_status');
            $table->text('revision_reason')->nullable();
            $table->string('rejected_pdf_path')->nullable();
            $table->boolean('is_final')->default(false);
            $table->longText('quote_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
