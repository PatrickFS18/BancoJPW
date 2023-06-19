<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transacao;
class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'Nome',
        'Username',
        'Senha',
        'Numero_Conta',
        'Saldo',
        'Limite',
    ];

    public function transacoes()
    {
        return $this->hasMany(Transacao::class, 'cliente_id');
    }
}