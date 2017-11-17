<?php

namespace BenTools\Where\Tests\Helper;

use BenTools\Where\Expression\Expression;
use PHPUnit\Framework\TestCase;
use function BenTools\Where\field;

class FieldHelperTest extends TestCase
{

    public function testIn()
    {
        $field = field('field_name');
        $values = ['foo', 'bar'];
        $expr = $field->in($values);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name IN (?, ?)', (string) $expr);
        $this->assertEquals($values, $expr->getValues());
    }

    public function testNotIn()
    {
        $field = field('field_name');
        $values = ['foo', 'bar'];
        $expr = $field->notIn($values);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name NOT IN (?, ?)', (string) $expr);
        $this->assertEquals($values, $expr->getValues());
    }

    public function testEquals()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->equals($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name = ?', (string) $expr);
        $this->assertEquals([$value], $expr->getValues());
    }

    public function testNotEquals()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->notEquals($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name <> ?', (string) $expr);
        $this->assertEquals([$value], $expr->getValues());
    }

    public function testLt()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->lt($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name < ?', (string) $expr);
        $this->assertEquals([$value], $expr->getValues());
    }

    public function testLte()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->lte($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name <= ?', (string) $expr);
        $this->assertEquals([$value], $expr->getValues());
    }

    public function testGt()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->gt($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name > ?', (string) $expr);
        $this->assertEquals([$value], $expr->getValues());
    }

    public function testGte()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->gte($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name >= ?', (string) $expr);
        $this->assertEquals([$value], $expr->getValues());
    }

    public function testBetween()
    {
        $field = field('field_name');
        $values = ['foo', 'bar'];
        $expr = $field->between(...$values);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name BETWEEN ? AND ?', (string) $expr);
        $this->assertEquals($values, $expr->getValues());
    }

    public function testNotBetween()
    {
        $field = field('field_name');
        $values = ['foo', 'bar'];
        $expr = $field->notBetween(...$values);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name NOT BETWEEN ? AND ?', (string) $expr);
        $this->assertEquals($values, $expr->getValues());
    }

    public function testLike()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->like($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name LIKE ?', (string) $expr);
        $this->assertEquals(['%' . $value . '%'], $expr->getValues());
    }

    public function testNotLike()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->notLike($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name NOT LIKE ?', (string) $expr);
        $this->assertEquals(['%' . $value . '%'], $expr->getValues());
    }

    public function testStartsWith()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->startsWith($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name LIKE ?', (string) $expr);
        $this->assertEquals([$value . '%'], $expr->getValues());
    }

    public function testNotStartsWith()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->notStartsWith($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name NOT LIKE ?', (string) $expr);
        $this->assertEquals([$value . '%'], $expr->getValues());
    }

    public function testEndsWith()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->endsWith($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name LIKE ?', (string) $expr);
        $this->assertEquals(['%' . $value], $expr->getValues());
    }

    public function testNotEndsWith()
    {
        $field = field('field_name');
        $value = 'foo';
        $expr = $field->notEndsWith($value);
        $this->assertInstanceOf(Expression::class, $expr);
        $this->assertEquals('field_name NOT LIKE ?', (string) $expr);
        $this->assertEquals(['%' . $value], $expr->getValues());
    }
}
