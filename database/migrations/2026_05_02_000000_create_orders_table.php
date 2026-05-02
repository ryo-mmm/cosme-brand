<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('stripe_charge_id')->unique();
            $table->string('stripe_invoice_id')->nullable()->index();
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('jpy');
            $table->string('status', 30)->default('succeeded');
            $table->string('description')->nullable();
            $table->string('payment_method_type', 30)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
