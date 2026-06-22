<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $withBiweekly = ['daily', 'weekly', 'biweekly', 'monthly', 'yearly'];

    private array $original = ['daily', 'weekly', 'monthly', 'yearly'];

    public function up(): void
    {
        $this->setAllowedFrequencies($this->withBiweekly);
    }

    public function down(): void
    {
        $this->setAllowedFrequencies($this->original);
    }

    /**
     * Update the allowed values for `recurring_frequency` in a way that works on
     * SQLite (dev/tests), PostgreSQL (production) and MySQL. The column is stored
     * as an enum / check constraint, which each driver alters differently.
     */
    private function setAllowedFrequencies(array $values): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // Drop any existing CHECK constraint(s) on the column (name is
            // auto-generated), then add a fresh one with the new value list.
            $constraints = DB::select(
                "SELECT c.conname FROM pg_constraint c
                 JOIN pg_class t ON c.conrelid = t.oid
                 WHERE t.relname = 'expenses' AND c.contype = 'c'
                   AND pg_get_constraintdef(c.oid) LIKE '%recurring_frequency%'"
            );

            foreach ($constraints as $constraint) {
                DB::statement('ALTER TABLE expenses DROP CONSTRAINT "'.$constraint->conname.'"');
            }

            $list = "'".implode("','", $values)."'";
            DB::statement(
                "ALTER TABLE expenses ADD CONSTRAINT expenses_recurring_frequency_check
                 CHECK (recurring_frequency IS NULL OR recurring_frequency::text IN ($list))"
            );

            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $list = "'".implode("','", $values)."'";
            DB::statement("ALTER TABLE expenses MODIFY recurring_frequency ENUM($list) NULL");

            return;
        }

        // SQLite (and anything else): rebuild the column via the schema builder.
        Schema::table('expenses', function (Blueprint $table) use ($values) {
            $table->enum('recurring_frequency', $values)->nullable()->change();
        });
    }
};
