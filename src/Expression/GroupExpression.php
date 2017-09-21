<?php
declare(strict_types=1);

namespace BenTools\Where\Expression;

final class GroupExpression extends Expression
{
    /**
     * @var Expression
     */
    private $expression;

    /**
     * GroupExpression constructor.
     * @param Expression $expression
     */
    protected function __construct(Expression $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return sprintf('(%s)', $this->expression);
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array
    {
        return $this->expression->getValues();
    }
}
