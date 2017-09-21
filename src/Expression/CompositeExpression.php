<?php
declare(strict_types=1);

namespace BenTools\Where\Expression;

final class CompositeExpression extends Expression
{
    /**
     * @var string
     */
    private $operator;

    /**
     * @var Expression[]
     */
    private $expressions = [];

    /**
     * CompositeExpression constructor.
     * @param string       $operator
     * @param Expression[] ...$expressions
     */
    protected function __construct(string $operator, Expression ...$expressions)
    {
        $this->operator = $operator;
        $this->expressions = $expressions;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return implode(" {$this->operator} ", array_map(function (Expression $expression) {
            return (string) $expression;
        }, $this->expressions));
    }

    /**
     * @inheritDoc
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

        return iterator_to_array($generator(...$this->expressions));
    }
}
