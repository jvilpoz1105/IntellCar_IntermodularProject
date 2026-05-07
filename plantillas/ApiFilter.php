<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter
{
    protected $safeParams = [];
    protected $columnMap = [];
    protected $operatorMap = [];

    public function transform(Request $request)
    {
        $eloquentQuery = [];

        foreach ($this->safeParams as $param => $operators) {
            $queryValues = $request->query($param);

            if (!isset($queryValues)) {
                continue;
            }

            $column = $this->columnMap[$param] ?? $param;

            foreach ($operators as $operator) {
                if (isset($queryValues[$operator])) {
                    $value = $queryValues[$operator];

                    // Verificamos si el operador es 'like' para añadir los comodines %
                    if ($operator === 'like') {
                        $value = "%{$value}%";
                    }

                    // Añadimos al array usando [] para no sobrescribir
                    $eloquentQuery[] = [$column, $this->operatorMap[$operator], $value];
                }
            }
        }

        return $eloquentQuery;
    }

    public function newTransform(Request $request)
{
    $queryData = ['main' => [], 'relations' => []];

    foreach ($this->safeParams as $param => $operators) {
        // Saltamos 'spec' porque lo manejamos manualmente en el controlador
        if ($param === 'spec') continue;

        $queryValues = $request->query($param);

        if (!isset($queryValues)) {
            continue;
        }

        $column = $this->columnMap[$param] ?? $param;

        foreach ($operators as $operator) {
            if (isset($queryValues[$operator])) {
                $value = $queryValues[$operator];
                $sqlOperator = $this->operatorMap[$operator];

                if ($operator === 'like') {
                    $value = "%{$value}%";
                }

                // Si la columna mapeada tiene un punto, es una relación
                if (str_contains($column, '.')) {
                    $parts = explode('.', $column);
                    $columnName = array_pop($parts);
                    $relationPath = implode('.', $parts);

                    $queryData['relations'][$relationPath][] = [
                        'column' => $columnName,
                        'operator' => $sqlOperator,
                        'value' => $value,
                        'is_numeric' => in_array($operator, ['gt', 'gte', 'lt', 'lte'])
                    ];
                } else {
                    // Campos de la tabla principal (Titulo, Precio, etc.)
                    $queryData['main'][] = [$column, $sqlOperator, $value];
                }
            }
        }
    }

    return $queryData;
}
}