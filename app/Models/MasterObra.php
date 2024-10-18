<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterObra extends Model
{
    protected $fillable = [
        'ref_mo',
        'name',
        'business_unit',
        'status',
        'potential_customer_id',
        'project_id'
    ];

    // Relación hasMany entre MasterObra y ClientsMo
    public function clients()
    {
        return $this->hasMany(ClientsMo::class, 'ref_mo', 'ref_mo');
    }
    // Función para obtener los clientes con información adicional de potential_clients
    public function getClients()
    {
        return $this->clients()
            ->join('potential_clients', 'potential_clients.potential_customer_id', '=', 'clients_mos.potential_customer_id')
            ->get(['clients_mos.potential_customer_id', 'potential_clients.name']);
    }
}
