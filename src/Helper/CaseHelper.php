<?php

namespace BenTools\Where\Helper;

use BenTools\Where\Expression\Expression;
use function BenTools\Where\where;

/**
 * @internal
 */
final class CaseHelper extends Expression
{

    /**
     * @var Expression
     */
    private $field;

    /**
     * @var Expression[]
     */
    private $when = [];

    /**
     * @var Expression[]
     */
    private $then = [];

    /**
     * @var Expression
     */
    private $else;

    private $lock = false;

    /**
     * CaseHelper constructor.
     * @param Expression|null $field
     */
    private function __construct(Expression $field = null)
    {
        $this->field = $field;
    }

    /**
     * @param       $expression
     * @param array ...$values
     * @return CaseHelper
     * @throws \InvalidArgumentException
     */
    public function when($expression, ...$values): self
    {
        if (\count($this->when) !== \count($this->then)) {
            throw new \RuntimeException("Mising a 'then' somewhere.");
        }
        if (true === $this->lock) {
            throw new \RuntimeException('This conditionnal structure is locked.');
        }
        $clone = clone $this;
        $clone->when[] = where($expression, ...$values);
        return $clone;
    }

    /**
     * @param       $expression
     * @param array ...$values
     * @return CaseHelper
     * @throws \InvalidArgumentException
     */
    public function then($expression, ...$values): self
    {
        if (\count($this->when) !== \count($this->then) + 1) {
            throw new \RuntimeException("Mising a 'when' somewhere.");
        }
        if (true === $this->lock) {
            throw new \RuntimeException('This conditionnal structure is locked.');
        }
        $clone = clone $this;
        $clone->then[] = where($expression, ...$values);
        return $clone;
    }

    /**
     * @param       $expression
     * @param array ...$values
     * @return CaseHelper
     * @throws \InvalidArgumentException
     */
    public function else($expression, ...$values): self
    {
        if (true === $this->lock) {
            throw new \RuntimeException('This conditionnal structure is locked.');
        }
        $clone = clone $this;
        $clone->else = where($expression, ...$values);
        return $clone;
    }

    /**
     * Only for fluent purposes.
     *
     * @return CaseHelper
     */
    public function end(): self
    {
        $clone = clone $this;
        $clone->lock = true;
        return $clone;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $parts = ['CASE'];

        if (null !== $this->field) {
            $parts[] = $this->field;
        }

        foreach ($this->when as $key => $value) {
            $parts[] = 'WHEN';
            $parts[] = $this->when[$key];
            $parts[] = 'THEN';
            $parts[] = $this->then[$key] ?? null;
        }

        if (null !== $this->else) {
            $parts[] = 'ELSE';
            $parts[] = $this->else;
        }

        $parts[] = 'END';

        return \implode(' ', $parts);
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        $values = [[]];

        if (null !== $this->field) {
            $values[] = $this->field->getValues();
        }

        foreach ($this->when as $key => $whenExpression) {
            $values[] = $whenExpression->getValues();
            $thenExpression = $this->then[$key];
            if (null !== $thenExpression) {
                $values[] = $thenExpression->getValues();
            }
        }

        if (null !== $this->else) {
            $values[] = $this->else->getValues();
        }

        return \array_merge(...$values);
    }

    /**
     * @param       $expression
     * @param array ...$values
     * @return CaseHelper
     * @throws \InvalidArgumentException
     */
    public static function create($expression = null, ...$values): self
    {
        if (null !== $expression) {
            $expression = where($expression, ...$values);
        }
        return new self($expression);
    }
}
