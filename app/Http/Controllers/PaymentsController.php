<?php

namespace App\Http\Controllers;

use App\Models\ChavePix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transacao;
use App\Models\Cliente;

class PaymentsController extends Controller
{
    public function inserirChavePix(Request $request)
    {
        $request->validate([
            'chave_pix' => 'required|string',
        ]);

        $userId = $request['id'];
        $chavePix = $request->chave_pix;

        // Verifique se a chave Pix é um CPF, telefone ou e-mail
        if ($chavePix !== '' && (preg_match('/^[0-9]{11}$/', $chavePix) || preg_match('/^\+[0-9]{1,3}[0-9]{10,}$/', $chavePix) || filter_var($chavePix, FILTER_VALIDATE_EMAIL))) {
            // Verifique se a chave Pix já está cadastrada para outro usuário
            $outraChaveExistente = ChavePix::where('chave', $chavePix)
                ->where('cliente_id', '!=', $userId)
                ->first();

            if ($outraChaveExistente) {
                // Se a chave já estiver cadastrada para outro usuário, exiba uma mensagem de erro
                return redirect()->back()->withErrors(['chave_pix' => 'Esta chave Pix já está em uso por outro usuário.']);
            }

            // Verifique se o usuário já possui uma chave Pix cadastrada
            $chaveExistente = ChavePix::where('cliente_id', $userId)->first();

            if ($chaveExistente) {
                // Se o usuário já tiver uma chave cadastrada, atualize-a
                $chaveExistente->chave = $chavePix;
                $chaveExistente->save();
            } else {
                // Se o usuário não tiver uma chave cadastrada, crie uma nova
                $novaChave = new ChavePix();
                $novaChave->cliente_id = $userId;
                $novaChave->chave = $chavePix;
                $novaChave->save();
            }

            return redirect()->route('pagamentos')->with('success', 'Chave Pix cadastrada com sucesso!');
        } else {
            // Chave Pix inválida
            return redirect()->back()->withErrors(['chave_pix' => 'Chave Pix inválida. Por favor, insira um CPF, telefone ou e-mail válido.']);
        }
    }
    public function pagamentoPix(request $request)
    {        

        $metodoPagamento = $request->input('metodo');
        $chavePix = $request->input('numeroConta');
        $valorDoPagamento = $request->input('valor');

        // Obter o cliente logado
        $clienteId = $request['id'];
        $cliente = Cliente::find($clienteId);

        // Verificar se o cliente existe
        if (!$cliente) {
            return redirect()->back()->with('error', 'Cliente não encontrado.');
        }

        // Verificar se o valor do pagamento é válido
        if ($valorDoPagamento <= 0) {
            return redirect()->back()->with('error', 'Valor do pagamento inválido.');
        }

        // Verificar se o cliente possui saldo suficiente para realizar o pagamento
        if ($valorDoPagamento > $cliente->saldo) {

            // Verificar se o cliente pode usar o limite
            if ($cliente->limite <= 0 || $cliente->limite < ($valorDoPagamento - $cliente->saldo)) {

                return redirect()->back()->with('error', 'Saldo insuficiente para realizar o pagamento e não é possível utilizar o limite.');
            }

            // Calcular o valor a ser utilizado do limite
            $limiteUtilizado = $valorDoPagamento - $cliente->saldo;
            $taxa = $limiteUtilizado * 0.01;
            $valorTaxado = $taxa + $limiteUtilizado;
            // Verificar se a taxa excede o limite disponível
            if ($valorTaxado > (($cliente->limite) + ($cliente->saldo))) {

                return redirect()->back()->with('error', 'Saldo insuficiente para cobrir a taxa de 1% sobre o limite utilizado.');
            }

            // Atualizar o saldo do cliente
            $cliente->saldo = 0;

            // Atualizar o limite do cliente
            $cliente->limite -= $valorTaxado;
            $cliente->save();

            $transacao = new Transacao();
            $transacao->cliente_id = $cliente->id;
            $transacao->descricao = $metodoPagamento;
            $transacao->tipo = $metodoPagamento;
            $transacao->valor = $valorDoPagamento;
            $transacao->data = now();
            $transacao->save();

            return redirect()->back()->with('warning', 'Você está utilizando parte do seu limite. Foi utilizado um valor de R$ ' . $limiteUtilizado . ' do seu limite de R$ ' . $cliente->limite . ' disponível.');
        }

        // Verificar se o cliente possui uma chave Pix registrada para receber o pagamento
        $chavePixDestino = ChavePix::where('chave', $chavePix)->first();

        if (!$chavePixDestino) {
            return redirect()->back()->with('error', 'Não foi possível encontrar um destinatário com a chave Pix informada.');
        }

        // Atualizar o saldo do cliente destino
        $clienteDestino = Cliente::find($chavePixDestino->cliente_id);
        $clienteDestino->saldo += $valorDoPagamento;
        $clienteDestino->save();

        // Atualizar o saldo do cliente pagador
        $cliente->saldo -= $valorDoPagamento;
        $cliente->save();

        // Registrar a transação
        $transacao = new Transacao();
        $transacao->cliente_id = $cliente->id;
        $transacao->descricao = 'Pagamento por: ' . $metodoPagamento;
        $transacao->tipo = $metodoPagamento;
        $transacao->valor = $valorDoPagamento;
        $transacao->data = now();
        $transacao->save();

        return redirect()->route('pagamentos')->with('success', 'Pagamento realizado com sucesso!');
    }
    public function transferir(request $request)
    {
        $metodoPagamento = "Transferência";
        $numeroConta = $request->input('numeroConta');
        $valorDoPagamento = $request->input('valor');

        // Obter o cliente logado
        $clienteId = $request['id'];
        $cliente = Cliente::find($clienteId);

        // Verificar se o cliente existe
        if (!$cliente) {
            return redirect()->back()->with('error', 'Cliente não encontrado.');
        }

        // Verificar se o valor do pagamento é válido
        if ($valorDoPagamento <= 0) {
            return redirect()->back()->with('error', 'Valor do pagamento inválido.');
        }

        // Verificar se o cliente possui saldo suficiente para realizar o pagamento
        if ($valorDoPagamento > $cliente->saldo) {

            // Verificar se o cliente pode usar o limite
            if ($cliente->limite <= 0 || $cliente->limite < ($valorDoPagamento - $cliente->saldo)) {
                return redirect()->back()->with('error', 'Saldo insuficiente para realizar o pagamento e não é possível utilizar o limite.');
            }

            // Calcular o valor a ser utilizado do limite
            $limiteUtilizado = $valorDoPagamento - $cliente->saldo;
            $taxa = $limiteUtilizado * 0.01;
            $valorTaxado = $taxa + $limiteUtilizado;

            // Verificar se a taxa excede o limite disponível
            if ($valorTaxado > ($cliente->limite + $cliente->saldo)) {
                return redirect()->back()->with('error', 'Saldo insuficiente para cobrir a taxa de 1% sobre o limite utilizado.');
            }

            // Atualizar o saldo do cliente
            $cliente->saldo = 0;

            // Atualizar o limite do cliente
            $cliente->limite -= $valorTaxado;
            $cliente->save();

            $transacao = new Transacao();
            $transacao->cliente_id = $cliente->id;
            $transacao->descricao = $metodoPagamento;
            $transacao->tipo = $metodoPagamento;
            $transacao->valor = $valorDoPagamento;
            $transacao->data = now();
            $transacao->save();

            return redirect()->back()->with('warning', 'Você está utilizando parte do seu limite. Foi utilizado um valor de R$ ' . $limiteUtilizado . ' do seu limite de R$ ' . $cliente->limite . ' disponível.');
        }

        // Verificar se o cliente possui uma conta bancária registrada para transferência
        if (!$cliente->numero_conta) {
            return redirect()->back()->with('error', 'Não foi possível encontrar uma conta bancária registrada.');
        }

        // Verificar se o número da conta informado é válido
        if ($numeroConta !== $cliente->numero_conta) {
            return redirect()->back()->with('error', 'Número de conta inválido.');
        }

        // Atualizar o saldo do cliente pagador
        $cliente->saldo -= $valorDoPagamento;
        $cliente->save();

        // Registrar a transação
        $transacao = new Transacao();
        $transacao->cliente_id = $cliente->id;
        $transacao->descricao = 'Pagamento por: ' . $metodoPagamento;
        $transacao->tipo = $metodoPagamento;
        $transacao->valor = $valorDoPagamento;
        $transacao->data = now();
        $transacao->save();

        return redirect()->route('pagamentos')->with('success', 'Pagamento realizado com sucesso!');
    }
}
