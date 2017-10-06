<?php

namespace BenTools\Where\Tests\InsertQuery;

use function BenTools\Where\insert;
use PHPUnit\Framework\TestCase;

class InsertQueryBuilderTest extends TestCase
{

    public function testDefault()
    {
        $query = insert(['id' => 1, 'name' => 'foo'], ['id' => 2, 'name' => 'bar'])
            ->into('foos');
        $this->assertEquals("INSERT INTO foos (id, name) VALUES (?, ?), (?, ?);", (string) $query);
        $query = $query->withEscaper('`');
        $this->assertEquals("INSERT INTO `foos` (`id`, `name`) VALUES (?, ?), (?, ?);", (string) $query);
        $this->assertEquals([1, 'foo', 2, 'bar'], $query->getValues());
    }

    public function testValues()
    {
        $query = insert()->into('foos');

        $query = $query->withValues(['id' => 1, 'name' => 'foo']);
        $this->assertEquals("INSERT INTO foos (id, name) VALUES (?, ?);", (string) $query);
        $this->assertEquals([1, 'foo'], $query->getValues());

        $query = $query->withValues(['id' => 2, 'name' => 'bar']);
        $this->assertEquals("INSERT INTO foos (id, name) VALUES (?, ?);", (string) $query);
        $this->assertEquals([2, 'bar'], $query->getValues());

        $query = $query->and(['id' => 1, 'name' => 'foo']);
        $this->assertEquals("INSERT INTO foos (id, name) VALUES (?, ?), (?, ?);", (string) $query);
        $this->assertEquals([2, 'bar', 1, 'foo'], $query->getValues());
    }

    public function testFlagsAndKeyword()
    {
        $query = insert(['id' => 1, 'name' => 'foo'])
            ->into('foos');
        $query = $query->withFlags('IGNORE');
        $this->assertEquals("INSERT IGNORE INTO foos (id, name) VALUES (?, ?);", (string) $query);
        $query = $query->withFlags('DELAYED');
        $this->assertEquals("INSERT DELAYED INTO foos (id, name) VALUES (?, ?);", (string) $query);
        $query = $query->withAddedFlags('IGNORE');
        $this->assertEquals("INSERT DELAYED IGNORE INTO foos (id, name) VALUES (?, ?);", (string) $query);
        $query = $query->withFlags()->withMainKeyword('REPLACE');
        $this->assertEquals("REPLACE INTO foos (id, name) VALUES (?, ?);", (string) $query);
    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    public function testInconsistentData()
    {
        $query = insert(['id' => 1, 'name' => 'foo'], ['id' => 2, 'label' => 'bar'])
            ->into('foos');
    }

    public function testIntersectColumns()
    {
        $query = insert(['id' => 1, 'name' => 'foo'], ['id' => 2, 'name' => 'bar'])
            ->into('foos', 'name');
        $this->assertEquals("INSERT INTO foos (name) VALUES (?), (?);", (string) $query);
        $this->assertEquals(['foo', 'bar'], $query->getValues());
    }

    /**
     * @expectedException  \InvalidArgumentException
     */
    public function testIntersectColumnsWithInconsistentData()
    {
        $query = insert(['id' => 1, 'name' => 'foo'], ['id' => 2, 'label' => 'bar'])
            ->into('foos', 'name');
    }

    public function testDuplicateKeyStatement()
    {
        $today = date('Y-m-d');
        $query = insert(['id' => 1, 'name' => 'foo', 'updated_at' => $today])
            ->into('foos')
            ->withEscaper('`')
            ->onDuplicateKeyUpdate(['updated_at' => 'VALUES(updated_at)', 'was_updated' => 1])
            ->end()
        ;
        $this->assertEquals(
            "INSERT INTO `foos` (`id`, `name`, `updated_at`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `updated_at` = VALUES(updated_at), `was_updated` = 1",
            (string) $query
        );
        $this->assertEquals([1, 'foo', $today], $query->getValues());
    }

    public function testSplit()
    {
        $dataset = [
            [
                'id'   => 1,
                'name' => 'foo',
            ],
            [
                'id'   => 2,
                'name' => 'bar',
            ],
            [
                'id'   => 3,
                'name' => 'baz',
            ],
        ];
        $query = insert(...$dataset)->into('foos')->withEscaper('`');

        $queries = iterator_to_array($query->split(2));
        $this->assertCount(2, $queries);

        $this->assertEquals("INSERT INTO `foos` (`id`, `name`) VALUES (?, ?), (?, ?);", (string) $queries[0]);
        $this->assertEquals([1, 'foo', 2, 'bar'], $queries[0]->getValues());

        $this->assertEquals("INSERT INTO `foos` (`id`, `name`) VALUES (?, ?);", (string) $queries[1]);
        $this->assertEquals([3, 'baz'], $queries[1]->getValues());
    }
}
