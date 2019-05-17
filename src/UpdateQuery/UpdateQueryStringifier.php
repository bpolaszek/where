<?php

namespace BenTools\Where\UpdateQuery;

class UpdateQueryStringifier
{

    private static function initBuild(UpdateQueryBuilder $query): array
    {
        return \array_merge([$query->mainKeyword], $query->flags);
    }

    /**
     * @param UpdateQueryBuilder $query
     * @param array              $parts
     */
    private static function buildFrom(UpdateQueryBuilder $query, array &$parts)
    {
        if (null !== $query->from) {
            $parts = \array_merge($parts, [$query->from]);
        }
    }

    /**
     * @param UpdateQueryBuilder $query
     * @param array              $parts
     */
    private static function buildJoins(UpdateQueryBuilder $query, array &$parts)
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
     * @param UpdateQueryBuilder $query
     * @param array              $parts
     */
    private static function buildSet(UpdateQueryBuilder $query, array &$parts)
    {
        if (null !== $query->set) {
            $parts = \array_merge($parts, ['SET', (string) $query->set]);
        }
    }

    /**
     * @param UpdateQueryBuilder $query
     * @param array              $parts
     */
    private static function buildWhere(UpdateQueryBuilder $query, array &$parts)
    {
        if (null !== $query->where) {
            $parts = \array_merge($parts, ['WHERE', (string) $query->where]);
        }
    }

    /**
     * @param UpdateQueryBuilder $query
     * @param array              $parts
     */
    private static function buildOrderBy(UpdateQueryBuilder $query, array &$parts)
    {
        if ([] !== $query->orderBy) {
            $parts = \array_merge($parts, ['ORDER BY'], [implode(', ', $query->orderBy)]);
        }
    }

    /**
     * @param UpdateQueryBuilder $query
     * @param array              $parts
     */
    private static function buildLimit(UpdateQueryBuilder $query, array &$parts)
    {
        if (null !== $query->limit) {
            $parts[] = \sprintf('LIMIT %d', $query->limit);
        }
    }

    /**
     * @param UpdateQueryBuilder $query
     * @return string
     */
    public static function stringify(UpdateQueryBuilder $query): string
    {
        $parts = self::initBuild($query);
        self::buildFrom($query, $parts);
        self::buildJoins($query, $parts);
        self::buildSet($query, $parts);
        self::buildWhere($query, $parts);
        self::buildOrderBy($query, $parts);
        self::buildLimit($query, $parts);
        return \implode(' ', $parts) . $query->end;
    }
}
