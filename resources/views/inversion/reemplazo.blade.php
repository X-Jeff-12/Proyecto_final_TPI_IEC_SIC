@extends('layouts.app')
@section('content')
<h1>Analisis defensor vs. retador</h1>
<p class="lead">Compara el CAUE de conservar el equipo actual (defensor) contra adquirir una alternativa nueva (retador). El defensor se evalúa a su valor de mercado actual, no al valor en libros. Se elige la opción con menor CAUE.</p>
<div class="noticia"><b>TMAR aplicada:</b> {{ $cfg['tmar'] ?? 12 }}%. Los activos sin valor de mercado registrado usan una estimación basada en caída anual del 15%.</div>
@forelse($resultados as $idx => $r)
<div class="card">
@if(isset($r['error']))
<h2>{{ $r['defensor']['nombre'] ?? 'Activo #'.($idx+1) }}</h2>
<div class="banner danger">{{ $r['error'] }}</div>
@else
<h2>Defensor: {{ $r['defensor']['nombre'] }}</h2>
<div class="grid">
 <div class="stat"><span>Valor de mercado</span><b>${{ number_format($r['defensor']['valor_mercado'],2) }}</b></div>
 <div class="stat"><span>Vida restante</span><b>{{ $r['defensor']['vida_restante'] }} años</b></div>
 <div class="stat"><span>CAO anual</span><b>${{ number_format($r['defensor']['cao'],2) }}</b></div>
 <div class="stat"><span>CAUE defensor</span><b>${{ number_format($r['defensor']['caue'],2) }}</b></div>
</div>

<h3>Retadores (alternativas de inversión)</h3>
<div class="scroll"><table>
<thead><tr><th>Alternativa</th><th class="num">Inversión ($)</th><th class="num">Vida útil</th><th class="num">Salvamento ($)</th><th class="num">CAO ($)</th><th class="num">CAUE ($)</th><th>¿Conviene?</th></tr></thead>
<tbody>
@foreach($r['retadores'] as $ret)
<tr class="{{ $ret['caue'] < $r['defensor']['caue'] ? '' : 'muted' }}">
 <td>{{ $ret['nombre'] }}</td>
 <td class="num">${{ number_format($ret['costo_inicial'],2) }}</td>
 <td class="num">{{ $ret['vida_util'] }} años</td>
 <td class="num">${{ number_format($ret['valor_salvamento'],2) }}</td>
 <td class="num">${{ number_format($ret['cao'],2) }}</td>
 <td class="num">${{ number_format($ret['caue'],2) }}</td>
 <td>{!! $ret['caue'] < $r['defensor']['caue'] ? '<span class="tag">Sí — menor CAUE</span>' : 'No' !!}</td>
</tr>
@endforeach
</tbody></table></div>

<div class="noticia" style="margin-top:1rem">
 <b>Decisión:</b>
 @if($r['conviene_reemplazar'])
  Conviene reemplazar. La mejor opción es <b>{{ $r['decision'] }}</b> con CAUE menor al defensor.
 @else
  Conservar el equipo actual (<b>{{ $r['defensor']['nombre'] }}</b>). Su CAUE es menor o igual que todas las alternativas.
 @endif
</div>
@endif
</div>
@empty
<div class="card">No hay activos registrados para análisis de reemplazo. <a href="{{ route('catalogo',['modulo'=>'activos']) }}">Registrar activos</a>.</div>
@endforelse

<div class="card"><h2>Metodología</h2>
<p>El análisis sigue el método del material de clase (Unidad 5 — Separata):</p>
<ul>
 <li><b>CAUE = P(A/P, i, n) − VS(A/F, i, n) + CAO</b></li>
 <li>El defensor usa su <b>valor de mercado</b> como P (no el valor en libros), porque el costo hundido ya no es recuperable.</li>
 <li>El CAO (Costo Anual de Operación) del retador se toma como energía + mantenimiento anuales de cada alternativa.</li>
 <li>Se elige la opción con <b>menor CAUE</b> (menor costo anual equivalente).</li>
</ul>
<p class="muted">Para registrar el valor de mercado y CAO de un activo, edítelo en <a href="{{ route('catalogo',['modulo'=>'activos']) }}">Activos</a>.</p>
</div>
@endsection
