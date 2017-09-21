<?php

namespace BenTools\Where;

use BenTools\Where\Expression\Condition;
use BenTools\Where\Expression\Expression;
use BenTools\Where\Expression\GroupExpression;
use BenTools\Where\Expression\NegatedExpression;
use BenTools\Where\SelectQuery\SelectQueryBuilder;

/**
 * @param string|Expression $expression
 * @param array ...$values
 * @return Expression|Condition
 * @throws \InvalidArgumentException
 */
function where($expression, ...$values): Expression
{
    return Expression::where($expression, ...$values);
}

/**
 * @param string|Expression $expression
 * @param array ...$values
 * @return GroupExpression
 * @throws \InvalidArgumentException
 */
function group($expression, ...$values): GroupExpression
{
    return Expression::group($expression, ...$values);
}

/**
 * @param string|Expression $expression
 * @param array ...$values
 * @return NegatedExpression
 * @throws \InvalidArgumentException
 */
function not($expression, ...$values): NegatedExpression
{
    return Expression::not($expression, ...$values);
}

/**
 * @param Expression[]|string[] ...$columns
 * @return SelectQueryBuilder
 */
function select(...$columns): SelectQueryBuilder
{
    return SelectQueryBuilder::make(...$columns);
}
