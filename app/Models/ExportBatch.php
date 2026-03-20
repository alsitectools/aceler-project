<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ExportBatch extends Model
{
    protected $table = 'export_batches';
    public $timestamps = false;
    protected $fillable = ['file_name','created_by','created_at'];
}
