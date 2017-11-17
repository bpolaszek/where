<?php

namespace BenTools\Where\Tests\UpdateQuery;

use function BenTools\Where\update;
use function BenTools\Where\group;
use function BenTools\Where\where;
use PHPUnit\Framework\TestCase;

class UpdateQueryBuilderTest extends TestCase
{

    public function testConstructor()
    {
        $query = update('foo');
        $this->assertEquals('UPDATE foo;', (string) $query);
        $this->assertEquals([], $query->getValues());
    }

    public function testFlags()
    {
        $query = update('foo')->withFlags('LOW_PRIORITY', 'IGNORE');
        $this->assertEquals('UPDATE LOW_PRIORITY IGNORE foo;', (string) $query);

        $query = $query->withFlags('DUMMY_FLAG');
        $this->assertEquals('UPDATE DUMMY_FLAG foo;', (string) $query);
    }

    public function testAddFlags()
    {
        $query = update('foo')->withFlags('LOW_PRIORITY', 'IGNORE');
        $this->assertEquals('UPDATE LOW_PRIORITY IGNORE foo;', (string) $query);

        $query = $query->withAddedFlags('DUMMY_FLAG');
        $this->assertEquals('UPDATE LOW_PRIORITY IGNORE DUMMY_FLAG foo;', (string) $query);
    }

    public function testTable()
    {
        $query = update('foo')->table('bar');
        $this->assertEquals('UPDATE bar;', (string) $query);
    }

    public function testSet()
    {
        $query = update('foos')->set('foo = ?', 'bar');
        $this->assertEquals('UPDATE foos SET foo = ?;', (string) $query);
        $this->assertEquals(['bar'], $query->getValues());

        $query = $query->set(group('foo = ?', 'bar'));
        $this->assertEquals('UPDATE foos SET (foo = ?);', (string) $query);
        $this->assertEquals(['bar'], $query->getValues());

        $query = $query->set(null);
        $this->assertEquals('UPDATE foos;', (string) $query);
        $this->assertEquals([], $query->getValues());
    }

    public function testAndSet()
    {
        $query = update('foos')->andSet('foo = ?', 'bar')->andSet('baz = ?', 'bat');
        $this->assertEquals('UPDATE foos SET foo = ?, baz = ?;', (string) $query);
        $this->assertEquals(['bar', 'bat'], $query->getValues());
    }

    public function testJoin()
    {
        $query = update('foo AS t1')->join('another_table AS t2');
        $this->assertEquals('UPDATE foo AS t1 JOIN another_table AS t2;', (string) $query);

        $query = update('foo AS t1')->join('another_table AS t2', 't2.id = t1.t_id');
        $this->assertEquals('UPDATE foo AS t1 JOIN another_table AS t2 ON t2.id = t1.t_id;', (string) $query);
        $this->assertEquals([], $query->getValues());

        $query = update('foo AS t1')->join('another_table AS t2', 't2.id = t1.t_id AND day = ?', date('Y-m-d'));
        $this->assertEquals('UPDATE foo AS t1 JOIN another_table AS t2 ON t2.id = t1.t_id AND day = ?;', (string) $query);
        $this->assertEquals([date('Y-m-d')], $query->getValues());

        $query = update('foo AS t1')->join('another_table AS t2', 't2.id = t1.t_id AND day = :day', ['day' => date('Y-m-d')]);
        $this->assertEquals('UPDATE foo AS t1 JOIN another_table AS t2 ON t2.id = t1.t_id AND day = :day;', (string) $query);
        $this->assertEquals(['day' => date('Y-m-d')], $query->getValues());

        $query = update('foo AS t1')->join('another_table AS t2', group('t2.id = t1.t_id AND day = :day', ['day' => date('Y-m-d')]));
        $this->assertEquals('UPDATE foo AS t1 JOIN another_table AS t2 ON (t2.id = t1.t_id AND day = :day);', (string) $query);
        $this->assertEquals(['day' => date('Y-m-d')], $query->getValues());
    }

    public function testInnerJoin()
    {
        $query = update('foo AS t1')->innerJoin('another_table AS t2');
        $this->assertEquals('UPDATE foo AS t1 INNER JOIN another_table AS t2;', (string) $query);
    }

