@extends('layouts.app')
@section('content')
<h1>Empresa y parámetros del estudio</h1><p class="lead">La configuración se conserva desde el proyecto Flask. Cambiar parámetros afectará todos los resultados de inversión.</p>
<form method="POST" action="{{ route('empresa.guardar') }}" class="card">@csrf
@php($campos=['empresa'=>'Empresa','propietario'=>'Propietario','giro'=>'Giro','decision'=>'Decisión de inversión','alternativas_txt'=>'Alternativas','contacto'=>'Contacto','integrantes_contables'=>'Integrantes Contables','integrantes_sistemas'=>'Integrantes Ingeniería/Sistemas','resp_contable'=>'Responsable contable','resp_economico'=>'Responsable económico','resp_app'=>'Responsable aplicación','periodo'=>'Periodo evaluado'])
<div class="f">@foreach($campos as $campo=>$label)<label class="{{ in_array($campo,['decision','alternativas_txt'])?'full':'' }}">{{ $label }}<input name="{{ $campo }}" value="{{ old($campo,$cfg[$campo]??'') }}" @if($campo==='empresa') required @endif></label>@endforeach
<label>Inicio del periodo<input type="date" name="periodo_inicio" value="{{ old('periodo_inicio',$cfg['periodo_inicio']??'') }}" required></label>
<label>Fin del periodo<input type="date" name="periodo_fin" value="{{ old('periodo_fin',$cfg['periodo_fin']??'') }}" required></label>
<label>TMAR (%) <input type="number" name="tmar" min="0" max="100" step="0.0001" value="{{ old('tmar',$cfg['tmar']??12) }}" required></label>
<label>ISR (%) <input type="number" name="isr" min="0" max="100" step="0.0001" value="{{ old('isr',$cfg['isr']??30) }}" required></label>
<label>Método depreciación<select name="metodo_dep">@foreach(['lr'=>'Línea recta','sda'=>'Suma de dígitos','ddb'=>'Saldo decreciente doble'] as $k=>$v)<option value="{{ $k }}" @selected(old('metodo_dep',$cfg['metodo_dep']??'lr')===$k)>{{ $v }}</option>@endforeach</select></label>
<label>Carta firmada<select name="carta_firmada"><option value="0" @selected(($cfg['carta_firmada']??'0')==='0')>Pendiente</option><option value="1" @selected(($cfg['carta_firmada']??'0')==='1')>Sí (comprobada)</option></select></label>
<label>Ingresos estimados del período ($)<input type="number" name="ingreso_bruto_estimado" step="0.01" min="0" value="{{ old('ingreso_bruto_estimado',$cfg['ingreso_bruto_estimado']??0) }}"></label>
<label>Ingresos de un mes normal ($)<input type="number" name="ingreso_mes_normal" step="0.01" min="0" value="{{ old('ingreso_mes_normal',$cfg['ingreso_mes_normal']??0) }}"></label>
<label>¿Ingreso estimado incluye aromatizantes?<select name="ingreso_incluye_aromas"><option value="si" @selected(($cfg['ingreso_incluye_aromas']??'si')==='si')>Sí</option><option value="no" @selected(($cfg['ingreso_incluye_aromas']??'si')==='no')>No</option></select></label>
</div>
<h2>Trazabilidad de parámetros</h2><div class="f">@foreach(['tmar'=>'TMAR','isr'=>'ISR'] as $k=>$nombre)<label>{{ $nombre }} — fuentes
<select name="f_{{ $k }}[]" multiple>@foreach($fuentes as $f)<option value="{{ $f->id }}" @selected(in_array($f->id,$trazas[$k]??[]))>#{{ $f->id }} · {{ $f->tipo }} · {{ Str::limit($f->descripcion,45) }}</option>@endforeach</select></label>@endforeach</div>
<button class="btn">Guardar configuración</button></form>
<div class="noticia"><b>Nota académica:</b> TMAR e ISR del proyecto original son supuestos. Verificar aplicabilidad tributaria al negocio informal antes de emitir recomendaciones definitivas.</div>
@endsection
