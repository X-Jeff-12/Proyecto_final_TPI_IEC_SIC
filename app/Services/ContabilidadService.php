<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
class ContabilidadService
{
 public static function cfg(): array {
   return DB::table('config')->pluck('v','k')->all();
 }
 public static function log(string $accion,string $detalle=''): void {
   DB::table('bitacora')->insert(['cuando'=>now()->toDateTimeString(),'accion'=>$accion,'detalle'=>$detalle]);
 }
 /** Asientos no negativos, con al menos 2 líneas, sin debe y haber simultáneos por línea. */
 public static function asiento(string $fecha,string $descripcion,string $tipo,string $nivel,?int $fuenteId,array $lineas,?string $origen=null): int {
   if(count($lineas)<2) throw ValidationException::withMessages(['lineas'=>'El asiento necesita al menos dos movimientos.']);
   $td=0;$th=0;
   foreach($lineas as $l){
     if(!DB::table('cuentas')->where('codigo',$l['cuenta'])->exists())throw ValidationException::withMessages(['lineas'=>'Cuenta no encontrada: '.$l['cuenta']]);
     $d=(float)$l['debe'];$h=(float)$l['haber'];
     if($d<0||$h<0||($d>0&&$h>0)||($d==0&&$h==0))throw ValidationException::withMessages(['lineas'=>'Cada movimiento requiere un importe positivo en debe o haber, no ambos.']);
     $td+=round($d,2);$th+=round($h,2);
   }
   if(round($td,2)!=round($th,2))throw ValidationException::withMessages(['lineas'=>'Partida doble no cuadra: debe $'.number_format($td,2).' / haber $'.number_format($th,2)]);
   return DB::transaction(function()use($fecha,$descripcion,$tipo,$nivel,$fuenteId,$lineas,$origen){
     $id=DB::table('asientos')->insertGetId(['fecha'=>$fecha,'descripcion'=>$descripcion,'tipo'=>$tipo,'nivel'=>$nivel,'fuente_id'=>$fuenteId,'origen'=>$origen,'demo'=>0]);
     foreach($lineas as $l){DB::table('movimientos')->insert(['asiento_id'=>$id,'cuenta'=>$l['cuenta'],'debe'=>round((float)$l['debe'],2),'haber'=>round((float)$l['haber'],2)]);}
     self::log('Asiento creado',"#{$id} {$descripcion}");
     return $id;
   });
 }
 public static function borrarAsiento(int $id): void {
   DB::transaction(function() use($id){
     DB::table('movimientos')->where('asiento_id',$id)->delete();
     DB::table('asientos')->where('id',$id)->delete();
     self::log('Asiento eliminado',"#{$id}");
   });
 }
 public static function saldos(array $tipos=['normal','ajuste','cierre']): array {
   $cuentas=DB::table('cuentas')->orderBy('codigo')->get();
   $movs=DB::table('movimientos as m')->join('asientos as a','a.id','=','m.asiento_id')
      ->whereIn('a.tipo',$tipos)->select('m.cuenta','m.debe','m.haber')->get();
   $sumas=[];
   foreach($movs as $m){$k=$m->cuenta;$sumas[$k]['debe']=($sumas[$k]['debe']??0)+(float)$m->debe;$sumas[$k]['haber']=($sumas[$k]['haber']??0)+(float)$m->haber;}
   return $cuentas->map(function($c)use($sumas){$r=(array)$c;$r['debe']=$sumas[$c->codigo]['debe']??0;$r['haber']=$sumas[$c->codigo]['haber']??0;return $r;})->all();
 }
 public static function saldoDeudor(string $codigo): float {
   foreach(self::saldos() as $r)if($r['codigo']===$codigo)return $r['debe']-$r['haber'];return 0;
 }
 public static function hoja(): array {
   $sin=array_column(self::saldos(['normal']),null,'codigo');
   $aju=array_column(self::saldos(['ajuste']),null,'codigo');
   $keys=['bc_d','bc_h','aj_d','aj_h','ba_d','ba_h','er_d','er_h','bg_d','bg_h'];
   $tot=array_fill_keys($keys,0.0);$filas=[];
   foreach($sin as $cod=>$r){
     $a=$aju[$cod]??['debe'=>0,'haber'=>0];$n1=$r['debe']-$r['haber'];$n2=$n1+$a['debe']-$a['haber'];
     $f=['codigo'=>$cod,'nombre'=>$r['nombre'],'tipo'=>$r['tipo'],'bc_d'=>max($n1,0),'bc_h'=>max(-$n1,0),
         'aj_d'=>$a['debe'],'aj_h'=>$a['haber'],'ba_d'=>max($n2,0),'ba_h'=>max(-$n2,0),
         'er_d'=>0,'er_h'=>0,'bg_d'=>0,'bg_h'=>0];
     $dest=in_array($r['tipo'],['ingreso','costo','gasto'],true)?'er':'bg';
     $f[$dest.'_d']=$f['ba_d'];$f[$dest.'_h']=$f['ba_h'];
     if(abs($n1)+abs($a['debe'])+abs($a['haber'])+abs($n2)>1e-9){$filas[]=$f;foreach($keys as $k)$tot[$k]+=$f[$k];}
   }
   $u=$tot['er_h']-$tot['er_d'];
   return ['filas'=>$filas,'tot'=>$tot,'utilidad'=>$u,'cierre'=>['er_d'=>max($u,0),'er_h'=>max(-$u,0),'bg_d'=>max(-$u,0),'bg_h'=>max($u,0)]];
 }
 public static function estados(): array {
   $h=self::hoja();$groups=['ingresos'=>[],'costos'=>[],'gastos'=>[],'activos'=>[],'pasivos'=>[],'patrimonio'=>[]];
   foreach($h['filas'] as $f){
     $s=$f['ba_d']-$f['ba_h'];$t=$f['tipo'];$campo=match($t){'ingreso'=>'ingresos','costo'=>'costos','gasto'=>'gastos','activo'=>'activos','pasivo'=>'pasivos','patrimonio'=>'patrimonio',default=>null};
     if(!$campo)continue;
     $v=in_array($t,['ingreso','pasivo','patrimonio'])?-$s:$s;$groups[$campo][]=['nombre'=>$f['nombre'],'valor'=>$v];
   }
   $t=[];foreach($groups as $k=>$rows)$t[$k]=array_sum(array_column($rows,'valor'));
   return $groups+['totales'=>$t,'utilidad_bruta'=>$t['ingresos']-$t['costos'],'utilidad'=>$t['ingresos']-$t['costos']-$t['gastos'],
       'cuadre'=>$t['activos']-($t['pasivos']+$t['patrimonio']+$t['ingresos']-$t['costos']-$t['gastos'])];
 }
 public static function costos(): array {
   $mov=['md'=>self::saldoDeudor('5101'),'mod'=>self::saldoDeudor('5102'),
         'cif'=>self::saldoDeudor('5103')+self::saldoDeudor('5104')+self::saldoDeudor('5105')];
   $serv=DB::table('servicios')->where('es_adicional',0)->orderBy('id')->get();
   $suma=$serv->sum('lavados_mes');$cfg=self::cfg();$base=($cfg['base_volumen']??'promedio')==='conteo'?$suma:(float)($cfg['dias_trabajo']??0)*(float)($cfg['veh_dia_promedio']??0);
   $escala=$suma>0?$base/$suma:0;
   $eq=$serv->sum(fn($s)=>(float)$s->lavados_mes*$escala*(float)$s->peso);
   $rows=[];
   foreach($serv as $s){
     $p=(float)$s->peso;
     $md=$eq>0?$mov['md']/$eq*$p:0;$mod=$eq>0?$mov['mod']/$eq*$p:0;$cif=$eq>0?$mov['cif']/$eq*$p:0;
     $rows[]=['id'=>$s->id,'nombre'=>$s->nombre,'precio'=>(float)$s->precio,'cantidad'=>(float)$s->lavados_mes,
       'peso'=>$p,'md'=>$md,'mod'=>$mod,'cif'=>$cif,'costo'=>$md+$mod+$cif,'margen'=>(float)$s->precio-($md+$mod+$cif)];
   }
   return ['filas'=>$rows,'totales'=>$mov,'base'=>$base,'equivalentes'=>$eq,'escala'=>$escala];
 }
 public static function recalcularCostos(): void {
   $c=self::costos();foreach($c['filas'] as $r){DB::table('servicios')->where('id',$r['id'])->update(['md'=>round($r['md'],4),'mod'=>round($r['mod'],4),'cif'=>round($r['cif'],4)]);}
 }
 public static function regenerarAjusteDepreciacion(): float {
   DB::transaction(function(){foreach(DB::table('asientos')->where('origen','auto_dep')->pluck('id') as $id)self::borrarAsiento((int)$id);});
   $total=0;
   foreach(DB::table('activos')->get() as $a){$meses=(int)$a->vida_util*12;if($meses>0)$total+=round(((float)$a->costo-(float)$a->valor_salvamento)/$meses,2);}
   $total=round($total,2);
   if($total>0)self::asiento(self::cfg()['periodo_fin']??now()->toDateString(),
       'Ajuste automático de depreciación mensual (vida útil por verificar)','ajuste','supuesto',null,
       [['cuenta'=>'5105','debe'=>$total,'haber'=>0],['cuenta'=>'1202','debe'=>0,'haber'=>$total]],'auto_dep');
   return $total;
 }
 public static function conciliacion(): array {
   $c=self::cfg();$serv=DB::table('servicios')->get();
   $veh=$serv->where('es_adicional',0);$ad=$serv->where('es_adicional',1);
   $q=$veh->sum('lavados_mes');$ingVeh=$veh->sum(fn($s)=>(float)$s->lavados_mes*(float)$s->precio);
   $ingAd=$ad->sum(fn($s)=>(float)$s->lavados_mes*(float)$s->precio);
   $aroma=DB::table('productos')->where('categoria','reventa')->get()->sum(fn($p)=>(float)$p->vendido_mes*(float)$p->precio_venta);
   $conteo=$ingVeh+$ingAd+$aroma;$dias=(float)($c['dias_trabajo']??0);$prom=(float)($c['veh_dia_promedio']??0);
   $porProm=$dias*$prom*($q>0?$ingVeh/$q:0)+$ingAd+$aroma;
   $trabajo=(float)($c['ingreso_bruto_estimado']??0);
   return compact('q','ingVeh','ingAd','aroma','conteo','porProm','trabajo','dias','prom')+
      ['diferencia_conteo'=>$trabajo-$conteo,'diferencia_promedio'=>$trabajo-$porProm];
 }
}
