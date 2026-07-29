<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'inicio_contato',
        'contato',
    ];

    protected $casts = [
        'inicio_contato' => 'date',
    ];

    public function oportunidades(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Oportunidade::class);
    }
}
