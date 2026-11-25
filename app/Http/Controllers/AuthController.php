<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller {
 public function form(){if(Auth::check())return redirect()->route('dashboard');return view('auth.login');}
 public function entrar(Request $r){
   $data=$r->validate(['email'=>'required|email','password'=>'required|string']);
   $key='login:'.strtolower($r->input('email')).'|'.$r->ip();
   if(RateLimiter::tooManyAttempts($key,5))throw ValidationException::withMessages(['email'=>'Demasiados intentos. Intente nuevamente en '.RateLimiter::availableIn($key).' segundos.']);
   if(!Auth::attempt($data)){
      RateLimiter::hit($key,60);
      throw ValidationException::withMessages(['email'=>'Correo o contraseña incorrectos.']);
   }
   RateLimiter::clear($key);$r->session()->regenerate();
   return redirect()->intended(route('dashboard'));
 }
 public function salir(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('login');}
}
