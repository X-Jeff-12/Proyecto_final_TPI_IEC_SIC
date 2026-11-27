@extends('layouts.app')

@section('content')
<header class="dashboard-hero">
    <h1>Panel de control <span>Car Wash Hernández</span></h1>
    <p>Registro contable, costos y evaluación de inversiones con base en la información de octubre de 2026.</p>
    <div class="dashboard-meta">
        <span>Laravel · PostgreSQL</span>
        <span>Proyecto académico integrado</span>
        <span>Gestión financiera</span>
    </div>
</header>

@php
    $metrics = [
        ['Ingresos en libros', '$'.number_format($est['totales']['ingresos'], 2), 'blue', 'wash', 'bars'],
        ['Costos del período', '$'.number_format($est['totales']['costos'], 2), 'violet', 'coins', 'coins'],
        ['Resultado del período', '$'.number_format($est['utilidad'], 2), $est['utilidad'] >= 0 ? 'green' : 'red', 'trend', 'trend'],
        ['Volumen de referencia', number_format($c['q'], 0).' vehículos', 'gold', 'car', 'car'],
    ];
    $shortcuts = [
        ['Libro diario', 'Asientos con partida doble', 'book', route('diario')],
        ['Costos por servicio', 'MD, MOD, CIF y punto de partida para inversión', 'cost', route('costos')],
        ['Alternativas', 'Registrar o modificar la inversión', 'arrows', route('catalogo', ['modulo' => 'alternativas'])],
        ['Comparar inversiones', 'VPN, TIR, CAUE, B/C y viabilidad', 'bars', route('comparar')],
        ['Evidencias', 'Adjuntar comprobantes y entrevistas', 'evidence', route('fuentes')],
        ['Diagnóstico', 'Conciliar ingresos y fuentes pendientes', 'search', route('diagnostico')],
    ];
@endphp

<dl class="dashboard-metrics" aria-label="Indicadores contables">
    @foreach($metrics as [$label, $value, $color, $icon, $decoration])
        <div class="dashboard-metric metric-{{ $color }}">
            <dt><x-ui-icon :name="$icon" /> {{ $label }}</dt>
            <dd>{{ $value }}</dd>
            <x-ui-icon :name="$decoration" class="metric-decoration" />
        </div>
    @endforeach
</dl>

<div class="dashboard-panels">
    <section class="dashboard-panel" aria-labelledby="dashboard-incomes">
        <header class="panel-heading">
            <h2 id="dashboard-incomes"><x-ui-icon name="bars" /> Ingresos en libros</h2>
            <a class="panel-link" href="{{ route('estados') }}">Estados financieros <x-ui-icon name="chevron" /></a>
        </header>
        @include('dashboard-income-chart', ['accounts' => $est['ingresos']])
    </section>

    <section class="dashboard-panel" aria-labelledby="dashboard-shortcuts">
        <header class="panel-heading">
            <h2 id="dashboard-shortcuts"><x-ui-icon name="layers" /> Accesos principales</h2>
        </header>
        <div class="dashboard-shortcuts">
            @foreach($shortcuts as [$label, $description, $icon, $url])
                <a class="dashboard-shortcut {{ $icon === 'cost' ? 'shortcut-highlight' : '' }}" href="{{ $url }}">
                    <x-ui-icon :name="$icon" />
                    <b>{{ $label }}</b>
                    <span>{{ $description }}</span>
                </a>
            @endforeach
        </div>
    </section>
</div>
@endsection
