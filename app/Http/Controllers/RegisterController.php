<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    public function register(Request $request)
    {

        $request->validate([
            'nome' => 'required|string',
            'username' => 'required|string|unique:clientes',
            'password_confirmation' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $cliente = new Cliente();
        $cliente->nome = $request->nome;
        $cliente->username = $request->username;
        $cliente->senha =  Hash::make($request->newPassword);
        $cliente->numero_Conta = $this->gerarNumeroContaAleatorio(); // Função para gerar o número de conta aleatório
        $cliente->saldo = 150;
        $cliente->limite = 1000;
        $cliente->save();

        return back()->with('success', 'Registro realizado com sucesso! Faça login para continuar.');
    }


    private function gerarNumeroContaAleatorio()
    {
        $numeroConta = '';

        // Gerar um número de conta aleatório de 8 dígitos
        for ($i = 0; $i < 8; $i++) {
            $numeroConta .= rand(0, 9);
        }

        // Verificar se o número de conta já existe na tabela
        $existente = DB::table('clientes')->where('numero_Conta', $numeroConta)->exists();

        // Se o número de conta já existir, gerar um novo número
        if ($existente) {
            return $this->gerarNumeroContaAleatorio();
        }

        return $numeroConta;
    }
}
