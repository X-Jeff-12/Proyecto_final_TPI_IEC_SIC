<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FinanzasService as F;
use App\Services\ContabilidadService as C;
class InversionController extends Controller {
 public static function calculos(): array {
  $cfg=C::cfg();$costos=C::costos();$map=[];
  foreach($costos['filas'] as $fila)$map[$fila['id']]=$fila;
  $evals=[];$errores=[];
  foreach(DB::table('alternativas')->orderBy('id')->get() as $a){
   $s=DB::table('servicios')->where('id',$a->servicio_id)->first();
   if(!$s){$errores[]="Alternativa #{$a->id}: servicio inexistente";continue;}
   $serv=(array)$s;
   if(isset($map[$s->id]))foreach(['md','mod','cif'] as $k)$serv[$k]=$map[$s->id][$k];
   try{
      $e=F::evaluar((array)$a,$serv,$cfg);
      $e['fuentes']=DB::table('trazas as t')->join('fuentes as f','f.id','=','t.fuente_id')
        ->where('t.entidad','alt')->where('t.ref_id',$a->id)
        ->select('t.campo','t.fuente_id','f.descripcion','f.tipo')->get()->all();
      $evals[]=$e;
   }catch(\Throwable $e){$errores[]="Alternativa #{$a->id}: ".$e->getMessage();}
  }
  try{$comparacion=F::comparar($evals);}catch(\Throwable $e){$comparacion=null;$errores[]=$e->getMessage();}
  return compact('evals','comparacion','cfg','errores');
 }
 public function evaluacion(){return view('inversion.evaluacion',self::calculos());}
 public function comparar(){return view('inversion.comparar',self::calculos());}
 public function verificacion(){
  $a=self::calculos();$manual=DB::table('verificacion_manual')->get()->groupBy('alt_id');$a['manual']=$manual;
  return view('inversion.verificacion',$a);
 }
 public function guardarVerificacion(Request $r){
  $v=$r->validate(['alt_id'=>'required|exists:alternativas,id','vpn'=>'nullable|numeric','tir'=>'nullable|numeric',
    'caue'=>'nullable|numeric','bc'=>'nullable|numeric','nota'=>'nullable|string|max:1500']);
  DB::transaction(function()use($v){
   DB::table('verificacion_manual')->where('alt_id',$v['alt_id'])->delete();
   foreach(['vpn','tir','caue','bc'] as $indicador){if(isset($v[$indicador]))DB::table('verificacion_manual')->insert([
     'alt_id'=>$v['alt_id'],'indicador'=>$indicador,'valor'=>$v[$indicador],'nota'=>$v['nota']??'']);}
   C::log('Control manual registrado',"Alternativa #{$v['alt_id']}");
  });
  return back()->with('ok','Cálculos manuales guardados para comparación.');
 }
 public function valorTiempo(Request $r){
  $v=$r->validate(['capital'=>'nullable|numeric|min:0|max:1000000000','tasa'=>'nullable|numeric|min:0|max:100','periodos'=>'nullable|integer|min:1|max:30','m'=>'nullable|integer|min:1|max:365']);
  $p=(float)($v['capital']??1000);$tasa=(float)($v['tasa']??(C::cfg()['tmar']??12));$n=(int)($v['periodos']??5);$m=(int)($v['m']??1);$i=$tasa/100;
  $resultados=['interes_simple'=>$p*$i*$n,'monto_simple'=>$p*(1+$i*$n),'monto_compuesto'=>$p*pow(1+$i,$n),
     'interes_compuesto'=>$p*(pow(1+$i,$n)-1), 'tasa_efectiva'=>pow(1+$i/$m,$m)-1,'tasa_nominal'=>$m*(pow(1+$i,1/$m)-1)];
  $factores=[];for($k=1;$k<=$n;$k++)$factores[]=['n'=>$k,'pf'=>F::pf($i,$k),'fp'=>F::fp($i,$k),'pa'=>F::pa($i,$k),'ap'=>F::ap($i,$k),'fa'=>F::fa($i,$k),'af'=>F::af($i,$k)];
  return view('inversion.valor-tiempo',compact('p','tasa','n','m','resultados','factores'));
 }
 public function activosDepreciacion(){
  $activos=[];
  foreach(DB::table('activos')->orderBy('id')->get() as $a){
   $tablas=[];
   try{foreach(['lr'=>'Línea recta','sda'=>'Suma de dígitos','ddb'=>'Saldo decreciente doble'] as $k=>$nombre)$tablas[$nombre]=F::cuadro((float)$a->costo,F::depreciacion($k,(float)$a->costo,(float)$a->valor_salvamento,(int)$a->vida_util));}
   catch(\Throwable $e){$tablas=['Error'=>[['anio'=>'—','dep'=>0,'acum'=>0,'libro'=>0]]];}
   $activos[]=['a'=>$a,'tablas'=>$tablas];
  }
  return view('inversion.activos',compact('activos'));
 }
}
