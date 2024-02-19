<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MembersExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return User::all();
    }

    public function map($user): array
    {
        return [
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->birthdate
        ];
    }

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Birthdate'
        ];
    }
}