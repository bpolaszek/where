<?php
declare(strict_types=1);

namespace BenTools\Where\Helper;

use BenTools\Where\Expression\Expression;
use function BenTools\Where\placeholders;
use function BenTools\Where\where;

class FieldHelper
{
    /**
     * @var string
     */
    private $field;

    /**
     * Field constructor.
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    /**
     * @param array  $values
     * @param string $placeholder
     * @param string $glue
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function in(array $values, string $placeholder = '?', string $glue = ', '): Expression
    {
        return where(sprintf('%s IN (%s)', $this->field, placeholders($values, $placeholder, $glue)), ...array_values($values));
    }

    /**
     * @param array  $values
     * @param string $placeholder
     * @param string $glue
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notIn(array $values, string $placeholder = '?', string $glue = ', '): Expression
    {
        return where(sprintf('%s NOT IN (%s)', $this->field, placeholders($values, $placeholder, $glue)), ...array_values($values));
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function equals($value, string $placeholder = '?'): Expression
    {
        return where(sprintf('%s = %s', $this->field, $placeholder), $value);
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notEquals($value, string $placeholder = '?'): Expression
    {
        return where(sprintf('%s <> %s', $this->field, $placeholder), $value);
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function lt($value, string $placeholder = '?'): Expression
    {
        return where(sprintf('%s < %s', $this->field, $placeholder), $value);
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function lte($value, string $placeholder = '?'): Expression
    {
        return where(sprintf('%s <= %s', $this->field, $placeholder), $value);
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function gt($value, string $placeholder = '?'): Expression
    {
        return where(sprintf('%s > %s', $this->field, $placeholder), $value);
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function gte($value, string $placeholder = '?'): Expression
    {
        return where(sprintf('%s >= %s', $this->field, $placeholder), $value);
    }

    /**
     * @param        $start
     * @param        $end
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function between($start, $end, string $placeholder = '?'): Expression
    {
        return where(sprintf('%s BETWEEN %s AND %s', $this->field, $placeholder, $placeholder), $start, $end);
    }

    /**
     * @param        $start
     * @param        $end
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notBetween($start, $end, string $placeholder = '?'): Expression
    {
        return where(sprintf('%s NOT BETWEEN %s AND %s', $this->field, $placeholder, $placeholder), $start, $end);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function like(string $value, string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        return where(sprintf('%s LIKE %s', $this->field, $placeholder), $surroundWith . $value . $surroundWith);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notLike(string $value, string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        return where(sprintf('%s NOT LIKE %s', $this->field, $placeholder), $surroundWith . $value . $surroundWith);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function startsWith(string $value, string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        return where(sprintf('%s LIKE %s', $this->field, $placeholder), $value . $surroundWith);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notStartsWith(string $value, string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        return where(sprintf('%s NOT LIKE %s', $this->field, $placeholder), $value . $surroundWith);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function endsWith(string $value, string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        return where(sprintf('%s LIKE %s', $this->field, $placeholder), $surroundWith . $value);
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notEndsWith(string $value, string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        return where(sprintf('%s NOT LIKE %s', $this->field, $placeholder), $surroundWith . $value);
    }
}
