<?php

namespace BenTools\Where\Tests\Expression;

use BenTools\Where\Expression\CompositeExpression;
use function BenTools\Where\group;
use PHPUnit\Framework\TestCase;

class CompositeExpressionTest extends TestCase
{

    public function test()
    {
        $condition = group('order_id BETWEEN ? AND ?', 10000, 15000)->and('status = ?', 3);
        $this->assertInstanceOf(CompositeExpression::class, $condition);
        $this->assertEquals('(order_id BETWEEN ? AND ?) AND status = ?', (string) $condition);
        $values = $condition->getValues();
        $this->assertCount(3, $values);
        $this->assertEquals(10000, $values[0]);
        $this->assertEquals(15000, $values[1]);
        $this->assertEquals(3, $values[2]);
    }
}
