@extends('layouts.app')
@section('content')
<h1>Hoja de trabajo contable</h1><p class="lead">Balanza de comprobación, ajustes, balanza ajustada, estado de resultados y balance general.</p>
<div class="scroll"><table><thead><tr><th>Cuenta</th>@foreach(['BC D','BC H','Aj D','Aj H','BA D','BA H','ER D','ER H','BG D','BG H'] as $col)<th class="num">{{ $col }}</th>@endforeach</tr></thead><tbody>
@foreach($h['filas'] as $f)<tr><td>{{ $f['codigo'] }} {{ $f['nombre'] }}</td>@foreach(['bc_d','bc_h','aj_d','aj_h','ba_d','ba_h','er_d','er_h','bg_d','bg_h'] as $k)<td class="num">{{ number_format($f[$k],2) }}</td>@endforeach</tr>@endforeach
<tr class="total"><td>Totales</td>@foreach(['bc_d','bc_h','aj_d','aj_h','ba_d','ba_h','er_d','er_h','bg_d','bg_h'] as $k)<td class="num">{{ number_format($h['tot'][$k],2) }}</td>@endforeach</tr>
<tr><td>Utilidad o pérdida</td>@foreach(['bc_d','bc_h','aj_d','aj_h','ba_d','ba_h','er_d','er_h','bg_d','bg_h'] as $k)<td class="num">{{ isset($h['cierre'][$k])?number_format($h['cierre'][$k],2):'—' }}</td>@endforeach</tr>
</tbody></table></div>
<div class="card">Resultado neto estimado: <b>${{ number_format($h['utilidad'],2) }}</b>. El asiento automático de depreciación es provisional mientras no se verifiquen las vidas útiles.</div>
@endsection
