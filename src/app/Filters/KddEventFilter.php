<?php

namespace App\Filters;

class KddEventFilter extends ApiFilter
{
    /**
     * Parámetros de query aceptados y sus operadores permitidos.
     *
     * Uso:  ?city[like]=Madrid&paddock[like]=drift&dateFrom[gte]=2025-01-01
     */
    protected $safeParams = [
        // ── Campos propios de event_kdd ───────────────────────────────────────
        'title'         => ['like'],
        'description'   => ['like'],
        'city'          => ['eq', 'like'],
        'location'      => ['like'],
        'dateFrom'      => ['gte'],          // eventos a partir de una fecha
        'dateTo'        => ['lte'],          // eventos hasta una fecha
        'maxSlots'      => ['gte', 'lte'],   // plazas máximas

        // ── Relacionales simples ──────────────────────────────────────────────
        'creator'  => ['eq', 'like'],  // username del creador (creator)
        'paddock'  => ['eq', 'like'],  // nombre del paddock/mood (paddock)
    ];

    protected $columnMap = [
        // Propios
        'title'       => 'title',
        'description' => 'event_description',
        'city'        => 'city',
        'location'    => 'location_name',
        'dateFrom'    => 'event_date',
        'dateTo'      => 'event_date',
        'maxSlots'    => 'max_participants',

        // Relacionales: <relacion>.<columna>
        'creator'  => 'creator.user_name',
        'paddock'  => 'paddock.name',
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
