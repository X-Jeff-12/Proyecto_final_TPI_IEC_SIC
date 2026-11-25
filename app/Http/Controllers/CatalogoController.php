<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\ContabilidadService as C;
class CatalogoController extends Controller {
 private function modulo(string $modulo): array { $cfg=config("sic.modulos.{$modulo}"); abort_unless($cfg,404);return $cfg; }
 private function modelo(string $modulo): string {
  return match($modulo){
   'servicios'=>\App\Models\Servicio::class, 'productos'=>\App\Models\Producto::class,
   'activos'=>\App\Models\Activo::class, 'alternativas'=>\App\Models\Alternativa::class,
   'ventas'=>\App\Models\Venta::class, 'pagos_personal'=>\App\Models\PagoPersonal::class,
   'pendientes'=>\App\Models\Pendiente::class, 'mantenimientos'=>\App\Models\Mantenimiento::class,
  };
 }
 private function entidades(): array {
  return ['servicios'=>DB::table('servicios')->orderBy('nombre')->get(), 'activos'=>DB::table('activos')->orderBy('nombre')->get(),
          'cuentas'=>DB::table('cuentas')->orderBy('codigo')->get(),'fuentes'=>DB::table('fuentes')->orderBy('id')->get()];
 }
 public function index(string $modulo,Request $request){
  $cfg=$this->modulo($modulo);$modelo=$this->modelo($modulo);
  $filas=$modelo::query()->orderByDesc('id')->paginate(15)->withQueryString();
  $ed=$request->filled('editar')?$modelo::query()->findOrFail($request->integer('editar')):null;
  $entidades=$this->entidades();$trazas=[];
  if($ed && isset(config('sic.campos_traza')[$modulo])){
   $tipo=$modulo==='alternativas'?'alt':'serv';
   foreach(DB::table('trazas')->where('entidad',$tipo)->where('ref_id',$ed->id)->get() as $r)$trazas[$r->campo][]=$r->fuente_id;
  }
  return view('catalogo.index',compact('modulo','cfg','filas','ed','entidades','trazas'));
 }
 private function recoger(Request $r,string $modulo,array $cfg): array {
  $rules=[];$required=['nombre','fecha','tipo','trabajador','tercero','concepto','servicio_id','vida_util'];
  foreach($cfg['campos'] as $k=>$def){
    $tipo=$def[1];$base=in_array($k,$required,true)?'required':'nullable';
    if($tipo==='number')$base.='|numeric|min:0|max:1000000000';
    elseif($tipo==='integer')$base.='|integer|min:1|max:300';
    elseif($tipo==='boolean')$base.='|boolean';
    elseif($tipo==='date')$base.='|date';
    elseif($tipo==='nivel')$base.='|'.Rule::in(array_keys(config('sic.niveles')));
    elseif($tipo==='servicio')$base.='|integer|exists:servicios,id';
    elseif($tipo==='activo')$base.='|integer|exists:activos,id';
    elseif($tipo==='cuenta')$base.='|string|exists:cuentas,codigo';
    elseif($tipo==='tipo_pendiente')$base.='|'.Rule::in(['cobrar','pagar']);
    else $base.='|string|max:'.($tipo==='textarea'?'5000':'255');
    $rules[$k]=$base;
  }
  foreach($cfg['campos'] as $k=>$def)if($def[1]==='boolean')$r->merge([$k=>$r->input($k,0)]);
  $v=Validator::make($r->all(),$rules)->validate();
  foreach($cfg['campos'] as $k=>$def)if(!array_key_exists($k,$v))$v[$k]=null;
  // Los valores vacíos de numerales opcionales se guardan como 0 y no cambian fórmulas por NULL.
  foreach($cfg['campos'] as $k=>$def)if(in_array($def[1],['number','integer','boolean'],true)&&($v[$k]===null||$v[$k]===''))$v[$k]=0;
  if($modulo==='servicios' && !(int)$v['es_adicional'] && (float)$v['peso']<=0)throw \Illuminate\Validation\ValidationException::withMessages(['peso'=>'El peso de un servicio de lavado debe ser mayor que cero.']);
  if($modulo==='alternativas'){
   if((float)$v['valor_salvamento']>(float)$v['costo_inicial'])throw \Illuminate\Validation\ValidationException::withMessages(['valor_salvamento'=>'No puede superar el costo inicial.']);
   if((float)$v['dep_mercado']>=1)throw \Illuminate\Validation\ValidationException::withMessages(['dep_mercado'=>'La caída debe expresarse en decimal menor que 1 (ej. 0.15).']);
  }
  if($modulo==='activos'&&(float)$v['valor_salvamento']>(float)$v['costo'])throw \Illuminate\Validation\ValidationException::withMessages(['valor_salvamento'=>'El salvamento no puede superar el costo.']);
  if($modulo==='pendientes'&&(float)$v['abonado']>(float)$v['monto'])throw \Illuminate\Validation\ValidationException::withMessages(['abonado'=>'Los abonos no pueden superar el monto.']);
  return $v;
 }
 public function guardar(string $modulo,Request $r){
  $cfg=$this->modulo($modulo);$v=$this->recoger($r,$modulo,$cfg);
  DB::transaction(function()use($r,$modulo,$v){
   $id=$r->filled('id')?$r->integer('id'):null;$modelo=$this->modelo($modulo);
   if($id){$actual=$modelo::query()->findOrFail($id);
      if($modulo==='ventas' && $actual->contabilizado)abort(422,'No se puede editar una venta contabilizada');
      $actual->update($v);
   }else{
       if($modulo==='ventas')$v['contabilizado']=0;
       if($modulo==='servicios')$v+=['md'=>0,'mod'=>0,'cif'=>0,'demo'=>0];
       if($modulo==='alternativas')$v+=['demo'=>0];
       $id=$modelo::query()->create($v)->getKey();
   }
   if(isset(config('sic.campos_traza')[$modulo])&&$r->input('_edit_fuentes')==='1'){
       $tipo=$modulo==='alternativas'?'alt':'serv';
       DB::table('trazas')->where('entidad',$tipo)->where('ref_id',$id)->delete();
       foreach(config("sic.campos_traza.{$modulo}") as $campo){
          $fids=$r->input('f_'.$campo,[]);
          if(!is_array($fids))continue;
          foreach(array_unique($fids) as $fid){
            if(DB::table('fuentes')->where('id',$fid)->exists())DB::table('trazas')->insert(['entidad'=>$tipo,'ref_id'=>$id,'campo'=>$campo,'fuente_id'=>$fid]);
          }
       }
   }
   if($modulo==='activos'){C::regenerarAjusteDepreciacion();C::recalcularCostos();}
   if($modulo==='servicios')C::recalcularCostos();
   C::log(ucfirst($modulo).' guardado',"#{$id}");
  });
  return redirect()->route('catalogo',['modulo'=>$modulo])->with('ok','Registro guardado.');
 }
 public function borrar(string $modulo,int $id){
  $this->modulo($modulo);$modelo=$this->modelo($modulo);$row=$modelo::query()->findOrFail($id);
  $dep=match($modulo){
    'servicios'=>DB::table('alternativas')->where('servicio_id',$id)->exists()||DB::table('ventas')->where('servicio_id',$id)->exists(),
    'activos'=>DB::table('mantenimientos')->where('activo_id',$id)->exists(),
    'ventas'=>(bool)$row->contabilizado,
    'pagos_personal'=>(bool)$row->asiento_id,
    'mantenimientos'=>(bool)$row->asiento_id,
    default=>false
  };
  if($dep)return back()->withErrors(['registro'=>'No se puede borrar: tiene registros asociados o contabilizados.']);
  DB::transaction(function()use($modulo,$id){
    DB::table($modulo)->where('id',$id)->delete();
    if($modulo==='servicios' && !(int)$v['es_adicional'] && (float)$v['peso']<=0)throw \Illuminate\Validation\ValidationException::withMessages(['peso'=>'El peso de un servicio de lavado debe ser mayor que cero.']);
  if($modulo==='alternativas'){
      DB::table('trazas')->where('entidad','alt')->where('ref_id',$id)->delete();
      DB::table('verificacion_manual')->where('alt_id',$id)->delete();
    }elseif($modulo==='servicios')DB::table('trazas')->where('entidad','serv')->where('ref_id',$id)->delete();
    if($modulo==='activos'){C::regenerarAjusteDepreciacion();C::recalcularCostos();}
    if($modulo==='servicios')C::recalcularCostos();
    C::log(ucfirst($modulo).' eliminado',"#{$id}");
  });
  return back()->with('ok','Registro eliminado.');
 }
}
