<?php

namespace App\Filters;

class CarAdvertFilter extends ApiFilter
{
    /**
     * Parámetros de query aceptados y sus operadores permitidos.
     *
     * Uso:  ?price[lte]=30000&make[like]=BMW&engineSpec[cv][gte]=200
     */
    protected $safeParams = [
        // ── Campos propios de car_advert ─────────────────────────────────────
        'title'  => ['like'],
        'type'   => ['eq'],
        'price'  => ['lte', 'gte'],
        'km'     => ['lte', 'gte'],
        'color'  => ['eq', 'like'],
        'year'   => ['lte', 'gte'],
        'region' => ['eq', 'like'],
        'city'   => ['eq', 'like'],
        'seller' => ['eq'],

        // ── Relacionales simples ──────────────────────────────────────────────
        'make'   => ['eq', 'like'],   // Marca  (model → make)
        'model'  => ['eq', 'like'],   // Modelo (model)
        'fuel'   => ['eq'],           // Tipo de combustible (engine)
        'engine' => ['eq', 'like'],   // Nombre del motor (engine)

        // ── Specs EAV (manejadas manualmente en el controlador) ───────────────
        'modelSpec'  => ['eq', 'lt', 'gt', 'lte', 'gte', 'like'],
        'engineSpec' => ['eq', 'lt', 'gt', 'lte', 'gte', 'like'],
    ];

    /**
     * Mapeo param → columna real (con puntos para indicar relaciones).
     * Los params EAV no se mapean aquí.
     */
    protected $columnMap = [
        // Propios
        'title'  => 'ad_title',
        'type'   => 'ad_type',
        'price'  => 'price',
        'km'     => 'kilometers',
        'color'  => 'car_color',
        'year'   => 'year_manufacture',
        'region' => 'region',
        'city'   => 'city',
        'seller' => 'seller_id',

        // Relacionales: <relacion>.<columna>
        'make'   => 'model.make.make_name',
        'model'  => 'model.model_name',
        'fuel'   => 'engine.fuel_type',
        'engine' => 'engine.engine_name',
    ];

    protected $operatorMap = [
        'eq'   => '=',
        'like' => 'like',
        'lt'   => '<',
        'gt'   => '>',
        'lte'  => '<=',
        'gte'  => '>=',
    ];
}
