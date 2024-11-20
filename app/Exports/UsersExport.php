<?php

namespace App\Exports;

use App\Models\AllOntStatsModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\FromArray;
ini_set('memory_limit', '256M');

class UsersExport implements FromCollection , WithHeadings
{
    protected $query;
 
    public function __construct($query)
    {
        $this->query = $query;
    }
 
    public function collection()
    {
        return AllOntStatsModel::whereRaw($this->query)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'OLT',
            'Type',
            'NAME',
            'USER',
            'Pon Port',
            'Onu Mac',
            'Onu Status',
            'Reason',
            'Distance',
            'Dbm RX',
            'Last Update'
        ];
    }
}
