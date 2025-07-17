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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('telno');
            $table->string('faxno')->nullable();
            $table->string('email');
            $table->string('mobile_no')->nullable();
            $table->string('tin_no')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('website')->nullable();
            $table->enum('status', ['active','inactive'])->default('active');
            $table->string('terms_of_payment')->nullable();
            $table->string('business_type')->nullable(); //['sole_proprietor', 'corporation', 'partnership', etc.])
            $table->string('business_style')->nullable();
            $table->string('customer_category')->nullable(); // ['retail', 'wholesale', 'VIP', etc.])
            $table->longText('remarks')->nullable();
            $table->boolean('tax_exempt')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
