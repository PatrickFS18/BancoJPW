<?php

namespace App\Http\Controllers;

use App\Models\ChavePix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
