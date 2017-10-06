<?php
declare(strict_types=1);

namespace BenTools\Where\InsertQuery;

use function BenTools\FlattenIterator\flatten;

/**
 * Class InsertQueryBuilder
 *
 * @property $mainKeyword
 * @property $flags
 * @property $values
 * @property $columns
 * @property $table
 * @property $onDuplicate
 * @property $end
 * @property $escape
 */
final class InsertQueryBuilder
{

    /**
     * @var string
     */
    private $mainKeyword = 'INSERT';

    /**
     * @var array
     */
    private $flags = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * @var array
     */
    private $columns;

    /**
     * @var string
     */
    private $table;

    /**
     * @var array
     */
    private $onDuplicate;

    /**
     * @var string
     */
    private $end = ';';

    /**
     * @var string
     */
    private $escape;

    /**
     * @param array[] ...$values
     * @return InsertQueryBuilder
     * @throws \InvalidArgumentException
     */
    public static function load(array ...$values): self
    {
        if (0 === count($values)) {
            throw new \InvalidArgumentException("At least 1 value is needed.");
        }

        $query = new self;
        foreach ($values as $value) {
            $query->values[] = $query->validateValue($value);
        }
        return $query;
    }

    /**
     * @param string $keyword
     * @return InsertQueryBuilder
     */
    public function withMainKeyword(string $keyword): self
    {
        $clone = clone $this;
        $clone->mainKeyword = $keyword;
        return $clone;
    }

    /**
     * @param string   $table
     * @param string[] ...$columns
     * @return InsertQueryBuilder
     */
    public function into(string $table, string ...$columns): self
    {
        $clone = clone $this;
        $clone->table = $table;
        $clone->columns = [] === $columns ? null : $columns;
        return $clone;
    }


    /**
     * @param string[] ...$flags
     * @return InsertQueryBuilder
     */
    public function withFlags(string ...$flags): self
    {
        $clone = clone $this;
        $clone->flags = $flags;
        return $clone;
    }

    /**
     * @param string[] ...$flags
     * @return InsertQueryBuilder
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
     * @param string|null $escape
     * @return InsertQueryBuilder
     */
    public function withEscaper(string $escape = null): self
    {
        $clone = clone $this;
        $clone->escape = $escape;
        return $clone;
    }

    /**
     * @param array $updateConditions
     * @return InsertQueryBuilder
     */
    public function onDuplicateKeyUpdate(array $updateConditions = null): self
    {
        $clone = clone $this;
        $clone->onDuplicate = $updateConditions;
        return $clone;
    }

    /**
     * @param null $end
     * @return InsertQueryBuilder
     */
    public function end($end = null): self
    {
        $clone = clone $this;
        $clone->end = $end;
        return $clone;
    }

    /**
     * @param array $value
     * @return array
     * @throws \InvalidArgumentException
     */
    private function validateValue(array &$value): array
    {
        if (empty($this->values)) {
            return $value;
        }
        $keys = array_keys($this->values[0]);
        $valueKeys = array_keys($value);
        if ($valueKeys !== $keys) {
            if (count($keys) !== count($valueKeys)) {
                throw new \InvalidArgumentException("Invalid value.");
            }
            uksort($value, function ($key1, $key2) use ($keys) {
                return array_search($key1, $keys) <=> array_search($key2, $keys);
            });
            $valueKeys = array_keys($value);
            if ($valueKeys !== $keys) {
                throw new \InvalidArgumentException("Invalid value.");
            }
        }
        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public function escape(string $value): string
    {
        return null === $this->escape ? $value : $this->escape . $value . $this->escape;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return InsertQueryStringifier::stringify($this);
    }

    /**
     * Split into multiple INSERT statements.
     *
     * @param int $max
     * @return iterable|self[]
     */
    public function split(int $max): iterable
    {
        $pos = 0;
        $total = count($this->values);
        while ($pos < $total) {
            $clone = clone $this;
            $clone->values = array_slice($this->values, $pos, $max);
            $pos += $max;
            yield $clone;
        }
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        $generator = function (array $values, array $columns) {
            foreach ($values as $value) {
                $valueKeys = array_keys($value);
                if ($valueKeys !== $columns) {
                    $value = array_intersect_key($value, array_combine($columns, array_fill(0, count($columns), null)));
                    $valueKeys = array_keys($value);
                    if ($valueKeys !== $columns) {
                        uksort($value, function ($key1, $key2) use ($columns) {
                            return array_search($key1, $columns) <=> array_search($key2, $columns);
                        });
                    }
                }
                yield $value;
            }
        };
        return flatten($generator($this->values, $this->getColumns()))->asArray();
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns ?? array_keys($this->values[0] ?? []);
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
