<?php

namespace BenTools\Where\Tests\SelectQuery;

use function BenTools\Where\select;
use function BenTools\Where\group;
use function BenTools\Where\where;
use PHPUnit\Framework\TestCase;

class SelectQueryBuilderTest extends TestCase
{

    public function testConstructor()
    {
        $query = select();
        $this->assertEquals('SELECT *;', (string) $query);
        $this->assertEquals([], $query->getValues());

        $query = select('foo', 'bar');
        $this->assertEquals('SELECT foo, bar;', (string) $query);
    }

    public function testFlags()
    {
        $query = select('foo', 'bar')->withFlags('SQL_CACHE', 'SQL_CALC_FOUND_ROWS');
        $this->assertEquals('SELECT SQL_CACHE SQL_CALC_FOUND_ROWS foo, bar;', (string) $query);

        $query = $query->withFlags('SQL_NO_CACHE');
        $this->assertEquals('SELECT SQL_NO_CACHE foo, bar;', (string) $query);
    }

    public function testAddFlags()
    {
        $query = select('foo', 'bar')->withFlags('SQL_CACHE', 'SQL_CALC_FOUND_ROWS');
        $this->assertEquals('SELECT SQL_CACHE SQL_CALC_FOUND_ROWS foo, bar;', (string) $query);

        $query = $query->withAddedFlags('SQL_NO_CACHE');
        $this->assertEquals('SELECT SQL_CACHE SQL_CALC_FOUND_ROWS SQL_NO_CACHE foo, bar;', (string) $query);
    }

    public function testColumns()
    {
        $query = select('foo')->withColumns('bar');
        $this->assertEquals('SELECT bar;', (string) $query);
    }

    public function testAddColumns()
    {
        $query = select('foo')->withColumns('bar')->withAddedColumns('baz', 'bat');
        $this->assertEquals('SELECT bar, baz, bat;', (string) $query);
    }

    public function testDistinct()
    {
        $query = select('foo')->distinct();
        $this->assertEquals('SELECT DISTINCT foo;', (string) $query);
        $query = select('foo')->distinct(true);
        $this->assertEquals('SELECT DISTINCT foo;', (string) $query);
        $query = select('foo')->distinct(false);
        $this->assertEquals('SELECT foo;', (string) $query);
    }

    public function testFrom()
    {
        $query = select('foo')->from('my_table');
        $this->assertEquals('SELECT foo FROM my_table;', (string) $query);
        $query = $query->from(null);
        $this->assertEquals('SELECT foo;', (string) $query);
    }

    public function testJoin()
    {
        $query = select('foo')->from('my_table AS t1')->join('another_table AS t2');
        $this->assertEquals('SELECT foo FROM my_table AS t1 JOIN another_table AS t2;', (string) $query);

        $query = select('foo')->from('my_table AS t1')->join('another_table AS t2', 't2.id = t1.t_id');
        $this->assertEquals('SELECT foo FROM my_table AS t1 JOIN another_table AS t2 ON t2.id = t1.t_id;', (string) $query);
        $this->assertEquals([], $query->getValues());

        $query = select('foo')->from('my_table AS t1')->join('another_table AS t2', 't2.id = t1.t_id AND day = ?', date('Y-m-d'));
        $this->assertEquals('SELECT foo FROM my_table AS t1 JOIN another_table AS t2 ON t2.id = t1.t_id AND day = ?;', (string) $query);
        $this->assertEquals([date('Y-m-d')], $query->getValues());

        $query = select('foo')->from('my_table AS t1')->join('another_table AS t2', 't2.id = t1.t_id AND day = :day', ['day' => date('Y-m-d')]);
        $this->assertEquals('SELECT foo FROM my_table AS t1 JOIN another_table AS t2 ON t2.id = t1.t_id AND day = :day;', (string) $query);
        $this->assertEquals(['day' => date('Y-m-d')], $query->getValues());

        $query = select('foo')->from('my_table AS t1')->join('another_table AS t2', group('t2.id = t1.t_id AND day = :day', ['day' => date('Y-m-d')]));
        $this->assertEquals('SELECT foo FROM my_table AS t1 JOIN another_table AS t2 ON (t2.id = t1.t_id AND day = :day);', (string) $query);
        $this->assertEquals(['day' => date('Y-m-d')], $query->getValues());
    }

