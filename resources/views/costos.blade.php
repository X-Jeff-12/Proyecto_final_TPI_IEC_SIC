@extends('layouts.app')
@section('content')
<h1>Sistema de costos por servicio</h1><p class="lead">Distribución de materiales directos (MD), mano de obra directa (MOD) y costos indirectos de fabricación/servicio (CIF). Las ponderaciones son supuestos a validar.</p>
<div class="grid"><div class="card stat"><span>MD del período</span><b>${{ number_format($c['totales']['md'],2) }}</b></div><div class="card stat"><span>MOD del período</span><b>${{ number_format($c['totales']['mod'],2) }}</b></div><div class="card stat"><span>CIF del período</span><b>${{ number_format($c['totales']['cif'],2) }}</b></div><div class="card stat"><span>Vehículos proyectados</span><b>{{ number_format($c['base'],2) }}</b></div></div>
<div class="scroll"><table><thead><tr><th>Servicio</th><th class="num">Cantidad</th><th class="num">Peso</th><th class="num">MD u.</th><th class="num">MOD u.</th><th class="num">CIF u.</th><th class="num">Costo u.</th><th class="num">Precio</th><th class="num">Margen</th></tr></thead><tbody>
@foreach($c['filas'] as $f)<tr><td>{{ $f['nombre'] }}</td>@foreach(['cantidad','peso','md','mod','cif','costo','precio','margen'] as $k)<td class="num">{{ number_format($f[$k],2) }}</td>@endforeach</tr>@endforeach</tbody></table></div>
<div class="card"><h2>Base de asignación</h2><p class="muted">Equivalentes de actividad: {{ number_format($c['equivalentes'],2) }}. Las cifras de conteo y promedio no coinciden y no se modifican para forzar una conciliación.</p><form method="POST" action="{{ route('costos.volumen') }}" class="f">@csrf
@php($cfg=\App\Services\ContabilidadService::cfg())
<label>Base de volumen<select name="base_volumen"><option value="promedio" @selected(($cfg['base_volumen']??'promedio')==='promedio')>Días × vehículos/día</option><option value="conteo" @selected(($cfg['base_volumen']??'promedio')==='conteo')>Conteo de entrevista</option></select></label>
<label>Días laborados<input name="dias_trabajo" type="number" step="0.1" min="0" max="31" value="{{ $cfg['dias_trabajo']??27 }}"></label>
<label>Vehículos promedio/día<input name="veh_dia_promedio" type="number" step="0.1" min="0" value="{{ $cfg['veh_dia_promedio']??9 }}"></label><div class="full"><button class="btn">Recalcular</button></div></form></div>
<h2>Clasificación de costos y gastos</h2>
<div class="scroll"><table><thead><tr><th>Cuenta</th><th>Comportamiento</th><th class="num">Costo/Gasto</th></tr></thead><tbody>
@foreach($costos as $f)@if(abs($f['debe']-$f['haber'])>0.001)<tr><td>{{ $f['codigo'] }} · {{ $f['nombre'] }}</td><td>{{ $f['comportamiento']?:'Sin clasificar' }}</td><td class="num">{{ number_format($f['debe']-$f['haber'],2) }}</td></tr>@endif @endforeach</tbody></table></div>
@endsection
