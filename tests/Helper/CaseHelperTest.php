<?php

namespace BenTools\Where\Tests\Helper;

use BenTools\Where\Expression\Expression;
use PHPUnit\Framework\TestCase;
use function BenTools\Where\conditionnal;
use function BenTools\Where\when;
use function BenTools\Where\where;

class CaseHelperTest extends TestCase
{

    public function testCreateWithField()
    {
        $expression = conditionnal('field');
        $this->assertInstanceOf(Expression::class, $expression);
        $this->assertEquals('CASE field END', (string) $expression);

        $expression = conditionnal(where('field = ?', 'foo'));
        $this->assertEquals('CASE field = ? END', (string) $expression);
        $this->assertEquals(['foo'], $expression->getValues());
    }

    public function testCreateWithoutField()
    {
        $expression = when('field = ?', 'foo')->then('foo = ?', 'bar');
        $this->assertEquals('CASE WHEN field = ? THEN foo = ? END', (string) $expression);
        $this->assertEquals(['foo', 'bar'], $expression->getValues());
    }

    public function testCreateWithMultipleExpressions()
    {
        $expression = conditionnal('foo = ?', 'bar')
            ->when('fruit = ?', 'apple')
            ->then('color = ?', 'green')
            ->when('fruit = ?', 'banana')
            ->then('color = ?', 'yellow')
            ->else('color = ?', 'black');

        $this->assertEquals('CASE foo = ? WHEN fruit = ? THEN color = ? WHEN fruit = ? THEN color = ? ELSE color = ? END', (string) $expression);
        $this->assertEquals([
            'bar',
            'apple',
            'green',
            'banana',
            'yellow',
            'black',
        ], $expression->getValues());

    }

    /**
     * @expectedException \RuntimeException
     */
    public function testThenWithoutAssociatedWhenThrowsAnException()
    {
        when('foo')->then('bar')->then('baz');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testWhenWithoutAssociatedThenThrowsAnException()
    {
        when('foo')->when('bar');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testLock()
    {
        when('fruit = ?', 'apple')
            ->then('color = ?', 'green')
            ->end()
            ->when('fruit = ?', 'banana');
    }

    public function testAlias()
    {
        $expression = conditionnal('foo = ?', 'bar')
            ->when('fruit = ?', 'apple')
            ->then('color = ?', 'green')
            ->when('fruit = ?', 'banana')
            ->then('color = ?', 'yellow')
            ->else('color = ?', 'black')
            ->end()
            ->as('foo');

        $this->assertEquals('CASE foo = ? WHEN fruit = ? THEN color = ? WHEN fruit = ? THEN color = ? ELSE color = ? END AS foo', (string) $expression);
    }

}
