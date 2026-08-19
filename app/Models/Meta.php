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
        'categoria',
        'tipo_meta_financeira',
        'valor_alvo',
        'periodicidade',
        'data_referencia',
    ];

    protected $casts = [
        'prazo' => 'date',
        'valor_alvo' => 'decimal:2',
        'data_referencia' => 'date',
    ];

    public function tarefas()
    {
        return $this->hasMany(Tarefa::class);
    }
}
