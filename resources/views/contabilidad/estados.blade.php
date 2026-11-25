@extends('layouts.app')
@section('content')
<h1>Estados financieros del periodo</h1>
@if($no>0)<div class="noticia"><strong>Atención:</strong> hay {{ $no }} asientos que no tienen nivel «confirmado». Los siguientes resultados son una reconstrucción académica, no estados certificados.</div>@endif
<div class="grid-2">
<div class="card"><h2>Estado de resultados</h2>
@foreach(['ingresos'=>'Ingresos','costos'=>'Costos','gastos'=>'Gastos'] as $key=>$label)<h3>{{ $label }}</h3><div class="scroll"><table><tbody>@foreach($e[$key] as $row)<tr><td>{{ $row['nombre'] }}</td><td class="num">{{ number_format($row['valor'],2) }}</td></tr>@endforeach
<tr class="total"><td>Total {{ strtolower($label) }}</td><td class="num">{{ number_format($e['totales'][$key],2) }}</td></tr></tbody></table></div>@endforeach
<h2>Utilidad bruta: ${{ number_format($e['utilidad_bruta'],2) }}</h2><h2>Utilidad neta: ${{ number_format($e['utilidad'],2) }}</h2>
</div>
<div class="card"><h2>Balance general reconstruido</h2>@foreach(['activos'=>'Activos','pasivos'=>'Pasivos','patrimonio'=>'Patrimonio'] as $key=>$label)<h3>{{ $label }}</h3><div class="scroll"><table><tbody>
@foreach($e[$key] as $row)<tr><td>{{ $row['nombre'] }}</td><td class="num">{{ number_format($row['valor'],2) }}</td></tr>@endforeach
<tr class="total"><td>Total {{ strtolower($label) }}</td><td class="num">{{ number_format($e['totales'][$key],2) }}</td></tr></tbody></table></div>@endforeach
<p><strong>Resultado del periodo incluido como componente de patrimonio:</strong> ${{ number_format($e['utilidad'],2) }}</p>
<p><b>Pasivo + patrimonio + resultado:</b> ${{ number_format($e['totales']['pasivos']+$e['totales']['patrimonio']+$e['utilidad'],2) }}</p>
<p><b>Activos:</b> ${{ number_format($e['totales']['activos'],2) }}</p>
<p><b>Diferencia:</b> ${{ number_format($e['cuadre'],2) }}</p>
</div></div>
@endsection
