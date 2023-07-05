<?php

namespace App\Exports;

use App\Models\Admin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InactiveAdminsExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    use Exportable;

    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function headings(): array
    {
        return [
            __('Admin ID'),
            __('Admin name'),
            __('Admin email'),
            __('Admin mobile'),
            __('Admin role'),
            __('Last login date'),
            __('Status'),
            __('Password block Status'),
        ];
    }

    public function map($admin): array
    {
        return [
            $admin->id,
            $admin->name,
            $admin->email,
            $admin->mobile,
            $admin->roles()->first()->name,
            $admin->last_login_at,
            $admin->is_active ? __('Active') : __('Unactive'),
            $admin->password_block ? __('Blocked') : __('Unblocked'),
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $inactiveAdmins = Admin::whereDate('last_login_at', '<=', $this->date)->orWhereNull('last_login_at');

        return $inactiveAdmins;
    }
}
