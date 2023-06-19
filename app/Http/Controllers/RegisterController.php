<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        
        // Validação dos dados do formulário de registro
        $request->validate([
            'nome' => 'required|string',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Criar um novo usuário
        $user = new User();
        $user->nome = $request->nome;
        $user->username = $request->username;
        $user->password = hash('sha256', $request->password); // Usando a função hash para gerar o hash da senha
        $user->save();

        // Redirecionar o usuário para a página de login ou qualquer outra ação desejada
        return redirect()->route('login')->with('success', 'Registro realizado com sucesso! Faça login para continuar.');
}
}