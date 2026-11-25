@extends('layouts.app')
@section('content')
<h1>Libro mayor y cuentas T</h1><p class="lead">Todos los movimientos agrupados por cuenta. Saldo positivo equivale a saldo deudor.</p>
@forelse($movs as $codigo=>$lista)
<details class="card"><summary><strong>{{ $codigo }} · {{ $cuentas[$codigo]->nombre??'Cuenta' }}</strong> — {{ count($lista) }} movimientos</summary>
<div class="scroll"><table><thead><tr><th>Fecha</th><th>Descripción</th><th>Tipo</th><th class="num">Debe</th><th class="num">Haber</th></tr></thead><tbody>
@php($td=0)@php($th=0)
@foreach($lista as $m)@php($td+=(float)$m->debe)@php($th+=(float)$m->haber)
<tr><td>{{ $m->fecha }}</td><td>{{ $m->descripcion }}</td><td>{{ $m->tipo }}</td><td class="num">{{ number_format($m->debe,2) }}</td><td class="num">{{ number_format($m->haber,2) }}</td></tr>
@endforeach<tr class="total"><td colspan="3">Totales · Saldo {{ number_format(abs($td-$th),2) }} {{ $td>=$th?'deudor':'acreedor' }}</td><td class="num">{{ number_format($td,2) }}</td><td class="num">{{ number_format($th,2) }}</td></tr>
</tbody></table></div></details>
@empty<div class="card">Sin movimientos.</div>@endforelse
@endsection
