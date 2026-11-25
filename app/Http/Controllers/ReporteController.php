<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ContabilidadService as C;
class ReporteController extends Controller {
 public function index(){
  $cfg=C::cfg();$e=C::estados();$costos=C::costos();$diag=C::conciliacion();
  $inversion=InversionController::calculos();
  return view('reportes',compact('cfg','e','costos','diag','inversion'));
 }
 public function historial(){ $filas=DB::table('bitacora')->orderByDesc('id')->paginate(40);return view('historial',compact('filas')); }
 public function respaldo(){
   $tables=['config','fuentes','trazas','cuentas','asientos','movimientos','servicios','activos','alternativas','verificacion_manual','ventas','pagos_personal','pendientes','mantenimientos','productos','bitacora'];
   $data=[];foreach($tables as $t)$data[$t]=DB::table($t)->get()->all();
   $json=json_encode(['exportado'=>now()->toIso8601String(),'empresa'=>C::cfg()['empresa']??'Car Wash Hernández','tablas'=>$data],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
   return response()->streamDownload(function()use($json){echo $json;},'carwash_backup_'.now()->format('Ymd_His').'.json',['Content-Type'=>'application/json']);
 }
}
