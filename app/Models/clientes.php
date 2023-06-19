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
        'nome',
        'username',
        'senha',
        'numero_Conta',
        'saldo',
        'limite',
    ];

    public function transacoes()
    {
        return $this->hasMany(Transacao::class, 'cliente_id');
    }
}