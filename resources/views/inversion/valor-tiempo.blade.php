@extends('layouts.app')
@section('content')
<h1>Valor del dinero en el tiempo</h1><p class="lead">Interés simple y compuesto, tasas nominal/efectiva y factores de equivalencia.</p>
<div class="card"><form method="GET" action="{{ route('valor.tiempo') }}" class="f">
<label>Capital ($)<input type="number" name="capital" min="0" step="0.01" value="{{ $p }}"></label>
<label>Tasa anual (%)<input type="number" name="tasa" min="0" max="100" step="0.0001" value="{{ $tasa }}"></label>
<label>Años<input type="number" name="periodos" min="1" max="30" value="{{ $n }}"></label>
<label>Capitalizaciones por año<input type="number" name="m" min="1" max="365" value="{{ $m }}"></label>
<div class="full"><button class="btn">Calcular</button></div></form></div>
<div class="grid">@foreach($resultados as $key=>$valor)<div class="card stat"><span>{{ str_replace('_',' ',ucwords($key,'_')) }}</span><b>{{ str_starts_with($key,'tasa')?number_format($valor*100,4).'%':'$'.number_format($valor,2) }}</b></div>@endforeach</div>
<h2>Factores de interés compuesto</h2>
<div class="scroll"><table><thead><tr><th>Año</th>@foreach(['P/F','F/P','P/A','A/P','F/A','A/F'] as $col)<th class="num">{{ $col }}</th>@endforeach</tr></thead><tbody>@foreach($factores as $r)<tr><td>{{ $r['n'] }}</td>@foreach(['pf','fp','pa','ap','fa','af'] as $k)<td class="num">{{ number_format($r[$k],6) }}</td>@endforeach</tr>@endforeach</tbody></table></div>
@endsection
