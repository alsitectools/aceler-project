<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PotentialClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'potential_customer_id',
        'customer_id'
    ];

    public function clientsMo()
    {
        return $this->hasMany(ClientsMo::class, 'potential_customer_id', 'potential_customer_id');
    }
    // Relación con la tabla intermedia ClientsMo para obtener las obras asociadas
    public function obras()
    {
        return $this->hasManyThrough(
            MasterObra::class,
            ClientsMo::class,
            'potential_customer_id', 
            'ref_mo',
            'potential_customer_id',
            'ref_mo'
        );
    }
}
