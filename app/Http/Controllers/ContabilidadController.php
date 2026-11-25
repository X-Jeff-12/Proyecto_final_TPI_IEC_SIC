<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\ContabilidadService as C;
class ContabilidadController extends Controller {
 public function diario(){
   $asientos=DB::table('asientos')->orderByDesc('fecha')->orderByDesc('id')->paginate(20);
   $movs=DB::table('movimientos as m')->join('cuentas as c','c.codigo','=','m.cuenta')->select('m.*','c.nombre')
     ->whereIn('m.asiento_id',$asientos->pluck('id')->all())->orderBy('m.id')->get()->groupBy('asiento_id');
   $cuentas=DB::table('cuentas')->orderBy('codigo')->get();$fuentes=DB::table('fuentes')->orderBy('id')->get();
   return view('contabilidad.diario',compact('asientos','movs','cuentas','fuentes'));
 }
 public function guardar(Request $r){
   $v=$r->validate(['fecha'=>'required|date','descripcion'=>'required|string|max:2000','tipo'=>['required',Rule::in(['normal','ajuste','cierre'])],
     'nivel'=>['required',Rule::in(array_keys(config('sic.niveles')))],'fuente_id'=>'nullable|exists:fuentes,id',
     'cuenta'=>'required|array|min:2','cuenta.*'=>'nullable|exists:cuentas,codigo',
     'debe'=>'required|array','debe.*'=>'nullable|numeric|min:0','haber'=>'required|array','haber.*'=>'nullable|numeric|min:0']);
   $lineas=[];
   foreach($v['cuenta'] as $i=>$cta){$d=(float)($v['debe'][$i]??0);$h=(float)($v['haber'][$i]??0);if($cta && ($d>0||$h>0))$lineas[]=['cuenta'=>$cta,'debe'=>$d,'haber'=>$h];}
   C::asiento($v['fecha'],$v['descripcion'],$v['tipo'],$v['nivel'],$v['fuente_id']??null,$lineas);
   C::recalcularCostos();
   return redirect()->route('diario')->with('ok','Asiento de partida doble guardado.');
 }
 public function borrar(int $id){
   $a=DB::table('asientos')->where('id',$id)->first();abort_unless($a,404);
   if($a->origen==='auto_dep')return back()->withErrors(['asiento'=>'El ajuste automático se administra desde Activos.']);
   if(DB::table('ventas')->where('contabilizado',1)->exists() && str_contains((string)$a->origen,'ventas'))return back()->withErrors(['asiento'=>'Asiento asociado con ventas: no se puede borrar sin revertir operaciones.']);
   if(DB::table('mantenimientos')->where('asiento_id',$id)->exists()||DB::table('pagos_personal')->where('asiento_id',$id)->exists())return back()->withErrors(['asiento'=>'Asiento referenciado en operaciones.']);
   C::borrarAsiento($id);C::recalcularCostos();
   return back()->with('ok','Asiento eliminado.');
 }
 public function mayor(){
   $movs=DB::table('movimientos as m')->join('asientos as a','a.id','=','m.asiento_id')
     ->join('cuentas as c','c.codigo','=','m.cuenta')->select('m.*','a.fecha','a.descripcion','a.tipo','c.nombre as nombre_cuenta')
     ->orderBy('m.cuenta')->orderBy('a.fecha')->orderBy('a.id')->get()->groupBy('cuenta');
   $cuentas=DB::table('cuentas')->orderBy('codigo')->get()->keyBy('codigo');
   return view('contabilidad.mayor',compact('movs','cuentas'));
 }
 public function balanza(){ $h=C::hoja();return view('contabilidad.balanza',compact('h')); }
 public function hoja(){ $h=C::hoja();return view('contabilidad.hoja',compact('h')); }
 public function estados(){ $e=C::estados();$no=DB::table('asientos')->where('nivel','!=','confirmado')->count();return view('contabilidad.estados',compact('e','no')); }
 public function caja(){
   $lista=DB::table('movimientos as m')->join('asientos as a','a.id','=','m.asiento_id')
     ->where('m.cuenta','1101')->orderBy('a.fecha')->orderBy('a.id')->select('m.*','a.fecha','a.descripcion','a.nivel')->get();
   $saldo=0;$filas=[];foreach($lista as $r){$saldo+=(float)$r->debe-(float)$r->haber;$filas[]=['mov'=>$r,'saldo'=>$saldo];}
   return view('contabilidad.caja',compact('filas','saldo'));
 }
 public function costos(){
   $c=C::costos();$filas=C::saldos();$costos=array_values(array_filter($filas,fn($r)=>in_array($r['tipo'],['costo','gasto'])));
   return view('costos',compact('c','costos'));
 }
 public function volumen(Request $r){
   $v=$r->validate(['base_volumen'=>['required',Rule::in(['promedio','conteo'])], 'dias_trabajo'=>'required|numeric|min:0|max:31','veh_dia_promedio'=>'required|numeric|min:0|max:1000']);
   DB::transaction(function()use($v){foreach($v as $k=>$valor)DB::table('config')->updateOrInsert(['k'=>$k],['v'=>(string)$valor]);C::recalcularCostos();C::log('Cambio de volumen de costeo',json_encode($v));});
   return back()->with('ok','Parámetros del costeo actualizados.');
 }
}