    public function testOuterJoin()
    {
        $query = update('foo AS t1')->outerJoin('another_table AS t2');
        $this->assertEquals('UPDATE foo AS t1 OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testLeftJoin()
    {
        $query = update('foo AS t1')->leftJoin('another_table AS t2');
        $this->assertEquals('UPDATE foo AS t1 LEFT JOIN another_table AS t2;', (string) $query);
    }

    public function testLeftOuterJoin()
    {
        $query = update('foo AS t1')->leftOuterJoin('another_table AS t2');
        $this->assertEquals('UPDATE foo AS t1 LEFT OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testRightJoin()
    {
        $query = update('foo AS t1')->rightJoin('another_table AS t2');
        $this->assertEquals('UPDATE foo AS t1 RIGHT JOIN another_table AS t2;', (string) $query);
    }

    public function testRightOuterJoin()
    {
        $query = update('foo AS t1')->rightOuterJoin('another_table AS t2');
        $this->assertEquals('UPDATE foo AS t1 RIGHT OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testFullJoin()
    {
        $query = update('foo AS t1')->fullJoin('another_table AS t2');
        $this->assertEquals('UPDATE foo AS t1 FULL JOIN another_table AS t2;', (string) $query);
    }

    public function testFullOuterJoin()
    {
        $query = update('foo AS t1')->fullOuterJoin('another_table AS t2');
        $this->assertEquals('UPDATE foo AS t1 FULL OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testResetJoins()
    {
        $query = update('foo AS t1')->join('another_table AS t2')->join('a_third_table AS t3')->resetJoins();
        $this->assertEquals('UPDATE foo AS t1;', (string) $query);
    }

    public function testWithoutJoin()
    {
        $query = update('foo AS t1')->join('another_table AS t2')->join('a_third_table AS t3')->withoutJoin('another_table AS t2');
        $this->assertEquals('UPDATE foo AS t1 JOIN a_third_table AS t3;', (string) $query);
    }

    public function testWhere()
    {
        $query = update('foos')->where('foo = ?', 'bar');
        $this->assertEquals('UPDATE foos WHERE foo = ?;', (string) $query);
        $this->assertEquals(['bar'], $query->getValues());

        $query = $query->where(group('foo = ?', 'bar'));
        $this->assertEquals('UPDATE foos WHERE (foo = ?);', (string) $query);
        $this->assertEquals(['bar'], $query->getValues());

        $query = $query->where(null);
        $this->assertEquals('UPDATE foos;', (string) $query);
        $this->assertEquals([], $query->getValues());
    }

    public function testAndWhere()
    {
        $query = update('foos')->andWhere('foo = ?', 'bar')->andWhere('baz = ?', 'bat');
        $this->assertEquals('UPDATE foos WHERE foo = ? AND baz = ?;', (string) $query);
        $this->assertEquals(['bar', 'bat'], $query->getValues());
    }

    public function testOrWhere()
    {
        $query = update('foos')->andWhere('foo = ?', 'bar')->orWhere('baz = ?', 'bat');
        $this->assertEquals('UPDATE foos WHERE foo = ? OR baz = ?;', (string) $query);
        $this->assertEquals(['bar', 'bat'], $query->getValues());
    }

    public function testOrderBy()
    {
        $query = update('foos')->orderBy('bar');
        $this->assertEquals('UPDATE foos ORDER BY bar;', (string) $query);

        $query = $query->orderBy('foo DESC');
        $this->assertEquals('UPDATE foos ORDER BY foo DESC;', (string) $query);
    }

    public function testAndOrderBy()
    {
        $query = update('foos')->orderBy('bar');
        $this->assertEquals('UPDATE foos ORDER BY bar;', (string) $query);

        $query = $query->andOrderBy('foo DESC');
        $this->assertEquals('UPDATE foos ORDER BY bar, foo DESC;', (string) $query);
    }

    public function testLimit()
    {
        $query = update('foos')->limit(10);
        $this->assertEquals('UPDATE foos LIMIT 10;', (string) $query);

        $query = $query->limit(null);
        $this->assertEquals('UPDATE foos;', (string) $query);
    }

    public function testEnd()
    {
        $query = update('foos');
        $this->assertEquals('UPDATE foos;', (string) $query);
        $query = $query->end();
        $this->assertEquals('UPDATE foos', (string) $query);
        $query = $query->end('||');
        $this->assertEquals('UPDATE foos||', (string) $query);
    }

    public function testValues()
    {
        $query = update('my_table as a')
            ->set('foo = ?', 'bar')
            ->innerJoin('second_table as b', where('b.id = a.b_id')->and('b.foo = ?', 'baz'))
            ->leftJoin('third_table as c', 'c.range BETWEEN ? AND ?', 10000, 15000)
            ->where('foo LIKE :foo', ['foo' => 'bar'])
        ;
        $this->assertEquals('UPDATE my_table as a INNER JOIN second_table as b ON b.id = a.b_id AND b.foo = ? LEFT JOIN third_table as c ON c.range BETWEEN ? AND ? SET foo = ? WHERE foo LIKE :foo;', (string) $query);
        $values = [
            'baz',
            10000,
            15000,
            'bar',
            'foo' => 'bar',
        ];
        $this->assertEquals($values, $query->getValues());
    }
}
