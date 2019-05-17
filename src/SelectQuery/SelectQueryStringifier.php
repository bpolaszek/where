<?php

namespace BenTools\Where\SelectQuery;

final class SelectQueryStringifier
{

    /**
     * @param SelectQueryBuilder $query
     * @return array
     */
    private static function initBuild(SelectQueryBuilder $query): array
    {
        $parts = \array_merge([$query->mainKeyword], $query->flags);

        if (true === $query->distinct) {
            $parts[] = 'DISTINCT';
        }

        return $parts;
    }

    /**
     * @param SelectQueryBuilder $query
     * @param array              $parts
     */
    private static function buildColumns(SelectQueryBuilder $query, array &$parts)
    {
        $parts = \array_merge($parts, [\implode(', ', $query->columns)]);
    }

    /**
     * @param SelectQueryBuilder $query
     * @param array              $parts
     */
    private static function buildFrom(SelectQueryBuilder $query, array &$parts)
    {
        if (null !== $query->from) {
            $parts = \array_merge($parts, ['FROM', $query->from]);
        }
    }

    /**
     * @param SelectQueryBuilder $query
     * @param array              $parts
     */
    private static function buildJoins(SelectQueryBuilder $query, array &$parts)
    {
        if ([] !== $query->joins) {
            foreach ($query->joins as $table => $join) {
                $str = \sprintf('%s %s', $join['t'], $table);
                if (null !== $join['c']) {
                    $str .= \sprintf(' ON %s', $join['c']);
                }
                $parts[] = $str;
            }
        }
    }

    /**
     * @param SelectQueryBuilder $query
     * @param array              $parts
     */
    private static function buildWhere(SelectQueryBuilder $query, array &$parts)
    {
        if (null !== $query->where) {
            $parts = \array_merge($parts, ['WHERE', (string) $query->where]);
        }
    }

    /**
     * @param SelectQueryBuilder $query
     * @param array              $parts
     */
    private static function buildGroupBy(SelectQueryBuilder $query, array &$parts)
    {
        if ([] !== $query->groupBy) {
            $parts = \array_merge($parts, ['GROUP BY'], [implode(', ', $query->groupBy)]);
        }
    }

    /**
     * @param SelectQueryBuilder $query
     * @param array              $parts
     */
    private static function buildHaving(SelectQueryBuilder $query, array &$parts)
    {
        if (null !== $query->having) {
            $parts = \array_merge($parts, ['HAVING', (string) $query->having]);
        }
    }

    /**
     * @param SelectQueryBuilder $query
     * @param array              $parts
     */
    private static function buildOrderBy(SelectQueryBuilder $query, array &$parts)
    {
        if ([] !== $query->orderBy) {
            $parts = \array_merge($parts, ['ORDER BY'], [implode(', ', $query->orderBy)]);
        }
    }

    /**
     * @param SelectQueryBuilder $query
     * @param array              $parts
     */
    private static function buildLimit(SelectQueryBuilder $query, array &$parts)
    {
        if (null !== $query->limit) {
            $parts[] = \sprintf('LIMIT %d', $query->limit);
        }
    }

    /**
     * @param SelectQueryBuilder $query
     * @param array              $parts
     */
    private static function buildOffset(SelectQueryBuilder $query, array &$parts)
    {
        if (null !== $query->offset) {
            $parts[] = \sprintf('OFFSET %d', $query->offset);
        }
    }

    /**
     * @param SelectQueryBuilder $query
     * @return string
     */
    public static function stringify(SelectQueryBuilder $query): string
    {
        $parts = self::initBuild($query);
        self::buildColumns($query, $parts);
        self::buildFrom($query, $parts);
        self::buildJoins($query, $parts);
        self::buildWhere($query, $parts);
        self::buildGroupBy($query, $parts);
        self::buildHaving($query, $parts);
        self::buildOrderBy($query, $parts);
        self::buildLimit($query, $parts);
        self::buildOffset($query, $parts);
        return \implode(' ', $parts) . $query->end;
    }
}
