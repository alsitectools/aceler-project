<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientsMo extends Model
{
    use HasFactory;
    protected $fillable = ['ref_mo', 'potential_customer_id'];

     // Relación belongsTo con MasterObra
     public function masterObra()
     {
         return $this->belongsTo(MasterObra::class, 'ref_mo', 'ref_mo');
     }
 
     // Relación belongsTo con PotentialClient si lo necesitas
     public function potentialClient()
     {
         return $this->belongsTo(PotentialClient::class, 'potential_customer_id', 'potential_customer_id');
     }
}
