<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\ContabilidadService as C;
class FuenteController extends Controller {
 public function index(){ $fuentes=DB::table('fuentes')->orderByDesc('id')->get();return view('fuentes',compact('fuentes')); }
 public function guardar(Request $r){
  $v=$r->validate(['tipo'=>['required',Rule::in(['cotizacion','factura','estado_financiero','entrevista','foto','observacion'])],
    'descripcion'=>'required|string|max:1500','referencia'=>'required|string|max:500','fecha'=>'nullable|date',
    'anexo'=>'nullable|string|max:100','archivo'=>'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120']);
  $file=$r->file('archivo');$path=$file?$file->store('evidencias','local'):null;
  try{DB::transaction(function()use($v,$path){DB::table('fuentes')->insert(['tipo'=>$v['tipo'],'descripcion'=>$v['descripcion'],'referencia'=>$v['referencia'],
      'fecha'=>$v['fecha']??'', 'anexo'=>$v['anexo']??'', 'demo'=>0,'archivo'=>$path]);C::log('Nueva fuente',$v['descripcion']);});}
  catch(\Throwable $e){if($path)Storage::disk('local')->delete($path);throw $e;}
  return back()->with('ok','Fuente guardada. Relaciónala con los datos clave de cada alternativa o servicio.');
 }
 public function archivo(int $id){$f=DB::table('fuentes')->where('id',$id)->first();abort_unless($f && $f->archivo && Storage::disk('local')->exists($f->archivo),404);return Storage::disk('local')->download($f->archivo);}
 public function borrar(int $id){
  $f=DB::table('fuentes')->where('id',$id)->first();abort_unless($f,404);
  DB::transaction(function()use($id){DB::table('trazas')->where('fuente_id',$id)->delete();DB::table('asientos')->where('fuente_id',$id)->update(['fuente_id'=>null]);DB::table('fuentes')->where('id',$id)->delete();C::log('Fuente eliminada',"#{$id}");});
  if($f->archivo)Storage::disk('local')->delete($f->archivo);
  return back()->with('ok','Fuente eliminada y relaciones actualizadas.');
 }
}
