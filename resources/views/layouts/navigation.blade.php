@php
    $groups = [
        'Resumen' => [
            ['Inicio', 'home', 'dashboard'],
            ['Diagnóstico y conciliación', 'search', 'diagnostico'],
        ],
        'Operación' => [
            ['Ventas', 'car', 'catalogo', 'ventas'],
            ['Personal y pagos', 'users', 'catalogo', 'pagos_personal'],
            ['Inventario', 'box', 'catalogo', 'productos'],
            ['Cobros y pagos pendientes', 'ledger', 'catalogo', 'pendientes'],
            ['Mantenimiento', 'tools', 'catalogo', 'mantenimientos'],
            ['Activos', 'car', 'catalogo', 'activos'],
        ],
        'Sistemas Contables' => [
            ['Libro diario', 'book', 'diario'],
            ['Cuentas T / Mayor', 'ledger', 'mayor'],
            ['Balanza de comprobación', 'scales', 'balanza'],
            ['Hoja de trabajo', 'file', 'hoja'],
            ['Estados financieros', 'bars', 'estados'],
            ['Control de caja', 'cash', 'caja'],
            ['Sistema de costos', 'cost', 'costos'],
            ['Servicios / CRUD', 'wash', 'catalogo', 'servicios'],
        ],
        'Ingeniería Económica' => [
            ['Alternativas / CRUD', 'arrows', 'catalogo', 'alternativas'],
            ['Evaluación económica', 'trend', 'evaluacion'],
            ['Comparación', 'bars', 'comparar'],
            ['Depreciación', 'car', 'activos.depreciacion'],
            ['Sensibilidad', 'trend', 'sensibilidad'],
            ['Defensor vs. retador', 'arrows', 'reemplazo'],
            ['Verificación cruzada', 'search', 'verificacion'],
            ['Valor del dinero', 'coins', 'valor.tiempo'],
        ],
        'Administración' => [
            ['Fuentes y evidencias', 'evidence', 'fuentes'],
            ['Empresa y tasas', 'cost', 'empresa'],
            ['Informe / imprimir', 'file', 'reportes'],
            ['Historial', 'ledger', 'historial'],
            ['Exportar JSON', 'box', 'respaldo'],
        ],
    ];
@endphp

<nav aria-label="Navegación principal">
    @foreach($groups as $heading => $links)
        <div class="navgroup">
            <h4>{{ $heading }}</h4>
            @foreach($links as $link)
                @php
                    [$label, $icon, $routeName] = $link;
                    $module = $link[3] ?? null;
                    $active = request()->routeIs($routeName) && ($module === null || request()->route('modulo') === $module);
                @endphp
                <a @class(['nav', 'on' => $active])
                   href="{{ route($routeName, $module === null ? [] : ['modulo' => $module]) }}"
                   @if($active) aria-current="page" @endif
                   @if($routeName !== 'respaldo') data-loading-page="{{ $routeName }}" @endif
                   @if($module !== null)
                       data-loading-fields="{{ implode(',', array_column(config("sic.modulos.{$module}.campos", []), 1)) }}"
                       data-loading-sources="{{ count(config("sic.campos_traza.{$module}", [])) }}"
                   @endif>
                    <x-ui-icon :name="$icon" />
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>
    @endforeach
</nav>
