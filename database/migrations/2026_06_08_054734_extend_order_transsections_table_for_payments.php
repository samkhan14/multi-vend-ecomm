<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite' && ! Schema::hasColumn('order_transsections', 'id')) {
            Schema::dropIfExists('order_transsections');

            Schema::create('order_transsections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->string('gateway')->nullable();
                $table->string('transaction_id')->nullable();
                $table->string('gateway_payment_id')->nullable()->index();
                $table->string('invoice_id')->nullable();
                $table->decimal('amount', 10, 2);
                $table->decimal('gateway_price_amount', 12, 2)->nullable();
                $table->string('gateway_price_currency', 10)->nullable();
                $table->string('currency')->default('PKR');
                $table->string('payment_method');
                $table->string('payment_status')->default('pending');
                $table->json('transaction_response')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('order_transsections', function (Blueprint $table) {
            if (! Schema::hasColumn('order_transsections', 'id')) {
                $table->id()->first();
            }
        });

        Schema::table('order_transsections', function (Blueprint $table) {
            if (! Schema::hasColumn('order_transsections', 'gateway')) {
                $table->string('gateway')->nullable()->after('order_id');
            }
            if (! Schema::hasColumn('order_transsections', 'gateway_payment_id')) {
                $table->string('gateway_payment_id')->nullable()->index()->after('transaction_id');
            }
            if (! Schema::hasColumn('order_transsections', 'invoice_id')) {
                $table->string('invoice_id')->nullable()->after('gateway_payment_id');
            }
            if (! Schema::hasColumn('order_transsections', 'gateway_price_amount')) {
                $table->decimal('gateway_price_amount', 12, 2)->nullable()->after('amount');
            }
            if (! Schema::hasColumn('order_transsections', 'gateway_price_currency')) {
                $table->string('gateway_price_currency', 10)->nullable()->after('gateway_price_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_transsections', function (Blueprint $table) {
            $columns = [
                'gateway',
                'gateway_payment_id',
                'invoice_id',
                'gateway_price_amount',
                'gateway_price_currency',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('order_transsections', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
