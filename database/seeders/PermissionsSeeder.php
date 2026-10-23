<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'ver-dashboard',
            'ver-sensores',
            'gestionar-sensores',
            'ver-firebase',
            'gestionar-firebase',
            'ver-estadisticas',
            'ver-reportes',
            'gestionar-areas',
            'gestionar-cargos',
            'ver-trabajadores',
            'crear-trabajadores',
            'editar-trabajadores',
            'eliminar-trabajadores',
            'gestionar-incidentes',
            'gestionar-alertas',
            'control-grupal',
            'gestionar-solicitudes-herramientas',
            'ver-business-flow',
            'gestionar-usuarios',
            'gestionar-permisos',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $adminPrincipal = Role::findByName('administrador-principal');
        if ($adminPrincipal) {
            $adminPrincipal->syncPermissions(Permission::all());
        }

        $adminArea = Role::findByName('administrador-area');
        if ($adminArea) {
            $adminArea->syncPermissions([
                'ver-dashboard','ver-sensores','gestionar-sensores','ver-firebase','gestionar-firebase',
                'ver-estadisticas','ver-reportes','gestionar-areas','gestionar-cargos',
                'ver-trabajadores','crear-trabajadores','editar-trabajadores',
                'gestionar-incidentes','gestionar-alertas','control-grupal','ver-business-flow',
            ]);
        }

        $tecnico = Role::findByName('tecnico');
        if ($tecnico) {
            $tecnico->syncPermissions([
                'ver-dashboard','ver-sensores','gestionar-sensores','ver-reportes',
                'gestionar-incidentes','gestionar-alertas','ver-business-flow',
            ]);
        }

        $trabajador = Role::findByName('trabajador');
        if ($trabajador) {
            $trabajador->syncPermissions([
                'ver-dashboard','ver-business-flow',
            ]);
        }
    }
}
