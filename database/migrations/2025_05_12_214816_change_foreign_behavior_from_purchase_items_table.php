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
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropForeign(['showing_seat_id']);
            $table->foreignId('showing_seat_id')->nullable()->change();
            $table->foreign('showing_seat_id')->references('id')->on('showing_seats')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropForeign(['showing_seat_id']);
            $table->foreignId('showing_seat_id')->nullable(false)->change();
            $table->foreign('showing_seat_id')->references('id')->on('showing_seats')->onDelete('cascade');
        });
    }
};
