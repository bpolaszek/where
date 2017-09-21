<?php

namespace BenTools\Where\Tests\Expression;

use function BenTools\Where\group;
use function BenTools\Where\where;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{

    public function testFactoryWithStringAndNoValue()
    {
        $condition = where('CURRENT_TIMESTAMP = NOW()');
        $this->assertEquals('CURRENT_TIMESTAMP = NOW()', (string) $condition);
        $this->assertCount(0, $condition->getValues());
    }

    public function testFactoryWithStringAndSimpleValue()
    {
        $condition = where('CURRENT_DATE = ?', '2017-09-13');
        $this->assertEquals('CURRENT_DATE = ?', (string) $condition);
        $values = $condition->getValues();
        $this->assertCount(1, $values);
        $this->assertArrayHasKey(0, $values);
        $this->assertEquals('2017-09-13', $values[0]);
    }

    public function testFactoryWithStringAndMultipleValues()
    {
        $condition = where('CURRENT_DATE BETWEEN ? AND ?', '2017-09-01', '2017-09-13');
        $this->assertEquals('CURRENT_DATE BETWEEN ? AND ?', (string) $condition);
        $values = $condition->getValues();
        $this->assertCount(2, $values);
        $this->assertArrayHasKey(0, $values);
        $this->assertArrayHasKey(1, $values);
        $this->assertEquals('2017-09-01', $values[0]);
        $this->assertEquals('2017-09-13', $values[1]);
    }

    public function testFactoryWithStringAndArrayedValue()
    {
        $condition = where('CURRENT_DATE = ?', ['2017-09-13']);
        $this->assertEquals('CURRENT_DATE = ?', (string) $condition);
        $values = $condition->getValues();
        $this->assertCount(1, $values);
        $this->assertArrayHasKey(0, $values);
        $this->assertEquals('2017-09-13', $values[0]);
    }

    public function testFactoryWithStringAndNamedArrayedValue()
    {
        $condition = where('CURRENT_DATE BETWEEN :start_date AND :end_date, ', ['start_date' => '2017-09-01', 'end_date' => '2017-09-13']);
        $this->assertEquals('CURRENT_DATE BETWEEN :start_date AND :end_date, ', (string) $condition);
        $values = $condition->getValues();
        $this->assertCount(2, $values);
        $this->assertArrayHasKey('start_date', $values);
        $this->assertArrayHasKey('end_date', $values);
        $this->assertEquals('2017-09-01', $values['start_date']);
        $this->assertEquals('2017-09-13', $values['end_date']);
    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    public function testFactoryWithStringAndInvalidValues()
    {
        where('CURRENT_DATE BETWEEN :start_date AND :end_date, ', ['start_date' => '2017-09-01'], ['end_date' => '2017-09-13']);
    }

    public function testFactoryWithExistingExpression()
    {
        $condition = where('CURRENT_DATE = ?', '2017-09-13');
        $condition = where($condition);
        $this->assertEquals('CURRENT_DATE = ?', (string) $condition);
        $values = $condition->getValues();
        $this->assertCount(1, $values);
        $this->assertArrayHasKey(0, $values);
        $this->assertEquals('2017-09-13', $values[0]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFactoryWithExistingExpressionCannotAddParameters()
    {
        $condition = where('CURRENT_DATE = ?', '2017-09-13');
        $condition = where($condition, 'foo');
    }

    public function testGroup()
    {
        $condition = where('CURRENT_DATE = ?', '2017-09-13');
        $condition = group($condition);
        $this->assertEquals('(CURRENT_DATE = ?)', (string) $condition);
        $values = $condition->getValues();
        $this->assertCount(1, $values);
        $this->assertArrayHasKey(0, $values);
        $this->assertEquals('2017-09-13', $values[0]);
    }

    public function testAnd()
    {
        $condition = where('CURRENT_DATE = ?', '2017-09-13');
        $condition = $condition->and('order_id = ?', 123456);
        $this->assertEquals('CURRENT_DATE = ? AND order_id = ?', (string) $condition);
        $values = $condition->getValues();
        $this->assertCount(2, $values);
        $this->assertEquals('2017-09-13', $values[0]);
        $this->assertEquals(123456, $values[1]);
    }

    public function testOr()
    {
        $condition = where('CURRENT_DATE = ?', '2017-09-13');
        $condition = $condition->or('order_id = ?', 123456);
        $this->assertEquals('CURRENT_DATE = ? OR order_id = ?', (string) $condition);
        $values = $condition->getValues();
        $this->assertCount(2, $values);
        $this->assertEquals('2017-09-13', $values[0]);
        $this->assertEquals(123456, $values[1]);
    }
}
