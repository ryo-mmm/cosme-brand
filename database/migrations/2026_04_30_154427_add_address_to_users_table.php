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
            $table->string('postal_code', 20)->nullable()->after('remember_token');
            $table->string('address', 255)->nullable()->after('postal_code');
            $table->string('address_line2', 255)->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['postal_code', 'address', 'address_line2']);
        });
    }
};
