<?php

namespace BenTools\Where\Tests\Expression;

use BenTools\Where\Expression\NegatedExpression;
use function BenTools\Where\not;
use PHPUnit\Framework\TestCase;

class NegatedExpressionTest extends TestCase
{

    public function testNegatedExpression()
    {
        $expression = not('foo = ?', 'bar');
        $this->assertInstanceOf(NegatedExpression::class, $expression);
        $this->assertEquals('NOT foo = ?', (string) $expression);
        $this->assertEquals('bar', $expression->getValues()[0]);
    }
}
