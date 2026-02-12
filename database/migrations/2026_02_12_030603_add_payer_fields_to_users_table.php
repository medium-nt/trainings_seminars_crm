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
            $table->string('payer_type', 20)->default('self')->after('is_blocked');
            $table->string('company_card_path')->nullable()->after('payer_type');
            $table->string('company_card_name')->nullable()->after('company_card_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['payer_type', 'company_card_path', 'company_card_name']);
        });
    }
};
