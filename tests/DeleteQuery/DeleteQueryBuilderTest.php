<?php

namespace BenTools\Where\Tests\DeleteQuery;

use function BenTools\Where\delete;
use function BenTools\Where\group;
use function BenTools\Where\where;
use PHPUnit\Framework\TestCase;

class DeleteQueryBuilderTest extends TestCase
{

    public function testConstructor()
    {
        $query = delete()->from('foo');
        $this->assertEquals('DELETE FROM foo;', (string) $query);
        $this->assertEquals([], $query->getValues());

        $query = delete('foo')->from('foo')->join('bar');
        $this->assertEquals('DELETE foo FROM foo JOIN bar;', (string) $query);
    }

    public function testFlags()
    {
        $query = delete()->withFlags('QUICK', 'LOW_PRIORITY')->from('foo');
        $this->assertEquals('DELETE QUICK LOW_PRIORITY FROM foo;', (string) $query);

        $query = $query->withFlags('IGNORE');
        $this->assertEquals('DELETE IGNORE FROM foo;', (string) $query);
    }

    public function testAddFlags()
    {
        $query = delete()->from('foo')->withFlags('QUICK', 'LOW_PRIORITY');
        $this->assertEquals('DELETE QUICK LOW_PRIORITY FROM foo;', (string) $query);

        $query = $query->withAddedFlags('IGNORE');
        $this->assertEquals('DELETE QUICK LOW_PRIORITY IGNORE FROM foo;', (string) $query);
    }

    public function testTables()
    {
        $query = delete()->from('foo, bar')->deleteOnlyFromTables('bar');
        $this->assertEquals('DELETE bar FROM foo, bar;', (string) $query);
    }

    public function testPartitions()
    {
        $query = delete()->from('foo')->withPartitions('p1', 'p2');
        $this->assertEquals('DELETE FROM foo PARTITION (p1, p2);', (string) $query);
        $query = $query->withPartitions('p3');
        $this->assertEquals('DELETE FROM foo PARTITION (p3);', (string) $query);
        $query = $query->withAddedPartitions('p4');
        $this->assertEquals('DELETE FROM foo PARTITION (p3, p4);', (string) $query);
        $query = $query->withPartitions(...[]);
        $this->assertEquals('DELETE FROM foo;', (string) $query);
    }

    public function testJoin()
    {
        $query = delete('foo')->from('my_table AS foo')->join('another_table AS t2');
        $this->assertEquals('DELETE foo FROM my_table AS foo JOIN another_table AS t2;', (string) $query);

        $query = delete('foo')->from('my_table AS foo')->join('another_table AS t2', 't2.id = t1.t_id');
        $this->assertEquals('DELETE foo FROM my_table AS foo JOIN another_table AS t2 ON t2.id = t1.t_id;', (string) $query);
        $this->assertEquals([], $query->getValues());

        $query = delete('foo')->from('my_table AS foo')->join('another_table AS t2', 't2.id = t1.t_id AND day = ?', date('Y-m-d'));
        $this->assertEquals('DELETE foo FROM my_table AS foo JOIN another_table AS t2 ON t2.id = t1.t_id AND day = ?;', (string) $query);
        $this->assertEquals([date('Y-m-d')], $query->getValues());

        $query = delete('foo')->from('my_table AS foo')->join('another_table AS t2', 't2.id = t1.t_id AND day = :day', ['day' => date('Y-m-d')]);
        $this->assertEquals('DELETE foo FROM my_table AS foo JOIN another_table AS t2 ON t2.id = t1.t_id AND day = :day;', (string) $query);
        $this->assertEquals(['day' => date('Y-m-d')], $query->getValues());

        $query = delete('foo')->from('my_table AS foo')->join('another_table AS t2', group('t2.id = t1.t_id AND day = :day', ['day' => date('Y-m-d')]));
        $this->assertEquals('DELETE foo FROM my_table AS foo JOIN another_table AS t2 ON (t2.id = t1.t_id AND day = :day);', (string) $query);
        $this->assertEquals(['day' => date('Y-m-d')], $query->getValues());
    }

    public function testInnerJoin()
    {
        $query = delete('foo')->from('my_table AS foo')->innerJoin('another_table AS t2');
        $this->assertEquals('DELETE foo FROM my_table AS foo INNER JOIN another_table AS t2;', (string) $query);
    }

