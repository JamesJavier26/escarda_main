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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('so_no')->unique();
            $table->timestamp('date_ordered');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('pr_no')->nullable();
            $table->string(column: 'attention')->nullable();
            $table->string('terms');
            $table->string('po_no')->nullable();
            $table->foreignId('sr_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('jo_no')->nullable();
            $table->string('tax_status');
            $table->string('approval_status')->nullable(); //Cancel Yes
            $table->timestamp('approval_date')->nullable();
            $table->longText('remarks')->nullable();
            $table->string('so_status')->default(value: 'pending'); 
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->date('delivery_date')->nullable()->change();
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_notified')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
