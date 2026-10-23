<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // These constraint operations are PostgreSQL-specific. SQLite's test
        // schema is created from scratch and does not need this data migration.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE incidentes DROP CONSTRAINT IF EXISTS incidentes_estado_check");
            DB::statement("ALTER TABLE incidentes DROP CONSTRAINT IF EXISTS incidentes_estado_check1");
            DB::statement("ALTER TABLE incidentes ALTER COLUMN estado SET DEFAULT 'pendiente'");
        }
    }

    public function down(): void
    {
        // No revertir - los datos pueden tener valores no-ENUM
    }
};
