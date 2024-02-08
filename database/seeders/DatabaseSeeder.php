<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Service;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'example@email.com'
        ]);

        $role = Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'partner']);
        Role::create(['name' => 'user']);

        $user->assignRole($role);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Wi-fi',
            'icon' => 'wifi',
            'category' => 1,
            'order' => 1
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Pet friendly',
            'icon' => 'pets',
            'category' => 1,
            'order' => 2
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Area para trabajar',
            'icon' => 'fa_solid:laptop',
            'category' => 1,
            'order' => 3
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Smart TV',
            'icon' => 'tv',
            'category' => 1,
            'order' => 4
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Picina',
            'icon' => 'fa_solid:person-swimming',
            'category' => 1,
            'order' => 5
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Lavanderia',
            'icon' => 'mdi:washing-machine',
            'category' => 1,
            'order' => 6
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Secadora',
            'icon' => 'mdi:tumble-dryer',
            'category' => 1,
            'order' => 7
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Jacuzzi',
            'icon' => 'fa_solid:hot-tub-person',
            'category' => 2,
            'order' => 1
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Agua caliente',
            'icon' => 'fa_solid:shower',
            'category' => 2,
            'order' => 2
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Sauna',
            'icon' => 'fa_solid:shower',
            'category' => 2,
            'order' => 3
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Toallas',
            'icon' => 'fa_solid:toiler-paper',
            'category' => 2,
            'order' => 4
        ]);

        Service::create([
            'uuid' => Str::uuid(),
            'name' => 'Shampoo y jabón',
            'icon' => 'mat_solid:10k',
            'category' => 2,
            'order' => 5
        ]);
    }
}
