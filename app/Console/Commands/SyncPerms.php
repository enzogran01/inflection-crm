<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncPerms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-perms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $role = \Spatie\Permission\Models\Role::where('name', 'Administrador')->first();
        if ($role) {
            $permissions = \Spatie\Permission\Models\Permission::where('name', 'like', '%area::atuacao%')->get();
            $role->syncPermissions($role->permissions->merge($permissions));
            $this->info('Permissions synced!');
        } else {
            $this->error('Administrador role not found');
        }
    }
}
