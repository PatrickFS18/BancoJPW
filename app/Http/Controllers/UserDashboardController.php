<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transacao;
use Illuminate\Support\Facades\Session;

class UserDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::guard('clientes')->user();
        return view('indexUser', ['user' => $user]);
    }
    public function pagamentos()
    {
        $user = Auth::guard('clientes')->user();
        return view('pagamentos', ['user' => $user]);
    }
    public function transferencia()
    {
        $user = Auth::guard('clientes')->user();
        return view('transferencia', ['user' => $user]);
    }
    public function extrato()
    {
        $user = Auth::guard('clientes')->user();

        // Obter o ID do cliente logado
        $clienteId = Session::get('userId');

        // Buscar as transações do cliente pelo ID
        $transacoes = Transacao::where('cliente_id', $clienteId)->orderBy('data', 'desc')->get();
        
        return view('extrato', ['transacoes'=>$transacoes , 'user'=>$user ]);
    }
}
