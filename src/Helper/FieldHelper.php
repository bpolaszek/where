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
     * @param array       $values
     * @param null|string $placeholder
     * @param string      $glue
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function in(array $values, ?string $placeholder = '?', string $glue = ', '): Expression
    {
        $expression = '%s IN (%s)';
        return null !== $placeholder ? where(sprintf($expression, $this->field, placeholders($values, $placeholder, $glue)), ...array_values($values)) : where(sprintf($expression, $this->field, implode(', ', $values)));
    }

    /**
     * @param array  $values
     * @param string $placeholder
     * @param string $glue
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notIn(array $values, ?string $placeholder = '?', string $glue = ', '): Expression
    {
        $expression = '%s NOT IN (%s)';
        return null !== $placeholder ? where(sprintf($expression, $this->field, placeholders($values, $placeholder, $glue)), ...array_values($values)) : where(sprintf($expression, $this->field, implode(', ', $values)));
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function equals($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s = %s';
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notEquals($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s <> %s';
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function lt($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s < %s';
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function lte($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s <= %s';
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function gt($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s > %s';
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param        $value
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function gte($value, ?string $placeholder = '?'): Expression
    {
        $expression = '%s >= %s';
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param        $start
     * @param        $end
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function between($start, $end, ?string $placeholder = '?'): Expression
    {
        $expression = '%s BETWEEN %s AND %s';
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder, $placeholder), $start, $end) : where(sprintf($expression, $this->field, $start, $end));
    }

    /**
     * @param        $start
     * @param        $end
     * @param string $placeholder
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notBetween($start, $end, ?string $placeholder = '?'): Expression
    {
        $expression = '%s NOT BETWEEN %s AND %s';
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder, $placeholder), $start, $end) : where(sprintf($expression, $this->field, $start, $end));
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function like(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s LIKE %s';
        $value = $surroundWith . $value . $surroundWith;
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notLike(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s NOT LIKE %s';
        $value = $surroundWith . $value . $surroundWith;
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function startsWith(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s LIKE %s';
        $value = $value . $surroundWith;
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notStartsWith(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s NOT LIKE %s';
        $value = $value . $surroundWith;
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function endsWith(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s LIKE %s';
        $value = $surroundWith . $value;
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }

    /**
     * @param string $value
     * @param string $placeholder
     * @param string $surroundWith
     * @return Expression
     * @throws \InvalidArgumentException
     */
    public function notEndsWith(string $value, ?string $placeholder = '?', string $surroundWith = '%'): Expression
    {
        $expression = '%s NOT LIKE %s';
        $value = $surroundWith . $value;
        return null !== $placeholder ? where(sprintf($expression, $this->field, $placeholder), $value) : where(sprintf($expression, $this->field, $value));
    }
}
