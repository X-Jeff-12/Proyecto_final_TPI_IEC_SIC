<!doctype html>
<html lang="es">
<head>
 <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <title>{{ $title ?? 'Car Wash Hernández' }} · Proyecto Integrador</title>
 <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
 @if(is_file(public_path('img/logo-carwash.png')))
  <link rel="icon" type="image/png" href="{{ asset('img/logo-carwash.png') }}">
 @endif
</head>
<body>
<input type="checkbox" id="menu-toggle"><label for="menu-toggle" class="scrim"></label>
<div class="shell">
 <aside class="side">
  <a class="brand" href="{{ route('dashboard') }}">
   @if(is_file(public_path('img/logo-carwash.png')))
    <img class="brand-logo" width="64" height="64" src="{{ asset('img/logo-carwash.png') }}" alt="Logo de Car Wash Hernández">
   @else
    <span class="brand-symbol">CH</span>
   @endif
   <span class="brand-copy"><b>Car Wash Hernández</b><small>Contabilidad · Economía · TPI</small></span>
  </a>
  <div class="navgroup"><h4>Resumen</h4>
   <a class="nav {{ request()->routeIs('dashboard')?'on':'' }}" href="{{ route('dashboard') }}">Inicio</a>
   <a class="nav {{ request()->routeIs('diagnostico')?'on':'' }}" href="{{ route('diagnostico') }}">Diagnóstico y conciliación</a>
  </div>
  <div class="navgroup"><h4>Operación</h4>
   <a class="nav" href="{{ route('catalogo',['modulo'=>'ventas']) }}">Ventas</a>
   <a class="nav" href="{{ route('catalogo',['modulo'=>'pagos_personal']) }}">Personal y pagos</a>
   <a class="nav" href="{{ route('catalogo',['modulo'=>'productos']) }}">Inventario</a>
   <a class="nav" href="{{ route('catalogo',['modulo'=>'pendientes']) }}">Cobros y pagos pendientes</a>
   <a class="nav" href="{{ route('catalogo',['modulo'=>'mantenimientos']) }}">Mantenimiento</a>
   <a class="nav" href="{{ route('catalogo',['modulo'=>'activos']) }}">Activos</a>
  </div>
  <div class="navgroup"><h4>Sistemas Contables</h4>
   <a class="nav" href="{{ route('diario') }}">Libro diario</a>
   <a class="nav" href="{{ route('mayor') }}">Cuentas T / Mayor</a>
   <a class="nav" href="{{ route('balanza') }}">Balanza de comprobación</a>
   <a class="nav" href="{{ route('hoja') }}">Hoja de trabajo</a>
   <a class="nav" href="{{ route('estados') }}">Estados financieros</a>
   <a class="nav" href="{{ route('caja') }}">Control de caja</a>
   <a class="nav" href="{{ route('costos') }}">Sistema de costos</a>
   <a class="nav" href="{{ route('catalogo',['modulo'=>'servicios']) }}">Servicios / CRUD</a>
  </div>
  <div class="navgroup"><h4>Ingeniería Económica</h4>
   <a class="nav" href="{{ route('catalogo',['modulo'=>'alternativas']) }}">Alternativas / CRUD</a>
   <a class="nav" href="{{ route('evaluacion') }}">Evaluación económica</a>
   <a class="nav" href="{{ route('comparar') }}">Comparación</a>
   <a class="nav" href="{{ route('activos.depreciacion') }}">Depreciación</a>
   <a class="nav" href="{{ route('verificacion') }}">Verificación cruzada</a>
   <a class="nav" href="{{ route('valor.tiempo') }}">Valor del dinero</a>
  </div>
  <div class="navgroup"><h4>Administración</h4>
   <a class="nav" href="{{ route('fuentes') }}">Fuentes y evidencias</a>
   <a class="nav" href="{{ route('empresa') }}">Empresa y tasas</a>
   <a class="nav" href="{{ route('reportes') }}">Informe / imprimir</a>
   <a class="nav" href="{{ route('historial') }}">Historial</a>
   <a class="nav" href="{{ route('respaldo') }}">Exportar JSON</a>
  </div>
  <div class="session"><span>Sesión: {{ auth()->user()->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn sec sm" type="submit">Cerrar sesión</button></form></div>
 </aside>
 <div class="content">
  <div class="topbar">
   @if(is_file(public_path('img/logo-carwash.png')))
    <img class="topbar-logo" width="36" height="36" src="{{ asset('img/logo-carwash.png') }}" alt="Logo de Car Wash Hernández">
   @endif
   <span class="topbar-title">Car Wash Hernández</span><label class="burger" for="menu-toggle" aria-label="Abrir o cerrar el menú de navegación">☰ Menú</label>
  </div>
  <main>
   @if(session('ok')) <div class="flash">{{ session('ok') }}</div> @endif
   @if($errors->any()) <div class="banner danger"><b>Revisa los datos:</b><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
   @yield('content')
   <footer class="footer">Car Wash Hernández · Sistema de Gestión Contable y Evaluación de Inversiones</footer>
  </main>
 </div>
</div>
</body></html>
