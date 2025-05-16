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
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->after('email');
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending')->after('price');
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('status');
            $table->dropForeign(['voucher_id']);
            $table->dropColumn('voucher_id');
        });
    }
};