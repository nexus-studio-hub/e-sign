<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing check constraint
        DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
        
        // Add new check constraint with 'reviewer' included
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role::text = ANY (ARRAY['superadmin'::character varying, 'reviewer'::character varying, 'supplier'::character varying]::text[]))");
    }

    public function down(): void
    {
        // Restore original constraint
        DB::statement("ALTER TABLE users DROP CONSTRAINT users_role_check");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role::text = ANY (ARRAY['superadmin'::character varying, 'pic_development'::character varying, 'supplier'::character varying, 'group_leader'::character varying, 'manager'::character varying]::text[]))");
    }
};
