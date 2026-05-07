<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter
{
    protected $safeParams = [];
    protected $columnMap = [];
    protected $operatorMap = [];

    /**
     * Transforma los query params en arrays separados de:
     *  - 'main'      → condiciones sobre la tabla propia del modelo
     *  - 'relations' → condiciones sobre relaciones (whereHas), agrupadas por path
     */
    public function newTransform(Request $request): array
    {
        $queryData = ['main' => [], 'relations' => []];

        foreach ($this->safeParams as $param => $operators) {
            // Los parámetros EAV (modelSpec / engineSpec) se gestionan en el controlador
            if (in_array($param, ['modelSpec', 'engineSpec'])) {
                continue;
            }

            $queryValues = $request->query($param);

            if (!isset($queryValues)) {
                continue;
            }

            $column = $this->columnMap[$param] ?? $param;

            foreach ($operators as $operator) {
                if (isset($queryValues[$operator])) {
                    $value      = $queryValues[$operator];
                    $sqlOperator = $this->operatorMap[$operator];

                    if ($operator === 'like') {
                        $value = "%{$value}%";
                    }

                    // Si la columna mapeada contiene un punto, es una relación anidada
                    if (str_contains($column, '.')) {
                        $parts      = explode('.', $column);
                        $columnName = array_pop($parts);
                        $relationPath = implode('.', $parts);

                        $queryData['relations'][$relationPath][] = [
                            'column'     => $columnName,
                            'operator'   => $sqlOperator,
                            'value'      => $value,
                            'is_numeric' => in_array($operator, ['gt', 'gte', 'lt', 'lte']),
                        ];
                    } else {
                        // Campo de la tabla principal
                        $queryData['main'][] = [$column, $sqlOperator, $value];
                    }
                }
            }
        }

        return $queryData;
    }
}
