@extends('layouts.app')
@section('content')
<h1>{{ $cfg['titulo'] }}</h1><p class="lead">{{ $cfg['descripcion'] }}</p>
@if($modulo==='ventas' && $filas->contains('contabilizado',0))
<div class="noticia"><strong>Precaución:</strong> si las ventas reconstruidas del período ya figuran como asiento estimado, contabilizar aquí nuevamente duplicaría ingresos. Haz primero la conciliación.</div>
<form method="POST" action="{{ route('ventas.contabilizar') }}" class="card">@csrf
 <div class="f"><label>Fecha del asiento <input type="date" name="fecha" value="{{ now()->toDateString() }}" required></label>
 <label><input type="checkbox" name="confirmar" value="1" required> He revisado que estas ventas no están duplicadas en el libro diario</label></div>
 <button class="btn" type="submit">Contabilizar las ventas pendientes</button>
</form>
@endif
<div class="card">
<h2>{{ $ed?'Editar registro #'.$ed->id:'Agregar registro' }}</h2>
<form method="POST" action="{{ route('catalogo.guardar',['modulo'=>$modulo]) }}">@csrf
<input type="hidden" name="id" value="{{ $ed->id??'' }}">
<div class="f">
@foreach($cfg['campos'] as $campo=>$def)
@php($tipo=$def[1])
<label class="{{ $tipo==='textarea'?'full':'' }}">{{ $def[0] }}
@if($tipo==='textarea')
<textarea name="{{ $campo }}" rows="2">{{ old($campo,$ed->$campo??'') }}</textarea>
@elseif($tipo==='boolean')
<select name="{{ $campo }}"><option value="0" @selected((string)old($campo,$ed->$campo??'0')==='0')>No</option><option value="1" @selected((string)old($campo,$ed->$campo??'0')==='1')>Sí</option></select>
@elseif($tipo==='nivel')
<select name="{{ $campo }}">@foreach(config('sic.niveles') as $valor=>$texto)<option value="{{ $valor }}" @selected(old($campo,$ed->$campo??($modulo==='alternativas'?'supuesto':'estimado'))===$valor)>{{ $texto }}</option>@endforeach</select>
@elseif($tipo==='tipo_pendiente')
<select name="{{ $campo }}"><option value="cobrar" @selected(old($campo,$ed->$campo??'cobrar')==='cobrar')>Por cobrar</option><option value="pagar" @selected(old($campo,$ed->$campo??'cobrar')==='pagar')>Por pagar</option></select>
@elseif(in_array($tipo,['servicio','activo','cuenta']))
@php($opciones=$entidades[$tipo==='servicio'?'servicios':($tipo==='activo'?'activos':'cuentas')])
<select name="{{ $campo }}"><option value="">Seleccionar...</option>@foreach($opciones as $op)
@php($opValor=$tipo==='cuenta'?$op->codigo:$op->id)
<option value="{{ $opValor }}" @selected((string)old($campo,$ed->$campo??'')===(string)$opValor)>{{ $tipo==='cuenta'?$op->codigo.' · '.$op->nombre:$op->nombre }}</option>
@endforeach</select>
@else
<input type="{{ $tipo==='number'||$tipo==='integer'?'number':($tipo==='date'?'date':'text') }}" name="{{ $campo }}" value="{{ old($campo,$ed->$campo??'') }}" @if($tipo==='number') step="0.0001" @elseif($tipo==='integer') step="1" @endif>
@endif
</label>
@endforeach
</div>
@if(isset(config('sic.campos_traza')[$modulo]))
<div class="card source-fields"><h3>Fuentes relacionadas con cada dato</h3><p class="muted">Selecciona dos documentos distintos si los tienes. Mantén sin seleccionar los datos aún no respaldados.</p>
<input type="hidden" name="_edit_fuentes" value="1">
<div class="f">
@foreach(config("sic.campos_traza.{$modulo}") as $campo)
<label>{{ $cfg['campos'][$campo][0]??$campo }}
<select name="f_{{ $campo }}[]" multiple>
@foreach($entidades['fuentes'] as $f)
<option value="{{ $f->id }}" @selected(in_array($f->id,$trazas[$campo]??[]))>#{{ $f->id }} · {{ $f->tipo }} · {{ Str::limit($f->descripcion,36) }}</option>
@endforeach
</select><small>Ctrl + clic para seleccionar varias.</small></label>
@endforeach
</div></div>
@endif
<div class="actions"><button type="submit" class="btn">{{ $ed?'Guardar cambios':'Crear registro' }}</button>@if($ed)<a href="{{ route('catalogo',['modulo'=>$modulo]) }}" class="btn sec">Cancelar edición</a>@endif</div>
</form>
</div>
<h2>Registros existentes <small>({{ $filas->total() }})</small></h2>
<div class="scroll"><table><thead><tr><th>ID</th>@foreach(array_slice(array_keys($cfg['campos']),0,5) as $campo)<th>{{ $cfg['campos'][$campo][0] }}</th>@endforeach<th>Detalle / acciones</th></tr></thead><tbody>
@forelse($filas as $r)
<tr><td>{{ $r->id }}</td>@foreach(array_slice(array_keys($cfg['campos']),0,5) as $campo)<td>{{ Str::limit((string)($r->$campo??''),65) }}</td>@endforeach
<td>
 <details><summary>Mostrar campos</summary><ul>@foreach($cfg['campos'] as $campo=>$def)<li><b>{{ $def[0] }}:</b> {{ $r->$campo??'—' }}</li>@endforeach</ul>@if($modulo==='ventas')<span class="tag">{{ $r->contabilizado?'Contabilizada':'Sin contabilizar' }}</span>@endif</details>
 <div class="actions"><a class="btn sec sm" href="{{ route('catalogo',['modulo'=>$modulo,'editar'=>$r->id]) }}">Editar</a>
 <form method="POST" action="{{ route('catalogo.borrar',['modulo'=>$modulo,'id'=>$r->id]) }}" onsubmit="return confirm('¿Eliminar registro? Esta acción puede afectar cálculos.')">@csrf @method('DELETE')<button class="btn danger sm" type="submit">Borrar</button></form>
 </div>
</td></tr>
@empty<tr><td colspan="8">No hay registros.</td></tr>@endforelse
</tbody></table></div>
<div class="pagination">@if($filas->previousPageUrl())<a href="{{ $filas->previousPageUrl() }}">← Anterior</a>@endif <span>Página {{ $filas->currentPage() }} de {{ $filas->lastPage() }}</span>@if($filas->nextPageUrl())<a href="{{ $filas->nextPageUrl() }}">Siguiente →</a>@endif</div>
@endsection
