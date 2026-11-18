<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dashboard featured ads are stored in the database instead of browser
     * storage. The parent diamonds table is switched to InnoDB so the
     * diamond_id foreign key is real (MySQL ignores FKs on MyISAM).
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `diamonds` ENGINE=InnoDB');

        Schema::create('featured_diamond_ads', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->unsignedBigInteger('diamond_id');
            $table->unsignedTinyInteger('position')->default(0);
            $table->timestamps();

            $table->foreign('diamond_id')->references('id')->on('diamonds')->onDelete('cascade');
            $table->unique('diamond_id');
            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_diamond_ads');

        DB::statement('ALTER TABLE `diamonds` ENGINE=MyISAM');
    }
};
