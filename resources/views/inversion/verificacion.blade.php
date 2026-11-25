@extends('layouts.app')
@section('content')
<h1>Verificación cruzada · Cálculo manual</h1><p class="lead">Registra aquí los indicadores que calculaste fuera de la aplicación a partir de los mismos flujos. La verificación no se considera hecha hasta aportar el procedimiento manual al informe.</p>
@forelse($evals as $e)
@php($registro=($manual[$e['alt']['id']]??collect())->keyBy('indicador'))
<div class="card"><h2>{{ $e['alt']['nombre'] }}</h2><form method="POST" action="{{ route('verificacion.guardar') }}">@csrf
<input type="hidden" name="alt_id" value="{{ $e['alt']['id'] }}">
<div class="f">@foreach(['vpn'=>'VPN','tir'=>'TIR decimal (ej. 0.12)','caue'=>'CAUE','bc'=>'Beneficio / Costo'] as $k=>$label)
<label>{{ $label }} (manual)<input name="{{ $k }}" type="number" step="0.000001" value="{{ $registro[$k]->valor??'' }}"></label>
@endforeach
<label class="full">Procedimiento o referencia del cálculo manual<textarea name="nota">{{ ($registro->first())->nota??'' }}</textarea></label></div><button class="btn">Guardar verificación</button></form>
<div class="scroll" style="margin-top:1rem"><table><thead><tr><th>Indicador</th><th class="num">Aplicación (después impuesto)</th><th class="num">Manual</th><th>Resultado</th></tr></thead><tbody>
@foreach(['vpn'=>'VPN','tir'=>'TIR (decimal)','caue'=>'CAUE','bc'=>'B/C'] as $k=>$label)
@php($app=$e['di'][$k])@php($man=$registro[$k]->valor??null)@php($tol=$k==='tir'?0.0001:0.01)
<tr><td>{{ $label }}</td><td class="num">{{ $app===null?'N/D':number_format($app,6) }}</td><td class="num">{{ $man===null?'Pendiente':number_format($man,6) }}</td><td>@if($man===null||$app===null)<span class="tag warn">Sin validar</span>@elseif(abs($man-$app)<=$tol)<span class="tag ok">Coincide</span>@else<span class="tag bad">Diferencia</span>@endif</td></tr>
@endforeach</tbody></table></div>
</div>
@empty<div class="card">Sin evaluaciones.</div>@endforelse
@endsection
