<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name');
        });
        
        // Update existing roles with codes
        DB::table('roles')->where('id', 4)->update(['code' => 'pic_development']);
        DB::table('roles')->where('id', 5)->update(['code' => 'group_leader']);
        DB::table('roles')->where('id', 6)->update(['code' => 'manager']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
