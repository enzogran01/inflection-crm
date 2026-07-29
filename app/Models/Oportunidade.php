<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oportunidade extends Model
{
    protected $fillable = [
        'cliente_id',
        'titulo',
        'descricao',
        'categoria',
        'status',
        'data_fechamento_esperada',
    ];

    protected $casts = [
        'data_fechamento_esperada' => 'date',
    ];

    public function cliente(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
