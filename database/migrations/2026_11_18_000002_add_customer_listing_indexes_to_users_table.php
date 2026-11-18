<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Indexes used by the paginated customer markup listing:
     * role filter + name/email/markup sorting.
     *
     * Short prefixes keep the keys compact (and remain compatible with the
     * old MyISAM 1000-byte limit if this migration is ever replayed on a
     * MyISAM users table). The role values are only a few characters long.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `users` ADD INDEX `users_role_name_index` (`role`(20), `name`(180))');
        DB::statement('ALTER TABLE `users` ADD INDEX `users_role_email_index` (`role`(20), `email`(180))');
        DB::statement('ALTER TABLE `users` ADD INDEX `users_role_markup_index` (`role`(20), `markup`)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `users` DROP INDEX `users_role_name_index`');
        DB::statement('ALTER TABLE `users` DROP INDEX `users_role_email_index`');
        DB::statement('ALTER TABLE `users` DROP INDEX `users_role_markup_index`');
    }
};
