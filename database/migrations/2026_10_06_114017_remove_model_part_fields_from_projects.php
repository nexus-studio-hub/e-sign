<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['model', 'part_number', 'part_name']);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('model')->nullable();
            $table->string('part_number')->nullable();
            $table->string('part_name')->nullable();
        });
    }
};
