<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Limpiar registros existentes que no tienen user_id
        DB::table('clientes')->delete();

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropColumn(['telefono', 'email']);

            $table->uuid('user_id')->after('id');

            $table->unique('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            $table->dropColumn(['user_id']);

            $table->string('telefono');
            $table->string('email')->unique();
        });
    }
};
