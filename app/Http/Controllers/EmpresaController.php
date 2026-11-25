<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\ContabilidadService as C;
class EmpresaController extends Controller {
 public function index(){ $cfg=C::cfg();$fuentes=DB::table('fuentes')->orderBy('id')->get();$trazas=[];
  foreach(DB::table('trazas')->where('entidad','cfg')->where('ref_id',0)->get() as $t)$trazas[$t->campo][]=$t->fuente_id;
  return view('empresa',compact('cfg','fuentes','trazas'));
 }
 public function guardar(Request $r){
   $r->validate(['empresa'=>'required|string|max:150','propietario'=>'nullable|string|max:150','giro'=>'nullable|string|max:255',
      'decision'=>'nullable|string|max:500','alternativas_txt'=>'nullable|string|max:800','periodo'=>'nullable|string|max:100',
      'periodo_inicio'=>'required|date','periodo_fin'=>'required|date|after_or_equal:periodo_inicio',
      'tmar'=>'required|numeric|min:0|max:100','isr'=>'required|numeric|min:0|max:100',
      'metodo_dep'=>['required',Rule::in(['lr','sda','ddb'])],
      'carta_firmada'=>'required|in:0,1','ingreso_bruto_estimado'=>'nullable|numeric|min:0','ingreso_mes_normal'=>'nullable|numeric|min:0',
      'ingreso_incluye_aromas'=>'nullable|in:si,no']);
   $permitidos=['empresa','propietario','giro','decision','alternativas_txt','integrantes_contables','integrantes_sistemas','resp_contable','resp_economico','resp_app','contacto','periodo','periodo_inicio','periodo_fin','tmar','isr','metodo_dep','carta_firmada','ingreso_bruto_estimado','ingreso_mes_normal','ingreso_incluye_aromas'];
   DB::transaction(function()use($r,$permitidos){
      foreach($permitidos as $k){if($r->exists($k))DB::table('config')->updateOrInsert(['k'=>$k],['v'=>(string)$r->input($k)]);}
      foreach(['tmar','isr'] as $campo){
         DB::table('trazas')->where('entidad','cfg')->where('ref_id',0)->where('campo',$campo)->delete();
         foreach(array_unique($r->input('f_'.$campo,[])) as $fid)if(DB::table('fuentes')->where('id',$fid)->exists())DB::table('trazas')->insert(['entidad'=>'cfg','ref_id'=>0,'campo'=>$campo,'fuente_id'=>$fid]);
      }
      C::log('Empresa y parámetros actualizados','El usuario modificó parámetros de la evaluación');
   });
   return back()->with('ok','Datos guardados. Comprueba nuevamente los cálculos y las fuentes.');
 }
}
