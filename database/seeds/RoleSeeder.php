<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        try{
    
            Role::create([
                'name' => 'admin'
            ]);
            Role::create([
                'name' => 'guest'
            ]);
            Role::create([
                'name' => 'colaborador'
            ]);


            Permission::create([
                'name' => 'crear usuarios'
            ]);

            Permission::create([
                'name' => 'ver usuarios'
            ]);

            Permission::create([
                'name' => 'editar usuarios'
            ]);

            Permission::create([
                'name' => 'eliminar usuario'
            ]);

            //setting permissions to admin role
            $allPerms = Permission::all();
            $adminRole = Role::where('name', 'admin')->first();
            $adminRole->givePermissionTo($allPerms);

            $guestRole = Role::where('name', 'guest')->first();
            $guestRole->givePermissionTo('ver usuarios');
        } catch(Exception $e){
            
        }
        
    }
}
