<?php

namespace BenTools\Where\DeleteQuery;

use BenTools\Where\Expression\Expression;
use function BenTools\Where\valuesOf;

/**
 * Class SelectQuery
 *
 * @property $mainKeyword
 * @property $flags
 * @property $tables
 * @property $from
 * @property $joins
 * @property $partitions
 * @property $where
 * @property $orderBy
 * @property $limit
 * @property $end
 */
final class DeleteQueryBuilder
{

    /**
     * @var string
     */
    private $mainKeyword = 'DELETE';

    /**
     * @var array
     */
    private $flags = [];

    /**
     * @var array
     */
    private $tables = [];

    /**
     * @var string
     */
    private $from;

    /**
     * @var array
     */
    private $partitions = [];

    /**
     * @var array
     */
    private $joins = [];

    /**
     * @var Expression
     */
    private $where;

    /**
     * @var array
     */
    private $orderBy = [];

    /**
     * @var int
     */
    private $limit;

    /**
     * @var string
     */
    private $end = ';';

    /**
     * @param Expression[]|string[] ...$tables
     * @return DeleteQueryBuilder
     */
    public static function make(...$tables): self
    {
        $query = new self;
        if (0 !== func_num_args()) {
            $query->tables = $tables;
        }
        return $query;
    }

    /**
     * @param string $keyword
     * @return DeleteQueryBuilder
     */
    public function withMainKeyword(string $keyword): self
    {
        $clone = clone $this;
        $clone->mainKeyword = $keyword;
        return $clone;
    }

    /**
     * @param Expression[]|string[] ...$tables
     * @return DeleteQueryBuilder
     */
    public function deleteOnlyFromTables(...$tables): self
    {
        $clone = clone $this;
        $clone->tables = $tables;
        return $clone;
    }

    /**
     * @param string[] ...$flags
     * @return DeleteQueryBuilder
     */
    public function withFlags(string ...$flags): self
    {
        $clone = clone $this;
        $clone->flags = $flags;
        return $clone;
    }

    /**
     * @param string[] ...$flags
     * @return DeleteQueryBuilder
     */
    public function withAddedFlags(string ...$flags): self
    {
        $clone = clone $this;
        $existingFlags = array_map('strtoupper', $clone->flags);
        foreach ($flags as $flag) {
            if (!in_array(strtoupper($flag), $existingFlags, true)) {
                $clone->flags[] = $flag;
            }
        }
        return $clone;
    }

    /**
     * @param string $table
     * @return DeleteQueryBuilder
     */
    public function from(string $table = null): self
    {
        $clone = clone $this;
        $clone->from = $table;
        return $clone;
    }

    /**
     * @param string[] ...$partitions
     * @return DeleteQueryBuilder
     */
    public function withPartitions(string ...$partitions): self
    {
        $clone = clone $this;
        $clone->partitions = $partitions;
        return $clone;
    }

