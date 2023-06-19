<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;

class LoginController extends Controller
{
    
    
    public function login(Request $request)
    {
        
        // Validação dos dados do formulário de login
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $request['password']=bcrypt($request->password);

        // Buscar o usuário com base no nome de usuário fornecido
        $user = Cliente::where('username', $request->username)->first();
        // Verificar se o usuário existe e se a senha está correta
        if ($user) {
            if (password_verify($request->password, $user->senha)) {
                // Autenticação bem-sucedida
                Auth::login($user); // Fazer login do usuário
                return redirect()->route('home');
            } else {
                // Senha incorreta
                return redirect()->route('login')->withErrors(['login' => 'Credenciais inválidas. Por favor, tente novamente.']);
            }
        } else {

            // Usuário não encontrado
            return redirect()->route('login')->withErrors(['login' => 'Credenciais inválidas. Por favor, tente novamente.']);
        } 
        
    }
}
