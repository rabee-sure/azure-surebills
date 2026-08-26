<?php

namespace App\Exports;

use App\Models\Admin;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActiveAdminsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Mobile',
            'Status',
            'Roles',
            'Last Login At',
            'Last Password Change At',
            'Created At',
        ];
    }

    /**
     * @param  \App\Models\Admin  $admin
     * @return array
     */
    public function map($admin): array
    {
        return [
            $admin->id,
            $admin->name,
            $admin->email,
            $admin->mobile,
            $admin->is_active ? 'Active' : 'Inactive',
            $admin->roles->pluck('name')->filter()->implode(', '),
            $admin->last_login_at,
            $admin->last_change_password_at,
            $admin->created_at,
        ];
    }

    /**
     * Active admins are records with is_active = true.
     * Soft-deleted admins are excluded by the Admin model's SoftDeletes trait.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return Admin::query()
            ->where('is_active', true)
            ->with(['roles.permissions', 'permissions'])
            ->orderBy('id');
    }
}
