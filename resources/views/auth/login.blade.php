<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar · Car Wash Hernández</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/carwash.css') }}?v={{ filemtime(public_path('css/carwash.css')) }}">
    @if(is_file(public_path('img/logo-carwash.png')))
        <link rel="icon" type="image/png" href="{{ asset('img/logo-carwash.png') }}">
    @endif
</head>
<body class="login-page">
    <main class="login-card" aria-labelledby="login-title">
        <header class="login-heading">
            @if(is_file(public_path('img/logo-carwash.png')))
                <img class="login-logo" width="148" height="148" src="{{ asset('img/logo-carwash.png') }}" alt="Logo de Car Wash Hernández">
            @else
                <span class="brand-symbol">CH</span>
            @endif
            <h1 id="login-title">Car Wash <span>Hernández</span></h1>
            <p>Sistema Contable y Evaluación de Inversiones</p>
        </header>

        <div class="login-divider" aria-hidden="true"><x-ui-icon name="wash" /></div>

        @if($errors->any())
            <div class="banner danger" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('entrar') }}">
            @csrf
            <div class="login-field">
                <label class="visually-hidden" for="login-email">Correo electrónico</label>
                <x-ui-icon name="mail" class="login-field-icon" />
                <input id="login-email" type="email" name="email" value="{{ old('email') }}" placeholder="Correo electrónico" title="Por ejemplo, correo@ejemplo.com" required autofocus autocomplete="username">
            </div>

            <div class="login-field">
                <label class="visually-hidden" for="login-password">Contraseña</label>
                <x-ui-icon name="lock" class="login-field-icon" />
                <input id="login-password" type="password" name="password" placeholder="Contraseña" required autocomplete="current-password">
                <button class="login-password-toggle" type="button" aria-label="Mostrar contraseña" aria-controls="login-password" aria-pressed="false" hidden>
                    <x-ui-icon name="eye-off" class="password-hidden-icon" />
                    <x-ui-icon name="eye" class="password-visible-icon" />
                </button>
            </div>

            <button class="login-submit" type="submit"><x-ui-icon name="sign-in" /> Iniciar sesión</button>
        </form>

        <p class="login-help">El administrador se crea desde la terminal con <code>php artisan sic:usuario</code>. El registro público está deshabilitado.</p>
        <div class="wash-wave" aria-hidden="true"></div>
    </main>
    <script>
        const passwordToggle = document.querySelector('.login-password-toggle');
        const passwordInput = document.getElementById(passwordToggle.getAttribute('aria-controls'));
        passwordToggle.hidden = false;
        passwordToggle.addEventListener('click', () => {
            const show = passwordInput.type === 'password';
            passwordInput.type = show ? 'text' : 'password';
            passwordToggle.setAttribute('aria-pressed', String(show));
            passwordToggle.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    </script>
</body>
</html>
