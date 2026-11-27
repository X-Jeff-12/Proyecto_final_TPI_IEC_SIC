<!doctype html>
<html lang="es">
@php($dashboardView = request()->routeIs('dashboard'))
<head>
 <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <title>{{ $title ?? 'Car Wash Hernández' }} · Proyecto Integrador</title>
 <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
 <link rel="stylesheet" href="{{ asset('css/carwash.css') }}?v={{ filemtime(public_path('css/carwash.css')) }}">
 <link rel="stylesheet" href="{{ asset('css/navigation-skeleton.css') }}?v={{ filemtime(public_path('css/navigation-skeleton.css')) }}">
 <script src="{{ asset('js/navigation-skeleton.js') }}?v={{ filemtime(public_path('js/navigation-skeleton.js')) }}" defer></script>
 @if(is_file(public_path('img/logo-carwash.png')))
  <link rel="icon" type="image/png" href="{{ asset('img/logo-carwash.png') }}">
 @endif
</head>
<body @class(['app-page', 'dashboard-page' => $dashboardView, 'module-page' => !$dashboardView])>
<input type="checkbox" id="menu-toggle" aria-label="Menú de navegación"><label for="menu-toggle" class="scrim" aria-hidden="true"></label>
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
  @include('layouts.navigation')
  <div class="session"><span>Sesión: {{ auth()->user()->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn sec sm" type="submit">Cerrar sesión</button></form></div>
 </aside>
 <div class="content">
  <div class="topbar">
   @if(is_file(public_path('img/logo-carwash.png')))
    <img class="topbar-logo" width="36" height="36" src="{{ asset('img/logo-carwash.png') }}" alt="Logo de Car Wash Hernández">
   @endif
   <span class="topbar-title">Car Wash Hernández</span><label class="burger" for="menu-toggle" aria-label="Abrir o cerrar el menú de navegación">☰ Menú</label>
  </div>
  <header class="app-toolbar">
   @php($today = now()->locale('es'))
   <time class="app-date" datetime="{{ $today->toDateString() }}"><x-ui-icon name="calendar" /> {{ $today->isoFormat('ddd, D MMM YYYY') }}</time>
   <details class="app-profile">
    <summary>
     <span class="app-avatar"><x-ui-icon name="user" /></span>
     <span class="app-profile-name"><b>{{ auth()->user()->name }}</b><small>Sesión activa</small></span>
     <x-ui-icon name="chevron" class="profile-chevron" />
    </summary>
    <form method="POST" action="{{ route('logout') }}">
     @csrf
     <button type="submit">Cerrar sesión</button>
    </form>
   </details>
  </header>
  <main id="app-content">
   @if(session('ok')) <div class="flash">{{ session('ok') }}</div> @endif
   @if($errors->any()) <div class="banner danger"><b>Revisa los datos:</b><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
   @yield('content')
   <footer class="footer">Car Wash Hernández · Sistema de Gestión Contable y Evaluación de Inversiones</footer>
  </main>
  <div id="navigation-skeleton" class="navigation-skeleton" aria-hidden="true" hidden>
   <div class="skeleton-body"></div>
  </div>
  <p id="navigation-status" class="visually-hidden" role="status" aria-live="polite" aria-atomic="true"></p>
 </div>
</div>
</body></html>
