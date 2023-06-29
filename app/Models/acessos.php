<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class acessos extends Model
{
    use HasFactory;

    protected $table = 'acessos';

    protected $fillable = [
        'cliente_id',
        'data_login',
        'data_logout',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    public $timestamps = false;

}