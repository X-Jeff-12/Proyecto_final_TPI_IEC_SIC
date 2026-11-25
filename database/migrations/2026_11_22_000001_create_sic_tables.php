<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
        Schema::create('config', function (Blueprint $t) {
            $t->string('k', 40)->primary();
            $t->string('v', 255)->nullable();
        });
        Schema::create('fuentes', function (Blueprint $t) {
            $t->id();
            $t->string('tipo', 255)->nullable();
            $t->text('descripcion')->nullable();
            $t->text('referencia')->nullable();
            $t->string('fecha', 255)->nullable();
            $t->string('anexo', 255)->nullable();
            $t->integer('demo')->nullable();
        });
        Schema::create('trazas', function (Blueprint $t) {
            $t->id();
            $t->string('entidad', 255)->nullable();
            $t->integer('ref_id')->nullable();
            $t->string('campo', 255)->nullable();
            $t->integer('fuente_id')->nullable();
        });
        Schema::create('cuentas', function (Blueprint $t) {
            $t->string('codigo', 40)->primary();
            $t->string('nombre', 255)->nullable();
            $t->string('tipo', 255)->nullable();
            $t->string('naturaleza', 255)->nullable();
            $t->string('comportamiento', 255)->nullable();
        });
        Schema::create('asientos', function (Blueprint $t) {
            $t->id();
            $t->string('fecha', 255)->nullable();
            $t->text('descripcion')->nullable();
            $t->string('tipo', 255)->nullable();
            $t->integer('fuente_id')->nullable();
            $t->integer('demo')->nullable();
            $t->string('nivel', 255)->nullable();
            $t->text('origen')->nullable();
        });
        Schema::create('movimientos', function (Blueprint $t) {
            $t->id();
            $t->integer('asiento_id')->nullable();
            $t->string('cuenta', 255)->nullable();
            $t->decimal('debe', 16, 4)->nullable();
            $t->decimal('haber', 16, 4)->nullable();
        });
        Schema::create('servicios', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 255)->nullable();
            $t->decimal('precio', 16, 4)->nullable();
            $t->decimal('md', 16, 4)->nullable();
            $t->decimal('mod', 16, 4)->nullable();
            $t->decimal('cif', 16, 4)->nullable();
            $t->decimal('lavados_mes', 16, 4)->nullable();
            $t->integer('demo')->nullable();
            $t->string('nivel', 255)->nullable();
            $t->decimal('peso', 16, 4)->nullable();
            $t->integer('es_adicional')->nullable();
            $t->text('nota')->nullable();
        });
        Schema::create('activos', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 255)->nullable();
            $t->string('fecha_adq', 255)->nullable();
            $t->decimal('costo', 16, 4)->nullable();
            $t->integer('vida_util')->nullable();
            $t->decimal('valor_salvamento', 16, 4)->nullable();
            $t->string('nivel', 255)->nullable();
            $t->text('nota')->nullable();
            $t->string('nivel_vida', 255)->nullable();
            $t->decimal('dep_acum_previa', 16, 4)->nullable();
        });
        Schema::create('alternativas', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 255)->nullable();
            $t->text('descripcion')->nullable();
            $t->decimal('costo_inicial', 16, 4)->nullable();
            $t->integer('vida_util')->nullable();
            $t->decimal('valor_salvamento', 16, 4)->nullable();
            $t->decimal('lavados_mes', 16, 4)->nullable();
            $t->integer('servicio_id')->nullable();
            $t->decimal('energia_anual', 16, 4)->nullable();
            $t->decimal('mantenimiento_anual', 16, 4)->nullable();
            $t->decimal('dep_mercado', 16, 4)->nullable();
            $t->integer('demo')->nullable();
            $t->decimal('ahorro_anual', 16, 4)->nullable();
            $t->string('nivel', 255)->nullable();
            $t->string('marca_modelo', 255)->nullable();
            $t->string('voltaje', 255)->nullable();
            $t->string('presion_psi', 255)->nullable();
            $t->string('garantia', 255)->nullable();
            $t->text('notas')->nullable();
        });
        Schema::create('verificacion_manual', function (Blueprint $t) {
            $t->id();
            $t->integer('alt_id')->nullable();
            $t->string('indicador', 255)->nullable();
            $t->decimal('valor', 16, 4)->nullable();
            $t->text('nota')->nullable();
        });
        Schema::create('ventas', function (Blueprint $t) {
            $t->id();
            $t->string('fecha', 255)->nullable();
            $t->integer('servicio_id')->nullable();
            $t->decimal('cantidad', 16, 4)->nullable();
            $t->decimal('precio_unit', 16, 4)->nullable();
            $t->integer('domicilio')->nullable();
            $t->text('nota')->nullable();
            $t->string('nivel', 255)->nullable();
            $t->integer('contabilizado')->nullable();
        });
        Schema::create('pagos_personal', function (Blueprint $t) {
            $t->id();
            $t->string('fecha', 255)->nullable();
            $t->string('trabajador', 255)->nullable();
            $t->decimal('dias', 16, 4)->nullable();
            $t->decimal('tarifa', 16, 4)->nullable();
            $t->decimal('extra', 16, 4)->nullable();
            $t->text('nota')->nullable();
            $t->string('nivel', 255)->nullable();
            $t->integer('asiento_id')->nullable();
        });
        Schema::create('pendientes', function (Blueprint $t) {
            $t->id();
            $t->string('tipo', 255)->nullable();
            $t->string('tercero', 255)->nullable();
            $t->string('fecha', 255)->nullable();
            $t->text('concepto')->nullable();
            $t->decimal('monto', 16, 4)->nullable();
            $t->decimal('abonado', 16, 4)->nullable();
            $t->string('cuenta_contra', 255)->nullable();
            $t->string('nivel', 255)->nullable();
        });
        Schema::create('mantenimientos', function (Blueprint $t) {
            $t->id();
            $t->string('fecha', 255)->nullable();
            $t->integer('activo_id')->nullable();
            $t->string('tipo', 255)->nullable();
            $t->decimal('costo', 16, 4)->nullable();
            $t->text('descripcion')->nullable();
            $t->string('nivel', 255)->nullable();
            $t->integer('asiento_id')->nullable();
        });
        Schema::create('productos', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 255)->nullable();
            $t->string('categoria', 255)->nullable();
            $t->string('unidad', 255)->nullable();
            $t->decimal('costo', 16, 4)->nullable();
            $t->decimal('frecuencia_dias', 16, 4)->nullable();
            $t->decimal('precio_venta', 16, 4)->nullable();
            $t->decimal('comprado_mes', 16, 4)->nullable();
            $t->decimal('vendido_mes', 16, 4)->nullable();
            $t->string('nivel', 255)->nullable();
            $t->text('nota')->nullable();
        });
        Schema::create('bitacora', function (Blueprint $t) {
            $t->id();
            $t->string('cuando', 255)->nullable();
            $t->string('accion', 255)->nullable();
            $t->text('detalle')->nullable();
        });
   Schema::table('fuentes', fn(Blueprint $t)=>$t->string('archivo')->nullable());
 }
 public function down(): void {
   Schema::dropIfExists('bitacora');
   Schema::dropIfExists('productos');
   Schema::dropIfExists('mantenimientos');
   Schema::dropIfExists('pendientes');
   Schema::dropIfExists('pagos_personal');
   Schema::dropIfExists('ventas');
   Schema::dropIfExists('verificacion_manual');
   Schema::dropIfExists('alternativas');
   Schema::dropIfExists('activos');
   Schema::dropIfExists('servicios');
   Schema::dropIfExists('movimientos');
   Schema::dropIfExists('asientos');
   Schema::dropIfExists('cuentas');
   Schema::dropIfExists('trazas');
   Schema::dropIfExists('fuentes');
   Schema::dropIfExists('config');
 }
};