    public function testInnerJoin()
    {
        $query = select('foo')->from('my_table AS t1')->innerJoin('another_table AS t2');
        $this->assertEquals('SELECT foo FROM my_table AS t1 INNER JOIN another_table AS t2;', (string) $query);
    }

    public function testOuterJoin()
    {
        $query = select('foo')->from('my_table AS t1')->outerJoin('another_table AS t2');
        $this->assertEquals('SELECT foo FROM my_table AS t1 OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testLeftJoin()
    {
        $query = select('foo')->from('my_table AS t1')->leftJoin('another_table AS t2');
        $this->assertEquals('SELECT foo FROM my_table AS t1 LEFT JOIN another_table AS t2;', (string) $query);
    }

    public function testLeftOuterJoin()
    {
        $query = select('foo')->from('my_table AS t1')->leftOuterJoin('another_table AS t2');
        $this->assertEquals('SELECT foo FROM my_table AS t1 LEFT OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testRightJoin()
    {
        $query = select('foo')->from('my_table AS t1')->rightJoin('another_table AS t2');
        $this->assertEquals('SELECT foo FROM my_table AS t1 RIGHT JOIN another_table AS t2;', (string) $query);
    }

    public function testRightOuterJoin()
    {
        $query = select('foo')->from('my_table AS t1')->rightOuterJoin('another_table AS t2');
        $this->assertEquals('SELECT foo FROM my_table AS t1 RIGHT OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testFullJoin()
    {
        $query = select('foo')->from('my_table AS t1')->fullJoin('another_table AS t2');
        $this->assertEquals('SELECT foo FROM my_table AS t1 FULL JOIN another_table AS t2;', (string) $query);
    }

    public function testFullOuterJoin()
    {
        $query = select('foo')->from('my_table AS t1')->fullOuterJoin('another_table AS t2');
        $this->assertEquals('SELECT foo FROM my_table AS t1 FULL OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testResetJoins()
    {
        $query = select('foo')->from('my_table AS t1')->join('another_table AS t2')->join('a_third_table AS t3')->resetJoins();
        $this->assertEquals('SELECT foo FROM my_table AS t1;', (string) $query);
    }

    public function testWithoutJoin()
    {
        $query = select('foo')->from('my_table AS t1')->join('another_table AS t2')->join('a_third_table AS t3')->withoutJoin('another_table AS t2');
        $this->assertEquals('SELECT foo FROM my_table AS t1 JOIN a_third_table AS t3;', (string) $query);
    }

    public function testWhere()
    {
        $query = select()->from('foos')->where('foo = ?', 'bar');
        $this->assertEquals('SELECT * FROM foos WHERE foo = ?;', (string) $query);
        $this->assertEquals(['bar'], $query->getValues());

        $query = $query->where(group('foo = ?', 'bar'));
        $this->assertEquals('SELECT * FROM foos WHERE (foo = ?);', (string) $query);
        $this->assertEquals(['bar'], $query->getValues());

        $query = $query->where(null);
        $this->assertEquals('SELECT * FROM foos;', (string) $query);
        $this->assertEquals([], $query->getValues());
    }

    public function testAndWhere()
    {
        $query = select()->from('foos')->andWhere('foo = ?', 'bar')->andWhere('baz = ?', 'bat');
        $this->assertEquals('SELECT * FROM foos WHERE foo = ? AND baz = ?;', (string) $query);
        $this->assertEquals(['bar', 'bat'], $query->getValues());
    }

    public function testOrWhere()
    {
        $query = select()->from('foos')->andWhere('foo = ?', 'bar')->orWhere('baz = ?', 'bat');
        $this->assertEquals('SELECT * FROM foos WHERE foo = ? OR baz = ?;', (string) $query);
        $this->assertEquals(['bar', 'bat'], $query->getValues());
    }

    public function testGroupBy()
    {
        $query = select()->from('foos')->groupBy('bar');
        $this->assertEquals('SELECT * FROM foos GROUP BY bar;', (string) $query);

        $query = $query->groupBy('foo');
        $this->assertEquals('SELECT * FROM foos GROUP BY foo;', (string) $query);
    }

    public function testAndGroupBy()
    {
        $query = select()->from('foos')->groupBy('bar');
        $this->assertEquals('SELECT * FROM foos GROUP BY bar;', (string) $query);

        $query = $query->andGroupBy('foo');
        $this->assertEquals('SELECT * FROM foos GROUP BY bar, foo;', (string) $query);
    }

    public function testHaving()
    {
        $query = select()->from('foos')->having('foo = ?', 'bar');
        $this->assertEquals('SELECT * FROM foos HAVING foo = ?;', (string) $query);
        $this->assertEquals(['bar'], $query->getValues());

        $query = $query->having(group('foo = ?', 'bar'));
        $this->assertEquals('SELECT * FROM foos HAVING (foo = ?);', (string) $query);
        $this->assertEquals(['bar'], $query->getValues());

        $query = $query->having(null);
        $this->assertEquals('SELECT * FROM foos;', (string) $query);
        $this->assertEquals([], $query->getValues());
    }

    public function testAndHaving()
    {
        $query = select()->from('foos')->andHaving('foo = ?', 'bar')->andHaving('baz = ?', 'bat');
        $this->assertEquals('SELECT * FROM foos HAVING foo = ? AND baz = ?;', (string) $query);
        $this->assertEquals(['bar', 'bat'], $query->getValues());
    }

    public function testOrHaving()
    {
        $query = select()->from('foos')->andHaving('foo = ?', 'bar')->orHaving('baz = ?', 'bat');
        $this->assertEquals('SELECT * FROM foos HAVING foo = ? OR baz = ?;', (string) $query);
        $this->assertEquals(['bar', 'bat'], $query->getValues());
    }

    public function testOrderBy()
    {
        $query = select()->from('foos')->orderBy('bar');
        $this->assertEquals('SELECT * FROM foos ORDER BY bar;', (string) $query);

        $query = $query->orderBy('foo DESC');
        $this->assertEquals('SELECT * FROM foos ORDER BY foo DESC;', (string) $query);
    }

    public function testAndOrderBy()
    {
        $query = select()->from('foos')->orderBy('bar');
        $this->assertEquals('SELECT * FROM foos ORDER BY bar;', (string) $query);

        $query = $query->andOrderBy('foo DESC');
        $this->assertEquals('SELECT * FROM foos ORDER BY bar, foo DESC;', (string) $query);
    }

    public function testLimitOffset()
    {
        $query = select()->from('foos')->limit(10);
        $this->assertEquals('SELECT * FROM foos LIMIT 10;', (string) $query);

        $query = $query->limit(null);
        $this->assertEquals('SELECT * FROM foos;', (string) $query);

        $query = $query->limit(10)->offset(50);
        $this->assertEquals('SELECT * FROM foos LIMIT 10 OFFSET 50;', (string) $query);

        $query = $query->offset(null);
        $this->assertEquals('SELECT * FROM foos LIMIT 10;', (string) $query);
    }

    public function testValues()
    {
        $query = select()
            ->from('my_table as a')
            ->innerJoin('second_table as b', where('b.id = a.b_id')->and('b.foo = ?', 'bar'))
            ->leftJoin('third_table as c', 'c.range BETWEEN ? AND ?', 10000, 15000)
            ->where('foo LIKE :foo', ['foo' => 'bar'])
            ->having('bar = ?', 'baz')
            ;
        $this->assertEquals('SELECT * FROM my_table as a INNER JOIN second_table as b ON b.id = a.b_id AND b.foo = ? LEFT JOIN third_table as c ON c.range BETWEEN ? AND ? WHERE foo LIKE :foo HAVING bar = ?;', (string) $query);
        $values = [
            'bar',
            10000,
            15000,
            'foo' => 'bar',
            'baz'
        ];
        $this->assertEquals($values, $query->getValues());
    }
}
