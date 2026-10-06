<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['pic_id']);
            $table->dropColumn(['supplier_id', 'pic_id', 'start_date', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->constrained();
            $table->foreignId('pic_id')->nullable()->constrained('users');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
        });
    }
};
