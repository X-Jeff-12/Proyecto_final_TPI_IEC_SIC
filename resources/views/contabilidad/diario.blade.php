@extends('layouts.app')
@section('content')
<h1>Libro diario · Partida doble</h1><p class="lead">Registra operaciones normales, ajustes o cierres. El Debe y Haber deben coincidir. Los asientos importados conservan su nivel de evidencia.</p>
<div class="card"><h2>Nuevo asiento</h2><form method="POST" action="{{ route('diario.guardar') }}">@csrf
<div class="f"><label>Fecha<input type="date" name="fecha" value="{{ old('fecha',now()->toDateString()) }}" required></label>
<label>Tipo<select name="tipo"><option value="normal">Normal</option><option value="ajuste">Ajuste</option><option value="cierre">Cierre</option></select></label>
<label>Nivel de evidencia<select name="nivel">@foreach(config('sic.niveles') as $k=>$v)<option value="{{ $k }}" @selected($k==='estimado')>{{ $v }}</option>@endforeach</select></label>
<label>Fuente<select name="fuente_id"><option value="">Sin respaldo documental</option>@foreach($fuentes as $f)<option value="{{ $f->id }}">#{{ $f->id }} · {{ Str::limit($f->descripcion,65) }}</option>@endforeach</select></label>
<label class="full">Descripción del hecho económico<input name="descripcion" placeholder="Ej.: pago de alquiler de noviembre" required></label></div>
<div class="scroll"><table><thead><tr><th>#</th><th>Cuenta</th><th>Debe ($)</th><th>Haber ($)</th></tr></thead><tbody>
@for($i=0;$i<8;$i++)<tr><td>{{ $i+1 }}</td><td><select name="cuenta[]"><option value="">Seleccione...</option>@foreach($cuentas as $c)<option value="{{ $c->codigo }}">{{ $c->codigo }} · {{ $c->nombre }}</option>@endforeach</select></td>
<td><input type="number" step="0.01" min="0" name="debe[]" value="0"></td><td><input type="number" step="0.01" min="0" name="haber[]" value="0"></td></tr>@endfor
</tbody></table></div>
<button class="btn">Registrar asiento</button>
</form></div>
<h2>Asientos registrados</h2>
@forelse($asientos as $a)
<div class="card"><div class="actions" style="justify-content:space-between"><b>#{{ $a->id }} · {{ $a->fecha }} · {{ $a->descripcion }}</b><span class="tag">{{ $a->nivel }}</span></div><p class="muted">Tipo: {{ $a->tipo }} @if($a->origen) · Origen: {{ $a->origen }} @endif @if($a->fuente_id) · Fuente #{{ $a->fuente_id }} @endif</p>
<div class="scroll"><table><thead><tr><th>Cuenta</th><th class="num">Debe</th><th class="num">Haber</th></tr></thead><tbody>
@foreach($movs[$a->id]??[] as $m)<tr><td>{{ $m->cuenta }} · {{ $m->nombre }}</td><td class="num">{{ number_format($m->debe,2) }}</td><td class="num">{{ number_format($m->haber,2) }}</td></tr>@endforeach
</tbody></table></div>
@if(!$a->origen)<form method="POST" action="{{ route('diario.borrar',$a->id) }}" onsubmit="return confirm('¿Eliminar asiento y todos sus movimientos?')">@csrf @method('DELETE')<button class="btn danger sm">Borrar asiento</button></form>@else<span class="muted">Asiento automático o enlazado: eliminación restringida</span>@endif
</div>
@empty<div class="card">No hay asientos.</div>@endforelse
<div class="pagination">@if($asientos->previousPageUrl())<a href="{{ $asientos->previousPageUrl() }}">← Anterior</a>@endif <span>Página {{ $asientos->currentPage() }} de {{ $asientos->lastPage() }}</span>@if($asientos->nextPageUrl())<a href="{{ $asientos->nextPageUrl() }}">Siguiente →</a>@endif</div>
@endsection
