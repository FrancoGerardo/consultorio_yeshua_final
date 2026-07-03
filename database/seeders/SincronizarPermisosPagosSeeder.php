<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Idempotente: seguro en producción sin borrar datos.
 * Crea permisos de pagos y los asigna a Administrador y Secretaria.
 */
class SincronizarPermisosPagosSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $nombres = [
            'gestionar-pagos',
            'ver-pagos',
            'crear-pagos',
            'editar-pagos',
            'eliminar-pagos',
            'mostrar-pagos',
        ];

        $permisos = collect($nombres)->map(
            fn (string $nombre) => Permission::firstOrCreate([
                'name' => $nombre,
                'guard_name' => 'web',
            ])
        );

        $rolAdministrador = Role::findByName('Administrador', 'web');
        $rolAdministrador->givePermissionTo($permisos);

        $rolSecretaria = Role::findByName('Secretaria', 'web');
        $rolSecretaria->givePermissionTo(
            $permisos->whereIn('name', ['gestionar-pagos', 'ver-pagos', 'mostrar-pagos'])
        );

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
