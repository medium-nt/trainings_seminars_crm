<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('postal_address')->nullable()->after('phone');
            $table->string('postal_doc_path')->nullable()->after('postal_address');
            $table->string('postal_doc_name')->nullable()->after('postal_doc_path');
            $table->string('tracking_number')->nullable()->after('postal_doc_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['postal_address', 'postal_doc_path', 'postal_doc_name', 'tracking_number']);
        });
    }
};
