@extends('layouts.app')
@section('content')
<h1>Diagnóstico y conciliación de ingresos</h1><p class="lead">Conservamos las diferencias del registro informal. No ajustamos automáticamente las cifras para hacerlas coincidir.</p>
<div class="grid">
<div class="card stat"><span>Vehículos según desglose</span><b>{{ number_format($c['q'],0) }}</b></div>
<div class="card stat"><span>Ingresos por conteo de servicios</span><b>${{ number_format($c['conteo'],2) }}</b></div>
<div class="card stat"><span>Ingresos por promedio diario</span><b>${{ number_format($c['porProm'],2) }}</b></div>
<div class="card stat"><span>Estimación mensual de trabajo</span><b>${{ number_format($c['trabajo'],2) }}</b></div>
</div>
<div class="card"><h2>Composición del ingreso estimado por conteos</h2><div class="scroll"><table><tbody>
<tr><td>Lavados de vehículos</td><td class="num">${{ number_format($c['ingVeh'],2) }}</td></tr>
<tr><td>Servicios adicionales</td><td class="num">${{ number_format($c['ingAd'],2) }}</td></tr>
<tr><td>Aromatizantes revendidos</td><td class="num">${{ number_format($c['aroma'],2) }}</td></tr>
<tr class="total"><td>Total estimado por conteo</td><td class="num">${{ number_format($c['conteo'],2) }}</td></tr>
<tr><td>Diferencia con estimación de trabajo</td><td class="num">${{ number_format($c['diferencia_conteo'],2) }}</td></tr>
<tr><td>Diferencia con método del promedio</td><td class="num">${{ number_format($c['diferencia_promedio'],2) }}</td></tr>
</tbody></table></div><p class="muted">Estas son comparaciones de estimaciones. No equivalen a ventas facturadas.</p></div>
<div class="card"><h2>Evidencia de datos clave</h2><p><strong>{{ $total-count($faltantes) }} de {{ $total }}</strong> campos alcanzan dos fuentes distintas registradas.</p>
@if(count($faltantes))<div class="noticia">Se requiere verificación para:</div><ul>@foreach($faltantes as $f)<li>{{ $f }}</li>@endforeach</ul>@else<p>Todos los campos evaluados tienen al menos dos fuentes relacionadas.</p>@endif
</div>
<div class="card"><h2>Clasificación de registros</h2><div class="grid">@foreach(config('sic.niveles') as $k=>$label)<div class="stat"><span>{{ $label }}</span><b>{{ $nivel[$k]??0 }}</b></div>@endforeach</div></div>
@endsection
