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
        'meta_id',
        'area_atuacao_id',
    ];

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function areaAtuacao()
    {
        return $this->belongsTo(AreaAtuacao::class);
    }

    public function getAreaAtuacaoNomeAttribute(): ?string
    {
        return $this->areaAtuacao?->nome;
    }

    public function getAreaAtuacaoCorAttribute(): ?string
    {
        return $this->areaAtuacao?->cor;
    }

    public function getAreaAtuacaoIconeAttribute(): ?string
    {
        return $this->areaAtuacao?->icone;
    }

    public function responsaveis()
    {
        return $this->belongsToMany(User::class, 'tarefa_user', 'tarefa_id', 'user_id')->using(TarefaUser::class);
    }

    public function getNomeResponsavelAttribute(): ?string
    {
        return $this->responsaveis->first()?->name;
    }

    public function cargos()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'cargo_tarefa', 'tarefa_id', 'role_id');
    }
}
