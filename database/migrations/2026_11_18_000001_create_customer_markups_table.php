<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-customer markup slabs. Each row defines a $/ct range and the
     * markup percentage that applies to both $/ct and total price inside it.
     *
     * A null max_value means "and above" (open-ended final tier).
     *
     * MySQL only stores/enforces FOREIGN KEY constraints on InnoDB tables, and
     * an InnoDB child cannot reference a MyISAM parent. The rest of the project
     * uses MyISAM, so both `users` and this table are switched to InnoDB here
     * to keep the user_id foreign key real (visible in phpMyAdmin).
     */
    public function up(): void
    {
        // The referenced table must be InnoDB before the child can reference it.
        DB::statement('ALTER TABLE `users` ENGINE=InnoDB');

        Schema::create('customer_markups', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('min_value', 12, 2)->default(0);
            $table->decimal('max_value', 12, 2)->nullable();
            $table->decimal('markup', 8, 2)->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // The unique index also serves as the lookup index on user_id.
            $table->unique(['user_id', 'min_value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_markups');

        // Restore the original engine when this migration is rolled back.
        DB::statement('ALTER TABLE `users` ENGINE=MyISAM');
    }
};
