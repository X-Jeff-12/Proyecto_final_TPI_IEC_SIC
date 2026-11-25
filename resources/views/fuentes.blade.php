@extends('layouts.app')
@section('content')
<h1>Fuentes y evidencias</h1><p class="lead">Documenta entrevistas, cotizaciones, comprobantes y fotografías. La guía exige sustentar los datos clave con al menos dos fuentes.</p>
<div class="card"><h2>Adjuntar evidencia</h2><form class="f" method="POST" action="{{ route('fuentes.guardar') }}" enctype="multipart/form-data">@csrf
<label>Tipo<select name="tipo" required>@foreach(['entrevista'=>'Entrevista','cotizacion'=>'Cotización','factura'=>'Factura','estado_financiero'=>'Registro contable','foto'=>'Foto/documento','observacion'=>'Observación'] as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></label>
<label>Referencia (persona/documento)<input name="referencia" required placeholder="Ej.: Francisco Hernández, propietario"></label>
<label>Fecha<input type="date" name="fecha"></label><label>Anexo (ej. A-2)<input name="anexo"></label>
<label class="full">Descripción<textarea name="descripcion" rows="2" required></textarea></label>
<label class="full">Archivo opcional (PDF, JPG, PNG, WebP; máximo 5 MB)<input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png,.webp"></label>
<div class="full"><button class="btn">Registrar evidencia</button></div></form></div>
<div class="scroll"><table><thead><tr><th>ID</th><th>Tipo</th><th>Descripción</th><th>Referencia</th><th>Fecha</th><th>Anexo</th><th>Documento</th><th></th></tr></thead><tbody>
@forelse($fuentes as $f)<tr><td>{{ $f->id }}</td><td>{{ $f->tipo }}</td><td>{{ $f->descripcion }}</td><td>{{ $f->referencia }}</td><td>{{ $f->fecha?:'Pendiente' }}</td><td>{{ $f->anexo }}</td><td>@if($f->archivo)<a href="{{ route('fuentes.archivo',$f->id) }}">Abrir</a>@else<span class="muted">No adjunto</span>@endif</td><td><form method="POST" action="{{ route('fuentes.borrar',$f->id) }}" onsubmit="return confirm('Se quitarán las relaciones con esta evidencia. ¿Continuar?')">@csrf @method('DELETE')<button class="btn danger sm">Borrar</button></form></td></tr>
@empty<tr><td colspan="8">Aún no hay fuentes.</td></tr>@endforelse
</tbody></table></div>
@endsection
