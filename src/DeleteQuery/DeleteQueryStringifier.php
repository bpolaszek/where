<?php

namespace BenTools\Where\DeleteQuery;

final class DeleteQueryStringifier
{

    /**
     * @param DeleteQueryBuilder $query
     * @return array
     */
    private static function initBuild(DeleteQueryBuilder $query): array
    {
        return \array_merge([$query->mainKeyword], $query->flags);
    }

    /**
     * @param DeleteQueryBuilder $query
     * @param array              $parts
     */
    private static function buildTables(DeleteQueryBuilder $query, array &$parts)
    {
        if ([] !== $query->tables && null !== $query->tables) {
            $parts = \array_merge($parts, [implode(', ', $query->tables)]);
        }
    }

    /**
     * @param DeleteQueryBuilder $query
     * @param array              $parts
     */
    private static function buildFrom(DeleteQueryBuilder $query, array &$parts)
    {
        if (null !== $query->from) {
            $parts = \array_merge($parts, ['FROM', $query->from]);
        }
    }

    /**
     * @param DeleteQueryBuilder $query
     * @param array              $parts
     */
    private static function buildPartitions(DeleteQueryBuilder $query, array &$parts)
    {
        if ([] !== $query->partitions) {
            $parts[] = 'PARTITION';
            $parts[] = sprintf('(%s)', \implode(', ', $query->partitions));
        }
    }

    /**
     * @param DeleteQueryBuilder $query
     * @param array              $parts
     */
    private static function buildJoins(DeleteQueryBuilder $query, array &$parts)
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
     * @param DeleteQueryBuilder $query
     * @param array              $parts
     */
    private static function buildWhere(DeleteQueryBuilder $query, array &$parts)
    {
        if (null !== $query->where) {
            $parts = \array_merge($parts, ['WHERE', (string) $query->where]);
        }
    }


    /**
     * @param DeleteQueryBuilder $query
     * @param array              $parts
     */
    private static function buildOrderBy(DeleteQueryBuilder $query, array &$parts)
    {
        if ([] !== $query->orderBy) {
            $parts = \array_merge($parts, ['ORDER BY'], [\implode(', ', $query->orderBy)]);
        }
    }

    /**
     * @param DeleteQueryBuilder $query
     * @param array              $parts
     */
    private static function buildLimit(DeleteQueryBuilder $query, array &$parts)
    {
        if (null !== $query->limit) {
            $parts[] = \sprintf('LIMIT %d', $query->limit);
        }
    }

    /**
     * @param DeleteQueryBuilder $query
     * @return string
     */
    public static function stringify(DeleteQueryBuilder $query): string
    {
        $parts = self::initBuild($query);
        self::buildTables($query, $parts);
        self::buildFrom($query, $parts);
        self::buildPartitions($query, $parts);
        self::buildJoins($query, $parts);
        self::buildWhere($query, $parts);
        self::buildOrderBy($query, $parts);
        self::buildLimit($query, $parts);
        return \implode(' ', $parts) . $query->end;
    }
}
