<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ExportLedgerLine extends Model
{
    protected $table = 'export_ledger_lines';
    public $timestamps = false;
    protected $fillable = [
        'batch_id','project_id','milestone_id','employee_number',
        'empresa','delegacion','masterobrasid','ref','op',
        'hours_decimal','puntos','hr_decimal','created_at'
    ];
}
