@extends('layouts.app')
@section('content')
<h1>Activos y cuadros de depreciación</h1><p class="lead">Tres métodos por activo, calculados sobre sus datos actuales. El ajuste contable mensual usa línea recta.</p>
<div class="actions"><a class="btn" href="{{ route('catalogo',['modulo'=>'activos']) }}">Administrar activos</a></div>
@forelse($activos as $item)<div class="card"><h2>{{ $item['a']->nombre }}</h2><p>Coste: <b>${{ number_format($item['a']->costo,2) }}</b> · Vida útil: {{ $item['a']->vida_util }} años · Salvamento: ${{ number_format($item['a']->valor_salvamento,2) }} · Nivel {{ $item['a']->nivel }}</p>
@foreach($item['tablas'] as $nombre=>$rows)<details><summary>{{ $nombre }}</summary><div class="scroll"><table><thead><tr><th>Año</th><th class="num">Dep. del año</th><th class="num">Dep. acumulada</th><th class="num">Valor en libros</th></tr></thead><tbody>@foreach($rows as $row)<tr><td>{{ $row['anio'] }}</td><td class="num">{{ number_format($row['dep'],2) }}</td><td class="num">{{ number_format($row['acum'],2) }}</td><td class="num">{{ number_format($row['libro'],2) }}</td></tr>@endforeach</tbody></table></div></details>@endforeach
</div>@empty<div class="card">Sin activos.</div>@endforelse
@endsection
