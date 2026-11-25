@extends('layouts.app')
@section('content')
<h1>Historial de modificaciones</h1><p class="lead">Bitácora académica de acciones guardadas desde el servidor. No es auditoría inmutable.</p>
<div class="scroll"><table><thead><tr><th>Fecha/hora</th><th>Acción</th><th>Detalle</th></tr></thead><tbody>
@foreach($filas as $r)<tr><td>{{ $r->cuando }}</td><td>{{ $r->accion }}</td><td>{{ $r->detalle }}</td></tr>@endforeach</tbody></table></div>
<div class="pagination">@if($filas->previousPageUrl())<a href="{{ $filas->previousPageUrl() }}">← Anterior</a>@endif <span>Página {{ $filas->currentPage() }} de {{ $filas->lastPage() }}</span>@if($filas->nextPageUrl())<a href="{{ $filas->nextPageUrl() }}">Siguiente →</a>@endif</div>
@endsection
