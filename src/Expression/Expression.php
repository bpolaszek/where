<?php
declare(strict_types=1);

namespace BenTools\Where\Expression;

use BenTools\Where\Helper\Previewer;

abstract class Expression
{
    /**
     * @var Expression
     */
    protected $expression;

    /**
     * @param       $expression
     * @param array ...$values
     * @return CompositeExpression
     * @throws \InvalidArgumentException
     */
    final public function and($expression, ...$values): CompositeExpression
    {
        $expression = self::where($expression, ...$values);
        return new CompositeExpression(' AND ', $this, $expression);
    }

    /**
     * @param       $expression
     * @param array ...$values
     * @return CompositeExpression
     * @throws \InvalidArgumentException
     */
    final public function or($expression, ...$values): CompositeExpression
    {
        $expression = self::where($expression, ...$values);
        return new CompositeExpression(' OR ', $this, $expression);
    }

    /**
     * @param       $expression
     * @param array ...$values
     * @return CompositeExpression
     * @throws \InvalidArgumentException
     */
    final public function plus($expression, ...$values): CompositeExpression
    {
        $expression = self::where($expression, ...$values);
        return new CompositeExpression(', ', $this, $expression);
    }

    /**
     * @param       $expression
     * @param array ...$values
     * @return CompositeExpression
     * @throws \InvalidArgumentException
     */
    final public function as($expression, ...$values): CompositeExpression
    {
        $expression = self::where($expression, ...$values);
        return new CompositeExpression(' AS ', $this, $expression);
    }

    /**
     * @return GroupExpression
     */
    final public function asGroup(): GroupExpression
    {
        return $this instanceof GroupExpression ? $this : new GroupExpression($this);
    }

    /**
     * @return Expression|NegatedExpression
     */
    final public function negate(): Expression
    {
        return $this instanceof NegatedExpression ? clone $this->expression : new NegatedExpression($this);
    }

    /**
     * @param       $expression
     * @param array ...$values
     * @return Expression
     * @throws \InvalidArgumentException
     */
    final public static function where($expression, ...$values): self
    {
        if (\is_scalar($expression)) {
            return new Condition($expression, self::valuesFactory($values));
        }
        if ($expression instanceof self) {
            if (1 !== \func_num_args()) {
                throw new \InvalidArgumentException("Cannot pass values to an existing Expression object.");
            }
            return $expression;
        }
        throw new \InvalidArgumentException(\sprintf('Expected string or Expression object, %s given', \is_object($expression) ? \get_class($expression) : \gettype($expression)));
    }

    /**
     * @param       $expression
     * @param array ...$values
     * @return GroupExpression
     * @throws \InvalidArgumentException
     */
    final public static function group($expression, ...$values): GroupExpression
    {
        return new GroupExpression(self::where($expression, ...$values));
    }

    /**
     * @param       $expression
     * @param array ...$values
     * @return NegatedExpression
     * @throws \InvalidArgumentException
     */
    final public static function not($expression, ...$values): NegatedExpression
    {
        return new NegatedExpression(self::where($expression, ...$values));
    }

    /**
     * @param array $values
     * @return array
     * @throws \InvalidArgumentException
     */
    final private static function valuesFactory(array $values): array
    {
        if (0 === \count($values)) {
            return [];
        }
        if (1 === \count($values) && \is_array($values[0])) {
            return $values[0];
        }
        foreach ($values as $value) {
            if (\is_array($value)) {
                throw new \InvalidArgumentException("Cannot construct expression with multiple array values.");
            }
        }
        return $values;
    }

    /**
     * @return string
     */
    abstract public function __toString(): string;

    /**
     * @return array
     */
    abstract public function getValues(): array;

    /**
     * @return string
     */
    public function preview(): string
    {
        return Previewer::preview((string) $this, $this->getValues());
    }

    /**
     * @param Expression[] ...$expressions
     * @return array
     */
    final public static function valuesOf(self ...$expressions): array
    {
        $generator = function (Expression ...$expressions) {
            foreach ($expressions as $expression) {
                foreach ($expression->getValues() as $key => $value) {
                    if (\is_numeric($key)) {
                        yield $value;
                    } else {
                        yield $key => $value;
                    }
                }
            }
        };
        return \iterator_to_array($generator(...$expressions));
    }
}
