<?php
declare(strict_types=1);

namespace BenTools\Where\SelectQuery;

use BenTools\Where\Expression\Expression;

/**
 * Class SelectQuery
 *
 * @property $flags
 * @property $distinct
 * @property $columns
 * @property $from
 * @property $joins
 * @property $where
 * @property $groupBy
 * @property $having
 * @property $orderBy
 * @property $limit
 * @property $offset
 * @property $end
 */
final class SelectQueryBuilder
{
    /**
     * @var array
     */
    private $flags = [];

    /**
     * @var bool
     */
    private $distinct = false;

    /**
     * @var array
     */
    private $columns = ['*'];

    /**
     * @var string
     */
    private $from;

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
    private $groupBy = [];

    /**
     * @var Expression
     */
    private $having;

    /**
     * @var array
     */
    private $orderBy = [];

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var string
     */
    private $end = ';';

    /**
     * @param Expression[]|string[] ...$columns
     * @return SelectQueryBuilder
     */
    public static function make(...$columns): self
    {
        $select = new self;
        if (0 !== func_num_args()) {
            $select->validateColumns(...$columns);
            $select->columns = $columns;
        }
        return $select;
    }

    /**
     * @param Expression[]|string[] ...$columns
     * @throws \InvalidArgumentException
     */
    private function validateColumns(...$columns)
    {
        foreach ($columns as $column) {
            if (!($column instanceof Expression || is_scalar($column))) {
                throw new \InvalidArgumentException(
                    sprintf(
                        "Expected string or Expression, got %s",
                        is_object($column) ? get_class($column) : gettype($column)
                    )
                );
            }
        }
    }

    /**
     * @param Expression[]|string[] ...$columns
     * @return SelectQueryBuilder
     */
    public function withColumns(...$columns): self
    {
        $this->validateColumns(...$columns);
        $clone = clone $this;
        $clone->columns = $columns;
        return $clone;
    }

    /**
     * @param Expression[]|string[] ...$columns
     * @return SelectQueryBuilder
     */
    public function withAddedColumns(...$columns): self
    {
        $this->validateColumns(...$columns);
        $clone = clone $this;
        $clone->columns = array_merge($clone->columns, $columns);
        return $clone;
    }

    /**
     * @param string[] ...$flags
     * @return SelectQueryBuilder
     */
    public function withFlags(string ...$flags): self
    {
        $clone = clone $this;
        $clone->flags = $flags;
        return $clone;
    }

    /**
     * @param string[] ...$flags
     * @return SelectQueryBuilder
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
     * @param bool $distinct
     * @return SelectQueryBuilder
     */
    public function distinct(bool $distinct = true): self
    {
        $clone = clone $this;
        $clone->distinct = $distinct;
        return $clone;
    }

    /**
     * @param string $table
     * @return SelectQueryBuilder
     */
    public function from(string $table = null): self
    {
        $clone = clone $this;
        $clone->from = $table;
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
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
     * @return SelectQueryBuilder
     */
    public function groupBy(string ...$groupBy): self
    {
        $clone = clone $this;
        $clone->groupBy = $groupBy;
        return $clone;
    }

    /**
     * @param string[] ...$groupBy
     * @return SelectQueryBuilder
     */
    public function andGroupBy(string ...$groupBy): self
    {
        $clone = clone $this;
        $clone->groupBy = array_merge($clone->groupBy, $groupBy);
        return $clone;
    }

    /**
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return SelectQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function having($expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->having = null !== $expression ? Expression::where($expression, ...$values) : null;
        return $clone;
    }

    /**
     * @param string|Expression $expression
     * @param array             ...$values
     * @return SelectQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function andHaving($expression, ...$values): self
    {
        if (null === $this->having) {
            return $this->having(Expression::where($expression, ...$values));
        }
        $clone = clone $this;
        $clone->having = $clone->having->and(Expression::where($expression, ...$values));
        return $clone;
    }

    /**
     * @param string|Expression $expression
     * @param array             ...$values
     * @return SelectQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function orHaving($expression, ...$values): self
    {
        if (null === $this->having) {
            return $this->having(Expression::where($expression, ...$values));
        }
        $clone = clone $this;
        $clone->having = $clone->having->or(Expression::where($expression, ...$values));
        return $clone;
    }

    /**
     * @param string[] ...$groupBy
     * @return SelectQueryBuilder
     */
    public function orderBy(string ...$orderBy): self
    {
        $clone = clone $this;
        $clone->orderBy = $orderBy;
        return $clone;
    }

    /**
     * @param string[] ...$groupBy
     * @return SelectQueryBuilder
     */
    public function andOrderBy(string ...$orderBy): self
    {
        $clone = clone $this;
        $clone->orderBy = array_merge($clone->orderBy, $orderBy);
        return $clone;
    }

    /**
     * @param int|null $limit
     * @return SelectQueryBuilder
     */
    public function limit(int $limit = null): self
    {
        $clone = clone $this;
        $clone->limit = $limit;
        return $clone;
    }

    /**
     * @param int|null $offset
     * @return SelectQueryBuilder
     */
    public function offset(int $offset = null): self
    {
        $clone = clone $this;
        $clone->offset = $offset;
        return $clone;
    }

    /**
     * @param string|null $end
     * @return SelectQueryBuilder
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
        $generator = function (Expression ...$expressions) {
            foreach ($expressions as $expression) {
                foreach ($expression->getValues() as $key => $value) {
                    if (is_numeric($key)) {
                        yield $value;
                    } else {
                        yield $key => $value;
                    }
                }
            }
        };

        $expressions = array_filter(array_merge($this->columns, array_column($this->joins, 'c'), [$this->where, $this->having]), function ($expression) {
            return $expression instanceof Expression;
        });

        return iterator_to_array($generator(...$expressions));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return SelectQueryStringifier::stringify($this);
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
