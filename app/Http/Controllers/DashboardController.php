<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Services\ContabilidadService as C;
class DashboardController extends Controller {
 public function index(){
  $est=C::estados();$c=C::conciliacion();$nivel=[];
  foreach(['asientos','servicios','activos','alternativas'] as $table){foreach(DB::table($table)->selectRaw('nivel, COUNT(*) AS n')->groupBy('nivel')->get() as $r){$nivel[$r->nivel]=($nivel[$r->nivel]??0)+$r->n;}}
  return view('dashboard',compact('est','c','nivel'));
 }
 public function diagnostico(){
  $c=C::conciliacion();$nivel=[];
  foreach(['asientos','servicios','activos','alternativas'] as $table){foreach(DB::table($table)->selectRaw('nivel, COUNT(*) AS n')->groupBy('nivel')->get() as $r){$nivel[$r->nivel]=($nivel[$r->nivel]??0)+$r->n;}}
  $faltantes=[];$total=0;
  foreach(DB::table('alternativas')->get() as $a){foreach(config('sic.campos_traza.alternativas') as $campo){$total++;$n=DB::table('trazas')->where('entidad','alt')->where('ref_id',$a->id)->where('campo',$campo)->distinct()->count('fuente_id');if($n<2)$faltantes[]="{$a->nombre} · {$campo} ({$n}/2)";}}
  foreach(DB::table('servicios')->where('es_adicional',0)->get() as $s){$total++;$n=DB::table('trazas')->where('entidad','serv')->where('ref_id',$s->id)->where('campo','precio')->distinct()->count('fuente_id');if($n<2)$faltantes[]="{$s->nombre} · precio ({$n}/2)";}
  foreach(['tmar','isr'] as $campo){$total++;$n=DB::table('trazas')->where('entidad','cfg')->where('ref_id',0)->where('campo',$campo)->distinct()->count('fuente_id');if($n<2)$faltantes[]="Parámetros · {$campo} ({$n}/2)";}
  return view('diagnostico',compact('c','nivel','faltantes','total'));
 }
}
