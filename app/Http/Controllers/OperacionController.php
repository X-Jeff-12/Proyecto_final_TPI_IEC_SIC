<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ContabilidadService as C;
class OperacionController extends Controller {
 /** Atención: verificar antes que el asiento estimado de apertura no duplique estas ventas. */
 public function contabilizarVentas(Request $r){
  $r->validate(['fecha'=>'required|date','confirmar'=>'accepted']);
  $pendientes=DB::table('ventas')->where('contabilizado',0)->get();
  if(!$pendientes->count())return back()->withErrors(['ventas'=>'No hay ventas pendientes.']);
  $totales=['4101'=>0,'4102'=>0];$total=0;
  foreach($pendientes as $v){
    $serv=DB::table('servicios')->where('id',$v->servicio_id)->first();
    if(!$serv)return back()->withErrors(['ventas'=>"La venta #{$v->id} no tiene servicio vigente."]);
    $monto=round((float)$v->cantidad*(float)$v->precio_unit,2);
    if($monto<=0)return back()->withErrors(['ventas'=>"La venta #{$v->id} tiene monto no positivo."]);
    $cta=$serv->es_adicional?'4102':'4101';$totales[$cta]+=$monto;$total+=$monto;
  }
  $total=round($total,2);$lineas=[['cuenta'=>'1101','debe'=>$total,'haber'=>0]];
  foreach($totales as $cuenta=>$monto)if($monto>0)$lineas[]=['cuenta'=>$cuenta,'debe'=>0,'haber'=>round($monto,2)];
  $nivel=$pendientes->every(fn($v)=>$v->nivel==='confirmado')?'confirmado':'estimado';
  DB::transaction(function()use($r,$lineas,$nivel,$pendientes){
     $id=C::asiento($r->input('fecha'),'Ventas individuales contabilizadas (lote de '.$pendientes->count().')','normal',$nivel,null,$lineas,'ventas');
     DB::table('ventas')->whereIn('id',$pendientes->pluck('id'))->update(['contabilizado'=>1]);
     C::log('Ventas contabilizadas',"Lote asiento #{$id}");
  });
  C::recalcularCostos();
  return back()->with('ok','Ventas contabilizadas. Revisa que no dupliquen los ingresos reconstruidos del periodo.');
 }
}
