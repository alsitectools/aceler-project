<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class projectsExport implements FromCollection, WithHeadings
{

    public function collection()
    {
        $data = Project::where('workspace', \Auth::user()->currant_workspace)->get();
        foreach ($data as $k => $Projects) {
            unset($Projects->created_by, $Projects->is_active, $Projects->id);


            $data[$k]["name"]           = $Projects->name;
            $data[$k]["ref_mo"]           = $Projects->ref_mo;
            $data[$k]["type"]           = $Projects->type;
            $data[$k]["status"]          = $Projects->status;
            $data[$k]["start_date"]       = $Projects->start_date;
            $data[$k]["end_date"]         = $Projects->end_date;
            $data[$k]["budget"]          =  $Projects->budget;
            $data[$k]["workspace"]       = $Projects->workspace;
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "Name",
            "Ref_MO",
            'Type',
            'Status',
            "Start_date",
            "End_date",
            "Budget",
            "Workspace_id",
        ];
    }
}
