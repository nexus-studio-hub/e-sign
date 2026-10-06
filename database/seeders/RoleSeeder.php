<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'PIC Development', 'level' => 1],
            ['name' => 'Group Leader', 'level' => 2],
            ['name' => 'Manager', 'level' => 3],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
