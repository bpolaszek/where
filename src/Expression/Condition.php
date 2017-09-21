<?php
declare(strict_types=1);

namespace BenTools\Where\Expression;

final class Condition extends Expression
{
    /**
     * @var string
     */
    private $condition;

    /**
     * @var array
     */
    private $values;

    /**
     * Condition constructor.
     * @param string $condition
     * @param array  $values
     */
    protected function __construct(string $condition, array $values = [])
    {
        $this->condition = $condition;
        $this->values = $values;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->condition;
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
