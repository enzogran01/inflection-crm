<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaAtuacao extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'icone',
        'cor',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
