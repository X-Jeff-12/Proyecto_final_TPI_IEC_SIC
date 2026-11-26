<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activos', function (Blueprint $table) {
            $table->decimal('valor_mercado', 14, 2)->nullable()->after('valor_salvamento');
            $table->integer('vida_restante')->nullable()->after('valor_mercado');
            $table->decimal('cao_anual', 14, 2)->default(0)->after('vida_restante');
        });
    }

    public function down(): void
    {
        Schema::table('activos', function (Blueprint $table) {
            $table->dropColumn(['valor_mercado', 'vida_restante', 'cao_anual']);
        });
    }
};
