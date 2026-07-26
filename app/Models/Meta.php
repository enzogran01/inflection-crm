<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'prazo',
        'status',
    ];

    public function tarefas()
    {
        return $this->hasMany(Tarefa::class);
    }
}
