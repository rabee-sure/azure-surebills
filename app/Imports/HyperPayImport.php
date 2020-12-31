<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HyperPayImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Country|null
     */
    public function model(array $row)
    {
        return $row;
    }
}