<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class ForceAdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@sistema.com')->first();
        if ($admin) {
            $admin->syncRoles(['admin']); // Force sync to be sure
            $this->command->info('Role admin assigned to ' . $admin->email);
        } else {
            $this->command->error('User admin@sistema.com not found');
        }
    }
}
