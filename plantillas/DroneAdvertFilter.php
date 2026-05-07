<?php

namespace App\Filters;

use Illuminate\Http\Request;

class DroneAdvertFilter extends ApiFilter
{
    protected $safeParams = [
        //Principales de Anuncios de Drones
        'Titulo' => ['like'],
        'Descripcion' => ['like'],
        'Precio' => ['lte', 'gte'],
        'Stock' => ['gte'],
        'Visible' => ['eq'],
        'Estado' => ['eq'],
        'ModeloDron' => ['eq'],
        'Proveedor' => ['eq', 'like'],

        //Relacionales
        'Vendedor'   => ['like', 'eq'],
        'Marca'      => ['eq'],
        'Etiqueta'   => ['eq'],
        'Sector'     => ['eq'],
        'spec' => ['eq', 'lt', 'gt', 'lte', 'gte', 'like'], // Especificación completa
    ];
    protected $columnMap = [
        'Titulo' => 'titulo',
        'Descripcion' => 'descripcion',
        'Precio' => 'precio',
        'Stock' => 'stock',
        'Visible' => 'visible',
        'Estado' => 'estado',
        'ModeloDron' => 'dron_id',
        'Proveedor' => 'vendedor_id',

        //Relacionales
        'Vendedor'   => 'vendedor.nombre',
        'Marca'      => 'dron.make.nombre',
        'Etiqueta'   => 'dron.etiquetas.nombre',
        'Sector'     => 'dron.etiquetas.sector',
       

    ];

    protected $operatorMap = [
        'eq' => '=',
        'like' => 'like',
        'lt' => '<',
        'gt' => '>',
        'lte' => '<=',
        'gte' => '>=',
    ];
}