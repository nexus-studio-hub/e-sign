<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('barcode_serial', 10)->unique()->nullable()->after('status');
            $table->timestamp('barcode_stamped_at')->nullable()->after('barcode_serial');
            $table->string('stamped_file_path')->nullable()->after('barcode_stamped_at');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['barcode_serial', 'barcode_stamped_at', 'stamped_file_path']);
        });
    }
};
