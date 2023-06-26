<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Transacao;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Cliente extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'username',
        'senha',
        'numero_Conta',
        'saldo',
        'limite',
    ];

    protected $hidden = [
        'senha',
       // 'remember_token', para lembrar senha de usuario
    ];

    protected $casts = [
        'senha' => 'hashed',
    ];


    protected $passwordName = 'senha';
    
    public function transacoes()
    {
        return $this->hasMany(Transacao::class, 'cliente_id');
    }
}

