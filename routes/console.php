<?php
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
Artisan::command('sic:usuario {email} {name=Administrador}', function (string $email, string $name) {
    $pwd = $this->secret('Contraseña del usuario (mínimo 8 caracteres)');
    if (strlen((string)$pwd) < 8) { $this->error('Contraseña demasiado corta'); return 1; }
    User::updateOrCreate(['email'=>$email], ['name'=>$name, 'password'=>Hash::make($pwd)]);
    $this->info('Usuario creado/actualizado: '.$email);
    return 0;
})->purpose('Crear o actualizar usuario administrador interactivo');
