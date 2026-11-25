@extends('layouts.app')
@section('content')
<h1>Balanza de comprobación</h1><p class="lead">Saldos normales y ajustes, según el libro diario.</p>
<div class="scroll"><table><thead><tr><th>Cuenta</th><th class="num">Saldo deudor</th><th class="num">Saldo acreedor</th></tr></thead><tbody>
@foreach($h['filas'] as $r)<tr><td>{{ $r['codigo'] }} · {{ $r['nombre'] }}</td><td class="num">{{ number_format($r['ba_d'],2) }}</td><td class="num">{{ number_format($r['ba_h'],2) }}</td></tr>@endforeach
<tr class="total"><td>Totales</td><td class="num">{{ number_format($h['tot']['ba_d'],2) }}</td><td class="num">{{ number_format($h['tot']['ba_h'],2) }}</td></tr>
</tbody></table></div>
<div class="card">Diferencia (Debe - Haber): <b>${{ number_format($h['tot']['ba_d']-$h['tot']['ba_h'],2) }}</b></div>
@endsection
