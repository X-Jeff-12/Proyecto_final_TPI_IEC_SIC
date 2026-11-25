@extends('layouts.app')
@section('content')
<div class="hero"><h1>Panel de control · Car Wash Hernández</h1><p>Registro contable, costos y evaluación de inversiones con base en la información de octubre de 2026.</p><div class="meta"><span>Laravel · PostgreSQL</span><span>Proyecto académico integrado</span><span>Gestión financiera</span></div></div>
<div class="grid">
 <div class="card stat"><span>Ingresos en libros</span><b>${{ number_format($est['totales']['ingresos'],2) }}</b></div>
 <div class="card stat"><span>Costos del período</span><b>${{ number_format($est['totales']['costos'],2) }}</b></div>
 <div class="card stat {{ $est['utilidad']>=0?'good':'neg' }}"><span>Resultado del período</span><b>${{ number_format($est['utilidad'],2) }}</b></div>
 <div class="card stat"><span>Volumen de referencia</span><b>{{ number_format($c['q'],0) }} vehículos</b></div>
</div>
<h2>Accesos principales</h2>
<div class="grid">
<a class="linkcard" href="{{ route('diario') }}"><b>Libro diario</b><span>Asientos con partida doble</span></a>
<a class="linkcard" href="{{ route('costos') }}"><b>Costos por servicio</b><span>MD, MOD, CIF y punto de partida para inversión</span></a>
<a class="linkcard" href="{{ route('catalogo',['modulo'=>'alternativas']) }}"><b>Alternativas</b><span>Registrar o modificar la inversión</span></a>
<a class="linkcard" href="{{ route('comparar') }}"><b>Comparar inversiones</b><span>VPN, TIR, CAUE, B/C y viabilidad</span></a>
<a class="linkcard" href="{{ route('fuentes') }}"><b>Evidencias</b><span>Adjuntar comprobantes y entrevistas</span></a>
<a class="linkcard" href="{{ route('diagnostico') }}"><b>Diagnóstico</b><span>Conciliar ingresos y fuentes pendientes</span></a>
</div>
@endsection
