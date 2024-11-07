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

    // Definir la relación con los clientes a través de la tabla intermedia ClientsMo
    public function clients()
    {
        return $this->hasManyThrough(
            PotentialClient::class,
            ClientsMo::class,
            'ref_mo',
            'potential_customer_id',
            'ref_mo',
            'potential_customer_id'
        );
    }

    public function getClients()
    {
        return $this->clients()
            ->join('clients_mos', 'clients_mos.potential_customer_id', '=', 'potential_clients.potential_customer_id')
            ->join('potential_clients', 'potential_clients.potential_customer_id', '=', 'clients_mos.potential_customer_id')
            ->select('potential_clients.potential_customer_id', 'potential_clients.name')
            ->get();
    }
}
