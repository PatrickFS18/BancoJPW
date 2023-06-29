<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class ChavePix extends Model
{
    use HasFactory;

    protected $table = 'chaves_pix';

    protected $fillable = [
        'cliente_id',
        'Chave',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}