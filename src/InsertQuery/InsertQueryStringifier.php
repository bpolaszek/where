<?php
declare(strict_types=1);

namespace BenTools\Where\InsertQuery;

class InsertQueryStringifier
{

    /**
     * @param InsertQueryBuilder $query
     * @return array
     */
    private static function initBuild(InsertQueryBuilder $query): array
    {
        $parts = array_merge([$query->mainKeyword], $query->flags);
        return $parts;
    }

    /**
     * @param InsertQueryBuilder $insertQuery
     * @param array              $parts
     */
    private static function buildTable(InsertQueryBuilder $insertQuery, array &$parts)
    {
        if (null !== $insertQuery->table) {
            $parts[] = sprintf('INTO %s', $insertQuery->escape($insertQuery->table));
        }
    }

    /**
     * @param InsertQueryBuilder $insertQuery
     * @param array              $parts
     */
    private static function buildColumns(InsertQueryBuilder $insertQuery, array &$parts)
    {
        $columns = $insertQuery->getColumns();
        if (null !== $columns) {
            $parts[] = sprintf('(%s)', implode(', ', array_map([$insertQuery, 'escape'], $columns)));
        }
    }

    /**
     * @param InsertQueryBuilder $insertQuery
     * @param array              $parts
     */
    private static function buildValues(InsertQueryBuilder $insertQuery, array &$parts)
    {
        $columns = $insertQuery->getColumns();

        $parts[] = 'VALUES';

        $nbColumns = count($columns);
        $nbValues = count($insertQuery->values);
        $pattern = sprintf('(%s)', implode(', ', array_fill(0, $nbColumns, '?')));
        $valueParts = array_fill(0, $nbValues, $pattern);

        $parts[] = implode(', ', $valueParts);
    }

    /**
     * @param InsertQueryBuilder $insertQuery
     * @param array              $parts
     */
    private static function buildDuplicateConditions(InsertQueryBuilder $insertQuery, array &$parts)
    {
        $duplicateConditions = $insertQuery->onDuplicate;
        if ([] !== $duplicateConditions && null !== $duplicateConditions) {
            $parts[] = 'ON DUPLICATE KEY UPDATE';
            $updateParts = [];
            array_walk($duplicateConditions, function ($value, $key) use ($insertQuery, &$updateParts) {
                $updateParts[] = sprintf('%s = %s', $insertQuery->escape($key), $value);
            });

            $parts[] = implode(', ', $updateParts);
        }
    }

    /**
     * @param InsertQueryBuilder $insertQuery
     * @return string
     */
    public static function stringify(InsertQueryBuilder $insertQuery): string
    {
        $parts = self::initBuild($insertQuery);
        self::buildTable($insertQuery, $parts);
        self::buildColumns($insertQuery, $parts);
        self::buildValues($insertQuery, $parts);
        self::buildDuplicateConditions($insertQuery, $parts);
        return implode(' ', $parts) . $insertQuery->end;
    }
}
