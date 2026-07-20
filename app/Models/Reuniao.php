<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reuniao extends Model
{
    protected $table = 'reunioes';

    protected $fillable = [
        'titulo',
        'descricao',
        'inicio',
        'fim',
        'status',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim' => 'datetime',
    ];

    /**
     * Os usuários associados diretamente a esta reunião.
     */
    public function participantes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'reuniao_user');
    }

    /**
     * Os cargos que podem visualizar esta reunião.
     */
    public function cargos(): BelongsToMany
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'reuniao_role', 'reuniao_id', 'role_id');
    }
}
