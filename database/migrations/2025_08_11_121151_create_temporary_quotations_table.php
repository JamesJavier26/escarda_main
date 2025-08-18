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
        Schema::create('temporary_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sr_id')->nullable()->constrained('users')->onDelete('set null'); //sales rep
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('id');
            $table->string('attention');
            $table->string('terms');
            $table->string('pr_no');
            $table->string('delivery_date')->nullable();
            $table->longText('remarks')->nullable();
            $table->string('vat');
            $table->longText('pe_remarks')->nullable();
            $table->longText('sr_remarks')->nullable();
            $table->string('currency_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_quotations');
    }
};