    public function testOuterJoin()
    {
        $query = delete('foo')->from('my_table AS foo')->outerJoin('another_table AS t2');
        $this->assertEquals('DELETE foo FROM my_table AS foo OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testLeftJoin()
    {
        $query = delete('foo')->from('my_table AS foo')->leftJoin('another_table AS t2');
        $this->assertEquals('DELETE foo FROM my_table AS foo LEFT JOIN another_table AS t2;', (string) $query);
    }

    public function testLeftOuterJoin()
    {
        $query = delete('foo')->from('my_table AS foo')->leftOuterJoin('another_table AS t2');
        $this->assertEquals('DELETE foo FROM my_table AS foo LEFT OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testRightJoin()
    {
        $query = delete('foo')->from('my_table AS foo')->rightJoin('another_table AS t2');
        $this->assertEquals('DELETE foo FROM my_table AS foo RIGHT JOIN another_table AS t2;', (string) $query);
    }

    public function testRightOuterJoin()
    {
        $query = delete('foo')->from('my_table AS foo')->rightOuterJoin('another_table AS t2');
        $this->assertEquals('DELETE foo FROM my_table AS foo RIGHT OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testFullJoin()
    {
        $query = delete('foo')->from('my_table AS foo')->fullJoin('another_table AS t2');
        $this->assertEquals('DELETE foo FROM my_table AS foo FULL JOIN another_table AS t2;', (string) $query);
    }

    public function testFullOuterJoin()
    {
        $query = delete('foo')->from('my_table AS foo')->fullOuterJoin('another_table AS t2');
        $this->assertEquals('DELETE foo FROM my_table AS foo FULL OUTER JOIN another_table AS t2;', (string) $query);
    }

    public function testResetJoins()
    {
        $query = delete('foo')->from('my_table AS foo')->join('another_table AS t2')->join('a_third_table AS t3')->resetJoins();
        $this->assertEquals('DELETE foo FROM my_table AS foo;', (string) $query);
    }

    public function testWithoutJoin()
    {
        $query = delete('foo')->from('my_table AS foo')->join('another_table AS t2')->join('a_third_table AS t3')->withoutJoin('another_table AS t2');
        $this->assertEquals('DELETE foo FROM my_table AS foo JOIN a_third_table AS t3;', (string) $query);
    }

    public function testWhere()
    {
        $query = delete()->from('foos')->where('foo = ?', 'bar');
        $this->assertEquals('DELETE FROM foos WHERE foo = ?;', (string) $query);
        $this->assertEquals(['bar'], $query->getValues());

        $query = $query->where(group('foo = ?', 'bar'));
        $this->assertEquals('DELETE FROM foos WHERE (foo = ?);', (string) $query);
        $this->assertEquals(['bar'], $query->getValues());

        $query = $query->where(null);
        $this->assertEquals('DELETE FROM foos;', (string) $query);
        $this->assertEquals([], $query->getValues());
    }

    public function testAndWhere()
    {
        $query = delete()->from('foos')->andWhere('foo = ?', 'bar')->andWhere('baz = ?', 'bat');
        $this->assertEquals('DELETE FROM foos WHERE foo = ? AND baz = ?;', (string) $query);
        $this->assertEquals(['bar', 'bat'], $query->getValues());
    }

    public function testOrWhere()
    {
        $query = delete()->from('foos')->andWhere('foo = ?', 'bar')->orWhere('baz = ?', 'bat');
        $this->assertEquals('DELETE FROM foos WHERE foo = ? OR baz = ?;', (string) $query);
        $this->assertEquals(['bar', 'bat'], $query->getValues());
    }

    public function testOrderBy()
    {
        $query = delete()->from('foos')->orderBy('bar');
        $this->assertEquals('DELETE FROM foos ORDER BY bar;', (string) $query);

        $query = $query->orderBy('foo DESC');
        $this->assertEquals('DELETE FROM foos ORDER BY foo DESC;', (string) $query);
    }

    public function testAndOrderBy()
    {
        $query = delete()->from('foos')->orderBy('bar');
        $this->assertEquals('DELETE FROM foos ORDER BY bar;', (string) $query);

        $query = $query->andOrderBy('foo DESC');
        $this->assertEquals('DELETE FROM foos ORDER BY bar, foo DESC;', (string) $query);
    }

    public function testLimit()
    {
        $query = delete()->from('foos')->limit(10);
        $this->assertEquals('DELETE FROM foos LIMIT 10;', (string) $query);

        $query = $query->limit(null);
        $this->assertEquals('DELETE FROM foos;', (string) $query);
    }

    public function testEnd()
    {
        $query = delete();
        $this->assertEquals('DELETE;', (string) $query);
        $query = $query->end();
        $this->assertEquals('DELETE', (string) $query);
        $query = $query->end('||');
        $this->assertEquals('DELETE||', (string) $query);
    }

    public function testValues()
    {
        $query = delete()
            ->from('foo as a')
            ->innerJoin('second_table as b', where('b.id = a.b_id')->and('b.foo = ?', 'bar'))
            ->leftJoin('third_table as c', 'c.range BETWEEN ? AND ?', 10000, 15000)
            ->where('foo LIKE :foo', ['foo' => 'bar'])
        ;
        $this->assertEquals('DELETE FROM foo as a INNER JOIN second_table as b ON b.id = a.b_id AND b.foo = ? LEFT JOIN third_table as c ON c.range BETWEEN ? AND ? WHERE foo LIKE :foo;', (string) $query);
        $values = [
            'bar',
            10000,
            15000,
            'foo' => 'bar',
        ];
        $this->assertEquals($values, $query->getValues());
    }
}
