<?php

namespace App\Http\Controllers;

use App\Models\ChavePix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transacao;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;




class PaymentsController extends Controller
{
    public function inserirChavePix(Request $request)
    {
        $request->validate([
            'chave_pix' => 'required|string',
            'userId' => 'required',

        ]);
        $userId= $request->userId;
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
    public function verificarPix(request $request)
    {
        $request->validate([
            'user' => 'required',
            'metodo' => 'required',
            'chavePix' => 'required',
            'valor' => 'required|numeric',
        ]);
        // Os dados foram validados com sucesso
        $user = $request->input('user');

        $metodoPagamento = $request->input('metodo');
        $chavePix = $request->input('chavePix');
        $valorDoPagamento = $request->input('valor');
        $chavePixDestino = ChavePix::where('chave', $chavePix)->first();

        if ($chavePixDestino) {
            $clienteDestino = Cliente::find($chavePixDestino->cliente_id);
            return view('confirmar-pagamento')->with([
                'user'=>$user,
                'metodoPagamento' => $metodoPagamento,
                'chavePix' => $chavePix,
                'valorDoPagamento' => $valorDoPagamento,
                'chavePixDestino' => $chavePixDestino,
                'clienteDestino' => $clienteDestino,
            ]);
        }else{
            return back()->with('errors', 'Chave pix não encontrada.');
        }
    }
    public function pagamentoPix(request $request)
    {

        $request->validate([
            'userId' => 'required',
            'metodo' => 'required',
            'chavePix' => 'required',
            'valor' => 'required|numeric',
        ]);
        // Os dados foram validados com sucesso
        $userId = $request->input('userId');
        $userId=intval($userId);
        $metodoPagamento = $request->input('metodo');
        $chavePix = $request->input('chavePix');
        $valorDoPagamento = $request->input('valor');

        // Obter o cliente logado
        $cliente = Cliente::find($userId);
        // Verificar se o cliente existe
        if (!$cliente) {


            return redirect()->back()->with('errors', 'Cliente não encontrado.');
        }

        // Verificar se o valor do pagamento é válido
        if ($valorDoPagamento <= 0) {
            return redirect()->back()->with('errors', 'Valor do pagamento inválido.');
        }

        // Verificar se o cliente possui saldo suficiente para realizar o pagamento
        if ($valorDoPagamento > $cliente->saldo) {

            // Verificar se o cliente pode usar o limite
            if ($cliente->limite <= 0 || $cliente->limite < ($valorDoPagamento - $cliente->saldo)) {

                return redirect()->back()->with('errors', 'Saldo insuficiente para realizar o pagamento e não é possível utilizar o limite.');
            }

            // Calcular o valor a ser utilizado do limite
            $limiteUtilizado = $valorDoPagamento - $cliente->saldo;
            $taxa = $limiteUtilizado * 0.01;
            $valorTaxado = $taxa + $limiteUtilizado;
            // Verificar se a taxa excede o limite disponível
            if ($valorTaxado > (($cliente->limite) + ($cliente->saldo))) {

                return redirect()->back()->with('errors', 'Saldo insuficiente para cobrir a taxa de 1% sobre o limite utilizado.');
            }
            $chavePixDestino = ChavePix::where('chave', $chavePix)->first();

            if (!$chavePixDestino) {
                return redirect()->back()->with('errors', 'Não foi possível encontrar um destinatário com a chave Pix informada.');
            }

            // Atualizar o saldo do cliente destino
            $clienteDestino = Cliente::find($chavePixDestino->cliente_id);
            if ($clienteDestino && $chavePixDestino->cliente_id == $userId) {
                return redirect()->back()->with('errors', 'Você não pode efetuar um pagamento a si mesmo.');
            }
            // Atualizar o saldo do cliente
            $cliente->saldo = 0;

            // Atualizar o limite do cliente
            $cliente->limite -= $valorTaxado;
            $cliente->save();
            $date = Carbon::now();
            if ($clienteDestino->limite < 1000) {
                // Calcular o valor que pode ser adicionado ao limite
                $valorLimite = min(1000 - $clienteDestino->limite, $valorDoPagamento);
                // Atualizar o limite do cliente recebedor
                $clienteDestino->limite += $valorLimite;
                // Atualizar o valor do pagamento com o valor restante
                $valorDoPagamento -= $valorLimite;
                // Atualizar o saldo do cliente recebedor com o valor restante
                $clienteDestino->saldo += $valorDoPagamento;
            } else {
                // Atualizar o saldo do cliente recebedor com o valor do pagamento
                $clienteDestino->saldo += $valorDoPagamento;
            }
            $clienteDestino->save();

            $transacao = new Transacao();
            $transacao->cliente_id = $cliente->id;
            $transacao->descricao = $metodoPagamento;
            $transacao->tipo = $metodoPagamento;
            $transacao->valor = $valorDoPagamento;
            $transacao->data = $date->format('Y-m-d H:i:s');
            $transacao->save();

            return redirect()->back()->with('warning', 'Pagamento efetuado, mas se liga! Você está utilizando parte do seu limite. Foi utilizado um valor de R$ ' . $limiteUtilizado . ' do seu limite de R$ ' . $cliente->limite . ' disponível.');
        }

        // Verificar se o cliente possui uma chave Pix registrada para receber o pagamento
        $chavePixDestino = ChavePix::where('chave', $chavePix)->first();

        if (!$chavePixDestino) {
            return redirect()->back()->with('errors', 'Não foi possível encontrar um destinatário com a chave Pix informada.');
        }

        // Atualizar o saldo do cliente destino
        $clienteDestino = Cliente::find($chavePixDestino->cliente_id);
        if ($clienteDestino && $chavePixDestino->cliente_id == $userId) {
            return redirect()->back()->with('errors', 'Você não pode efetuar um pagamento a si mesmo.');
        }

        if ($clienteDestino->limite < 1000) {
            // Calcular o valor que pode ser adicionado ao limite
            $valorLimite = min(1000 - $clienteDestino->limite, $valorDoPagamento);
            // Atualizar o limite do cliente recebedor
            $clienteDestino->limite += $valorLimite;
            // Atualizar o valor do pagamento com o valor restante
            $valorDoPagamento -= $valorLimite;
            // Atualizar o saldo do cliente recebedor com o valor restante
            $clienteDestino->saldo += $valorDoPagamento;
        } else {
            // Atualizar o saldo do cliente recebedor com o valor do pagamento
            $clienteDestino->saldo += $valorDoPagamento;
        }
        $clienteDestino->save();

        // Atualizar o saldo do cliente pagador
        $cliente->saldo -= $valorDoPagamento;
        $cliente->save();
        $date = Carbon::now();

        $transacao = new Transacao();
        $transacao->cliente_id = $cliente->id;
        $transacao->descricao = $metodoPagamento;
        $transacao->tipo = $metodoPagamento;
        $transacao->valor = $valorDoPagamento;
        $transacao->data = $date->format('Y-m-d H:i:s');
        $transacao->save();

        return redirect()->route('pagamentos')->with('success', 'Pagamento realizado com sucesso!');
    }



    public function transferir(request $request)
    {
        $request->validate([
            'Transferencia' => 'required',
            'numeroConta' => 'required',
            'userId' => 'required',
            'valor' => 'required|numeric',
        ]);

        // Os dados foram validados com sucesso

        $metodoPagamento = $request->input('Transferencia');
        $numeroConta = $request->input('numeroConta');
        $valorDoPagamento = $request->input('valor');



        // Verificar se o número da conta foi informado
        if (!$numeroConta) {
            return redirect()->back()->with('errors', 'Número da conta não informado.');
        }

        // Buscar o cliente pelo número da conta
        $clienteFinal = Cliente::where('numero_Conta', $numeroConta)->first();


        // Obter o cliente logado
        $clienteId = $request->input('userId');

        $cliente = Cliente::find($clienteId);
        // Verificar se o cliente existe
        if (!$clienteFinal) {
            return redirect()->back()->with('errors', 'Número de conta não encontrado/inválido.');
        }

        // Verificar se o valor do pagamento é válido
        if ($valorDoPagamento <= 0) {
            return redirect()->back()->with('errors', 'Valor do pagamento inválido.');
        }

        // Verificar se o cliente possui saldo suficiente para realizar o pagamento
        if ($valorDoPagamento > $cliente->saldo) {


            // Verificar se o cliente pode usar o limite
            if ($cliente->limite <= 0 || $cliente->limite < ($valorDoPagamento - $cliente->saldo)) {
                $errorMessage = 'Saldo e limite insuficiente para realizar o pagamento.';

                return redirect()->back()->with('errors', $errorMessage);
            }

            // Calcular o valor a ser utilizado do limite
            $limiteUtilizado = $valorDoPagamento - $cliente->saldo;
            $taxa = $limiteUtilizado * 0.01;
            $valorTaxado = $taxa + $limiteUtilizado;

            // Verificar se a taxa excede o limite disponível
            if ($valorTaxado > ($cliente->limite + $cliente->saldo)) {
                return redirect()->back()->with('errors', 'Saldo insuficiente para cobrir a taxa de 1% sobre o limite utilizado.');
            }

            // Atualizar o saldo do cliente
            $cliente->saldo = 0;

            // Atualizar o limite do cliente
            $cliente->limite -= $valorTaxado;
            $cliente->save();
            $date = Carbon::now();

            $transacao = new Transacao();
            $transacao->cliente_id = $cliente->id;
            $transacao->descricao = $metodoPagamento;
            $transacao->tipo = $metodoPagamento;
            $transacao->valor = $valorDoPagamento;
            $transacao->data = $date->format('Y-m-d H:i:s');
            $transacao->save();
            return redirect()->back()->with('warning', 'Você está utilizando parte do seu limite. Foi utilizado um valor de R$ ' . $limiteUtilizado . ' do seu limite de R$ ' . $cliente->limite . ' disponível.');
        }

        // Verificar se o cliente possui uma conta bancária registrada para transferência
        if (!$cliente->numero_Conta) {
            return redirect()->back()->with('errors', 'Não foi possível encontrar uma conta bancária registrada.');
        }



        // Atualizar o saldo do cliente pagador
        $cliente->saldo -= $valorDoPagamento;
        $cliente->save();
        $date = Carbon::now();

        // Verificar se o cliente recebedor possui um limite abaixo de 1000
        if ($clienteFinal->limite < 1000) {
            // Calcular o valor que pode ser adicionado ao limite
            $valorLimite = min(1000 - $clienteFinal->limite, $valorDoPagamento);
            // Atualizar o limite do cliente recebedor
            $clienteFinal->limite += $valorLimite;
            // Atualizar o valor do pagamento com o valor restante
            $valorDoPagamento -= $valorLimite;
            // Atualizar o saldo do cliente recebedor com o valor restante
            $clienteFinal->saldo += $valorDoPagamento;
        } else {
            // Atualizar o saldo do cliente recebedor com o valor do pagamento
            $clienteFinal->saldo += $valorDoPagamento;
        }

        $clienteFinal->save();
        // Registrar a transação
        $transacao = new Transacao();
        $transacao->cliente_id = $cliente->id;
        $transacao->descricao = 'Pagamento por: ' . $metodoPagamento;
        $transacao->tipo = $metodoPagamento;
        $transacao->valor = $valorDoPagamento;
        $transacao->data = $date->format('Y-m-d H:i:s');
        $transacao->save();

        return redirect()->route('pagamentos')->with('success', 'Pagamento realizado com sucesso!');
    }
}