<?php

namespace BenTools\Where\Tests\Expression;

use function BenTools\Where\group;
use BenTools\Where\Expression\GroupExpression;
use PHPUnit\Framework\TestCase;

class GroupExpressionTest extends TestCase
{

    public function testGroup()
    {
        $condition = group('order_id = ?', 123456);
        $this->assertInstanceOf(GroupExpression::class, $condition);
        $this->assertEquals('(order_id = ?)', (string) $condition);
    }

    public function testAnd()
    {
        $condition = group('order_id = ?', 123456)->and('date_added = ?', '2017-09-13');
        $this->assertEquals('(order_id = ?) AND date_added = ?', (string) $condition);
    }

    public function testOr()
    {
        $condition = group('order_id = ?', 123456)->or('date_added = ?', '2017-09-13');
        $this->assertEquals('(order_id = ?) OR date_added = ?', (string) $condition);
    }
}