    /**
     * @param string[] ...$partitions
     * @return DeleteQueryBuilder
     */
    public function withAddedPartitions(string ...$partitions): self
    {
        $clone = clone $this;
        foreach ($partitions as $partition) {
            if (!in_array($partition, $clone->partitions, true)) {
                $clone->partitions[] = $partition;
            }
        }
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function join(string $table, $expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->joins[$table] = [
            't' => 'JOIN',
            'c' => null !== $expression ? Expression::where($expression, ...$values) : null,
        ];
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function innerJoin(string $table, $expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->joins[$table] = [
            't' => 'INNER JOIN',
            'c' => null !== $expression ? Expression::where($expression, ...$values) : null,
        ];
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function outerJoin(string $table, $expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->joins[$table] = [
            't' => 'OUTER JOIN',
            'c' => null !== $expression ? Expression::where($expression, ...$values) : null,
        ];
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function leftJoin(string $table, $expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->joins[$table] = [
            't' => 'LEFT JOIN',
            'c' => null !== $expression ? Expression::where($expression, ...$values) : null,
        ];
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function leftOuterJoin(string $table, $expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->joins[$table] = [
            't' => 'LEFT OUTER JOIN',
            'c' => null !== $expression ? Expression::where($expression, ...$values) : null,
        ];
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function rightJoin(string $table, $expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->joins[$table] = [
            't' => 'RIGHT JOIN',
            'c' => null !== $expression ? Expression::where($expression, ...$values) : null,
        ];
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function rightOuterJoin(string $table, $expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->joins[$table] = [
            't' => 'RIGHT OUTER JOIN',
            'c' => null !== $expression ? Expression::where($expression, ...$values) : null,
        ];
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function fullJoin(string $table, $expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->joins[$table] = [
            't' => 'FULL JOIN',
            'c' => null !== $expression ? Expression::where($expression, ...$values) : null,
        ];
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function fullOuterJoin(string $table, $expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->joins[$table] = [
            't' => 'FULL OUTER JOIN',
            'c' => null !== $expression ? Expression::where($expression, ...$values) : null,
        ];
        return $clone;
    }

    /**
     * Reset all JOIN clauses.
     *
     * @return DeleteQueryBuilder
     */
    public function resetJoins(): self
    {
        $clone = clone $this;
        $clone->joins = [];
        return $clone;
    }

    /**
     * Remove a specific JOIN clause.
     *
     * @param string $table
     * @return DeleteQueryBuilder
     */
    public function withoutJoin(string $table)
    {
        $clone = clone $this;
        unset($clone->joins[$table]);
        return $clone;
    }

    /**
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function where($expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->where = null !== $expression ? Expression::where($expression, ...$values) : null;
        return $clone;
    }

    /**
     * @param string|Expression $expression
     * @param array             ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function andWhere($expression, ...$values): self
    {
        if (null === $this->where) {
            return $this->where(Expression::where($expression, ...$values));
        }
        $clone = clone $this;
        $clone->where = $clone->where->and(Expression::where($expression, ...$values));
        return $clone;
    }

    /**
     * @param string|Expression $expression
     * @param array             ...$values
     * @return DeleteQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function orWhere($expression, ...$values): self
    {
        if (null === $this->where) {
            return $this->where(Expression::where($expression, ...$values));
        }
        $clone = clone $this;
        $clone->where = $clone->where->or(Expression::where($expression, ...$values));
        return $clone;
    }

    /**
     * @param string[] ...$groupBy
     * @return DeleteQueryBuilder
     */
    public function orderBy(string ...$orderBy): self
    {
        $clone = clone $this;
        $clone->orderBy = $orderBy;
        return $clone;
    }

    /**
     * @param string[] ...$groupBy
     * @return DeleteQueryBuilder
     */
    public function andOrderBy(string ...$orderBy): self
    {
        $clone = clone $this;
        $clone->orderBy = array_merge($clone->orderBy, $orderBy);
        return $clone;
    }

    /**
     * @param int|null $limit
     * @return DeleteQueryBuilder
     */
    public function limit(int $limit = null): self
    {
        $clone = clone $this;
        $clone->limit = $limit;
        return $clone;
    }

    /**
     * @param string|null $end
     * @return DeleteQueryBuilder
     */
    public function end(string $end = null): self
    {
        $clone = clone $this;
        $clone->end = $end;
        return $clone;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        $expressions = array_filter(array_merge(array_column($this->joins, 'c'), [$this->where]), function ($expression) {
            return $expression instanceof Expression;
        });
        return valuesOf(...$expressions);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return DeleteQueryStringifier::stringify($this);
    }

    /**
     * Read-only properties.
     *
     * @param $property
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get($property)
    {
        if (!property_exists($this, $property)) {
            throw new \InvalidArgumentException(sprintf('Property %s::$%s does not exist.', __CLASS__, $property));
        }
        return $this->{$property};
    }
}
