<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class TimeEntriesExport implements FromCollection, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $date_from;
    private $date_to;
    private $user;
    private $collection;

    public function __construct($date_from, $date_to, $user)
    {
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->user = $user;

        $array = excelExport($date_from,$date_to,$user);

        // get headers for current dataset
        $output[] = array_keys($array[0]);
        // store values for each row
        foreach ($array as $row) {
            $output[] = array_values($row);
        }

        $this->collection = collect($output);
    }

    public function collection()
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->user->name.' - '.$this->user->employee_id;
    }
}
