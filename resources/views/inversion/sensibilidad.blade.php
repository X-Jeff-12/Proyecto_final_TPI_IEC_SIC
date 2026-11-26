@extends('layouts.app')
@section('content')
<h1>Analisis de sensibilidad</h1>
<p class="lead">Se varía cada variable individualmente (±10% y ±20%) y se recalculan VPN, TIR, CAUE y TIRV después de impuesto. La variable con mayor impacto porcentual sobre el VPN es la más sensible.</p>
@foreach($errores as $error)<div class="banner danger">{{ $error }}</div>@endforeach
@forelse($analisis as $a)
<div class="card">
<h2>{{ $a['nombre'] }}</h2>
@if(isset($a['error']))
<div class="banner danger">{{ $a['error'] }}</div>
@else
@php $r = $a['resultado']; @endphp
<div class="grid">
 <div class="stat"><span>VPN base</span><b>${{ number_format($r['base']['vpn'],2) }}</b></div>
 <div class="stat"><span>TIR base</span><b>{{ $r['base']['tir']===null?'N/D':number_format($r['base']['tir']*100,2).'%' }}</b></div>
 <div class="stat"><span>CAUE base</span><b>${{ number_format($r['base']['caue'],2) }}</b></div>
 <div class="stat"><span>Variable más sensible</span><b>{{ $r['variables'][$r['mas_sensible']]['etiqueta'] ?? $r['mas_sensible'] }}</b></div>
</div>
@foreach($r['variables'] as $campo => $info)
<details{{ $campo === $r['mas_sensible'] ? ' open' : '' }}>
<summary>{{ $info['etiqueta'] }} (valor original: {{ $campo === 'tmar' ? number_format($info['original'],2).'%' : '$'.number_format($info['original'],2) }}) @if($campo === $r['mas_sensible'])<span class="tag">Más sensible</span>@endif</summary>
<div class="scroll"><table>
<thead><tr><th>Variación</th><th class="num">Valor modificado</th><th class="num">VPN ($)</th><th class="num">Δ VPN (%)</th><th class="num">TIR (%)</th><th class="num">CAUE ($)</th><th class="num">TIRV (%)</th></tr></thead>
<tbody>
@foreach($info['filas'] as $f)
<tr>
 <td>{{ $f['delta']>0?'+':'' }}{{ number_format($f['delta']*100,0) }}%</td>
 <td class="num">{{ $campo === 'tmar' ? number_format($f['valor_mod'],2).'%' : '$'.number_format($f['valor_mod'],2) }}</td>
 <td class="num">{{ $f['vpn']===null?'Error':'$'.number_format($f['vpn'],2) }}</td>
 <td class="num">{{ ($f['vpn']===null||abs($r['base']['vpn'])<0.01)?'—':number_format(($f['vpn']-$r['base']['vpn'])/abs($r['base']['vpn'])*100,2).'%' }}</td>
 <td class="num">{{ $f['tir']===null?'N/D':number_format($f['tir']*100,2).'%' }}</td>
 <td class="num">{{ $f['caue']===null?'Error':'$'.number_format($f['caue'],2) }}</td>
 <td class="num">{{ ($f['tirv']??null)===null?'N/D':number_format($f['tirv']*100,2).'%' }}</td>
</tr>
@endforeach
</tbody></table></div>
</details>
@endforeach
@endif
</div>
@empty
<div class="card">No hay alternativas evaluables. <a href="{{ route('catalogo',['modulo'=>'alternativas']) }}">Crear alternativa</a>.</div>
@endforelse
<div class="card"><h2>Interpretación</h2><p>Una variable es más sensible cuando un cambio porcentual pequeño en ella produce un cambio porcentual grande en el VPN. El porcentaje de impacto se calcula como |ΔVPN / VPN_base|. La fórmula de sensibilidad del material es: S = (ΔTIR / TIR_original) ÷ (ΔX / X_original) × 100.</p></div>
@endsection
