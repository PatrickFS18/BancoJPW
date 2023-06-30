<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;
use App\Models\acessos;
use Illuminate\Support\Facades\Session;

use Carbon\Carbon;

class LoginController extends Controller
{


    public function login(Request $request)
    {
        // Validação dos dados do formulário de login
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Cliente::where('username', $request->username)->first();


        if ($user && Hash::check($request->password, $user->senha)) {
            //registro de acesso (login)
            $acesso = new acessos();
            $acesso->cliente_id = $user->id;
            $acesso->data_login = Carbon::now();
            $acesso->save();

            Auth::guard('clientes')->login($user);
            
            //inserir id na sessao

            Session::put('userId', $user->id);

            return redirect()->route('home');
        } else {
            return redirect()->route('login')->withErrors(['login' => 'Credenciais inválidas. Por favor, tente novamente.']);
        }
    }
    public function logout()
    {
        $user = Auth::guard('clientes')->user();

        // Registrar o acesso de logout na tabela "acessos"
        acessos::where('cliente_id', $user->id)
        ->whereNull('data_logout')
        ->update(['data_logout' => now()]);


        Auth::guard('clientes')->logout();
        Session::forget('userId');
        return redirect()->route('login');
    }
}
/* // Buscar o usuário com base no nome de usuário fornecido
        
        $user = Cliente::where('username', $request->username)->first();

        // Verificar se o usuário existe e se a senha está correta
        if ($user) {
            if (Hash::check($request->password, $user->senha)) {
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
        } */