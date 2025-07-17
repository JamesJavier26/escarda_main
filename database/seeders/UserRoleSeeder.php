<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = ['super-admin', 'admin', 'sales-manager','sales-representative','operation-manager','operation-staff','production-staff'];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // Create super-admin
        $superAdmin = User::create([
            'username' => 'papajroi', 
            'avatar' => '',
            'email' => 'zhanroi18@gmail.com', 
            'password' => Hash::make('1'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');

        // Create first admin
        $admin1 = User::create([
            'username' => 'restie',
            'avatar' => '',
            'email' => 'rbgapusan@gmail.com',
            'password' => Hash::make('1'),
            'email_verified_at' => now(),
        ]);
        $admin1->assignRole('super-admin');

        // Create second admin
        $admin2 = User::create([
            'username' => 'bossing',
            'avatar' => '',
            'email' => 'percivalescarda@gmail.com',
            'password' => Hash::make('1'),
            'email_verified_at' => now(),
        ]);
        $admin2->assignRole('admin');

        $admin3 = User::create([
            'username' => 'christienne',
             'avatar' => '',
            'email' => 'christienne@gmail.com',
            'password' => Hash::make('1'),
            'email_verified_at' => now(),
        ]);
        $admin3->assignRole('admin');

        // $this->command->info('Roles and permissions seeded successfully!');
        
        // $this->command->info('Super Admin User created:');
        // $this->command->warn('Email: zhanroi18@gmail.com'); 
        // $this->command->warn('Password: 1'); 
        
        // $this->command->info('\nAdmin Users created:');
        // $this->command->warn('Email: rbgapusan@gmail.com | Password: 1');
        // $this->command->warn('Email: percivalescarda@gmail.com | Password: 1');
    }
}
