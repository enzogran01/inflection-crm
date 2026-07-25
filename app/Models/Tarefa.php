<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarefa extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'prioridade',
        'prazo',
        'status',
        'position',
    ];

    public function responsaveis()
    {
        return $this->belongsToMany(User::class, 'tarefa_user');
    }

    public function cargos()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'cargo_tarefa', 'tarefa_id', 'role_id');
    }
}
