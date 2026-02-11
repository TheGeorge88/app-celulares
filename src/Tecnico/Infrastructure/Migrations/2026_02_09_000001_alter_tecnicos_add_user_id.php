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
        DB::table('tecnicos')->delete();

        Schema::table('tecnicos', function (Blueprint $table) {
            $table->dropUnique(['cedula']);
            $table->dropUnique(['email']);
            $table->dropColumn(['cedula', 'nombre', 'apellido', 'telefono', 'email']);

            $table->uuid('user_id')->after('id');
            $table->string('certificacion', 100)->nullable()->after('especialidad');
            $table->date('fecha_contratacion')->nullable()->after('certificacion');

            $table->unique('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('tecnicos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            $table->dropColumn(['user_id', 'certificacion', 'fecha_contratacion']);

            $table->string('cedula', 20)->unique();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('telefono', 20);
            $table->string('email', 150)->unique();
        });
    }
};
