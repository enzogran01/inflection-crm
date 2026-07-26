<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TarefaUser extends Pivot
{
    protected $table = 'tarefa_user';
}
