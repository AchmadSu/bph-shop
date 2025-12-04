<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $roles = ['admin', 'cs1', 'cs2', 'user'];
        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role],
                ['guard_name' => 'api']
            );
        }

        $admin = User::find(1);
        if ($admin && !$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
