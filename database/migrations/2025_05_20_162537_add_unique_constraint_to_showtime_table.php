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
        Schema::table('showtimes', function (Blueprint $table) {
            $table->dropUnique('showtimes_slug_unique');
            $table->unique(['movie_id', 'show_date', 'time', 'type'], 'unique_showtime_combination');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->dropUnique('unique_showtime_combination');
            $table->unique('slug');
        });
    }
};
