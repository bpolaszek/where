<?php

namespace BenTools\Where\UpdateQuery;

use BenTools\Where\Expression\Expression;
use BenTools\Where\Helper\Previewer;
use function BenTools\Where\valuesOf;

/**
 * Class UpdateQueryBuilder
 *
 * @property $mainKeyword
 * @property $flags
 * @property $from
 * @property $set
 * @property $joins
 * @property $where
 * @property $orderBy
 * @property $limit
 * @property $end
 */
final class UpdateQueryBuilder
{
    /**
     * @var string
     */
    private $mainKeyword = 'UPDATE';

    /**
     * @var array
     */
    private $flags = [];

    /**
     * @var string
     */
    private $from;

    /**
     * @var Expression
     */
    private $set;

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
     * @param string $table
     * @return UpdateQueryBuilder
     */
    public static function make(string $table): self
    {
        $query = new self;
        $query->from = $table;
        return $query;
    }

    /**
     * @param string $table
     * @return UpdateQueryBuilder
     */
    public function table(string $table): self
    {
        $clone = clone $this;
        $clone->from = $table;
        return $clone;
    }

    /**
     * @param string $keyword
     * @return UpdateQueryBuilder
     */
    public function withMainKeyword(string $keyword): self
    {
        $clone = clone $this;
        $clone->mainKeyword = $keyword;
        return $clone;
    }

    /**
     * @param string[] ...$flags
     * @return UpdateQueryBuilder
     */
    public function withFlags(string ...$flags): self
    {
        $clone = clone $this;
        $clone->flags = $flags;
        return $clone;
    }

    /**
     * @param string[] ...$flags
     * @return UpdateQueryBuilder
     */
    public function withAddedFlags(string ...$flags): self
    {
        $clone = clone $this;
        $existingFlags = \array_map('strtoupper', $clone->flags);
        foreach ($flags as $flag) {
            if (!\in_array(\strtoupper($flag), $existingFlags, true)) {
                $clone->flags[] = $flag;
            }
        }
        return $clone;
    }

    /**
     * @param string                 $table
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
     */
    public function withoutJoin(string $table)
    {
        $clone = clone $this;
        unset($clone->joins[$table]);
        return $clone;
    }

    /**
     * @param null  $expression
     * @param array ...$values
     * @return UpdateQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function set($expression = null, ...$values): self
    {
        $clone = clone $this;
        $clone->set = null !== $expression ? Expression::where($expression, ...$values) : null;
        return $clone;
    }

    /**
     * @param null  $expression
     * @param array ...$values
     * @return UpdateQueryBuilder
     * @throws \InvalidArgumentException
     */
    public function andSet($expression = null, ...$values): self
    {
        if (null === $this->set) {
            return $this->set(Expression::where($expression, ...$values));
        }
        $clone = clone $this;
        $clone->set = $clone->set->plus(Expression::where($expression, ...$values));
        return $clone;
    }

    /**
     * @param string|Expression|null $expression
     * @param array                  ...$values
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
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
     * @return UpdateQueryBuilder
     */
    public function orderBy(string ...$orderBy): self
    {
        $clone = clone $this;
        $clone->orderBy = $orderBy;
        return $clone;
    }

    /**
     * @param string[] ...$groupBy
     * @return UpdateQueryBuilder
     */
    public function andOrderBy(string ...$orderBy): self
    {
        $clone = clone $this;
        $clone->orderBy = \array_merge($clone->orderBy, $orderBy);
        return $clone;
    }

    /**
     * @param int|null $limit
     * @return UpdateQueryBuilder
     */
    public function limit(int $limit = null): self
    {
        $clone = clone $this;
        $clone->limit = $limit;
        return $clone;
    }

    /**
     * @param string|null $end
     * @return UpdateQueryBuilder
     */
    public function end(string $end = null): self
    {
        $clone = clone $this;
        $clone->end = $end;
        return $clone;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return UpdateQueryStringifier::stringify($this);
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        $expressions = \array_filter(\array_merge(\array_column($this->joins, 'c'), [$this->set, $this->where]), function ($expression) {
            return $expression instanceof Expression;
        });
        return valuesOf(...$expressions);
    }

    /**
     * @return string
     */
    public function preview(): string
    {
        return Previewer::preview((string) $this, $this->getValues());
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
        if (!\property_exists($this, $property)) {
            throw new \InvalidArgumentException(\sprintf('Property %s::$%s does not exist.', __CLASS__, $property));
        }
        return $this->{$property};
    }
}
