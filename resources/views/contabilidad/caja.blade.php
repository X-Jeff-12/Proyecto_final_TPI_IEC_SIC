@extends('layouts.app')
@section('content')
<h1>Movimientos de caja</h1><p class="lead">Saldo acumulado de la cuenta 1101. No es una conciliación bancaria y el saldo de apertura fue reconstruido sin efectivo inicial comprobado.</p>
<div class="card stat"><span>Saldo de caja según libro</span><b>${{ number_format($saldo,2) }}</b></div>
<div class="scroll"><table><thead><tr><th>Fecha</th><th>Concepto</th><th>Nivel</th><th class="num">Entradas</th><th class="num">Salidas</th><th class="num">Saldo</th></tr></thead><tbody>
@foreach($filas as $r)<tr><td>{{ $r['mov']->fecha }}</td><td>{{ $r['mov']->descripcion }}</td><td>{{ $r['mov']->nivel }}</td><td class="num">{{ number_format($r['mov']->debe,2) }}</td><td class="num">{{ number_format($r['mov']->haber,2) }}</td><td class="num">{{ number_format($r['saldo'],2) }}</td></tr>@endforeach
</tbody></table></div>
@endsection
