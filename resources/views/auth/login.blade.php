<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Ingresar · Car Wash Hernández</title><link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">@if(is_file(public_path('img/logo-carwash.png')))<link rel="icon" type="image/png" href="{{ asset('img/logo-carwash.png') }}">@endif</head>
<body class="login-page"><div class="login-card">@if(is_file(public_path('img/logo-carwash.png')))
<img class="login-logo" width="104" height="104" src="{{ asset('img/logo-carwash.png') }}" alt="Logo de Car Wash Hernández">
@else
<span class="brand-symbol">CH</span>
@endif
<h1>Car Wash Hernández</h1><p class="muted">Sistema Contable y Evaluación de Inversiones</p>
@if($errors->any())<div class="banner danger">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('entrar') }}">@csrf
<label>Correo electrónico <input type="email" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com" required autofocus autocomplete="username"></label>
<label>Contraseña <input type="password" name="password" required autocomplete="current-password"></label>
<button class="btn" type="submit" style="width:100%">Iniciar sesión</button>
</form><p class="muted" style="margin-top:1.2rem">El administrador se crea desde la terminal con <code>php artisan sic:usuario</code>. El registro público está deshabilitado.</p></div></body></html>
