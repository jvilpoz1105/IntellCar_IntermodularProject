<?php

namespace App\Filters;

class PostFilter extends ApiFilter
{
    /**
     * Parámetros de query aceptados y sus operadores permitidos.
     *
     * Uso:  ?title[like]=BMW&make[like]=Audi&mood[eq]=drift
     */
    protected $safeParams = [
        // ── Campos propios de post ────────────────────────────────────────────
        'title'   => ['like'],
        'content' => ['like'],

        // ── Relacionales simples ──────────────────────────────────────────────
        'author'  => ['eq', 'like'],  // username del autor (author)
        'make'    => ['eq', 'like'],  // nombre de marca  (model → make)
        'model'   => ['eq', 'like'],  // nombre de modelo (model)
        'fuel'    => ['eq'],          // tipo combustible  (engine)
        'engine'  => ['eq', 'like'],  // nombre del motor  (engine)
        'mood'    => ['eq', 'like'],  // paddock/mood      (moods)

        // ── Specs EAV (manejadas manualmente en el controlador) ───────────────
        'modelSpec'  => ['eq', 'lt', 'gt', 'lte', 'gte', 'like'],
        'engineSpec' => ['eq', 'lt', 'gt', 'lte', 'gte', 'like'],
    ];

    protected $columnMap = [
        // Propios
        'title'   => 'title',
        'content' => 'content',

        // Relacionales: <relacion>.<columna>
        'author'  => 'author.username',
        'make'    => 'model.make.make_name',
        'model'   => 'model.model_name',
        'fuel'    => 'engine.fuel_type',
        'engine'  => 'engine.engine_name',
        'mood'    => 'moods.name',
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
