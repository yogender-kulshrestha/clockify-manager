<?php

namespace App\Exports;

use App\Exports\Sheets\TimeEntriesExport;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TimeSheetExport implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    private $date_from;
    private $date_to;
    private $user_id;

    public function __construct($date_from, $date_to, $user_id=null)
    {
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->user_id = $user_id;
    }

    public function sheets(): array
    {
        $sheets = [];

        if(!empty($this->user_id)) {
            $user=User::where('clockify_id', $this->user_id)->first();
            $sheets[] = new TimeEntriesExport($this->date_from, $this->date_to, $user);
        } else {
            $users=User::where('role', 'user')->where('status', 'active')->orderBy('id')->get();
            foreach($users as $user) {
                $sheets[] = new TimeEntriesExport($this->date_from, $this->date_to, $user);
            }
        }

        return $sheets;
    }
}
