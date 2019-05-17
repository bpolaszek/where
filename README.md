[![Latest Stable Version](https://poser.pugx.org/bentools/where/v/stable)](https://packagist.org/packages/bentools/where)
[![License](https://poser.pugx.org/bentools/where/license)](https://packagist.org/packages/bentools/where)
[![Build Status](https://img.shields.io/travis/bpolaszek/where/master.svg?style=flat-square)](https://travis-ci.org/bpolaszek/where)
[![Coverage Status](https://coveralls.io/repos/github/bpolaszek/where/badge.svg?branch=master)](https://coveralls.io/github/bpolaszek/where?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/bpolaszek/where.svg?style=flat-square)](https://scrutinizer-ci.com/g/bpolaszek/where)
[![Total Downloads](https://poser.pugx.org/bentools/where/downloads)](https://packagist.org/packages/bentools/where)

# Where

The simplest fluent SQL query builder ever.

Built in PHP7.1 with immutability in mind.

## Features

* Framework agnostic, connection agnostic (you just render a string and an array of values)
* Natural language: where, and, or, ...
* Support named and numeric placeholders
* Build complex, nested WHERE conditions
* Helpers for building `SELECT`, `INSERT`, `UPDATE`, `DELETE`, `REPLACE` queries
* Helpers for SQL functions like `IN`, `BETWEEN`, `IS NULL`, `CASE ... WHEN`

## Why?

In most cases simple SQL queries are fine. 

But if your application logic is designed in a way that several classes / methods can modify an SQL query (like the Visitor pattern), then you'll probably need query builder 
(you can define LIMIT / OFFSET before WHERE for instance, and the query will be rendered in the correct order).

Conditions builder
--------------------------
**Where** allows you to build your conditions with **Expressions**. **Expressions** are objects that can be:

* Simple expressions: `date_added = CURRENT_DATE`
* Composite expressions: `date_added = CURRENT_DATE OR date_added = SUBDATE(CURRENT_DATE, INTERVAL 1 DAY)`
* Group expressions: `(country = 'UK' OR country = 'BE')`
* Negated expressions: `NOT date_added = CURRENT_DATE`

An **Expression** object can also contain an array of parameters to bind (to avoid SQL injections).

You don't need to instanciate them. Just rely on the powerful functions the library offers:
```php
require_once __DIR__ . '/vendor/autoload.php';

use function BenTools\Where\group;
use function BenTools\Where\not;
use function BenTools\Where\where;

$where = where('country IN (?, ?)', ['FRA', 'UK'])
    ->and(
        not(
            group(
                where('continent = ?', 'Europe')
                    ->or('population < ?', 100000)
            )
        )
    );

print((string) $where);
print_r($where->getValues());
print_r($where->preview());  // For debugging purposes
```
Outputs:
```mysql
country IN (?, ?) AND NOT (continent = ? OR population < ?)
```
```php
Array
(
    [0] => FRA
    [1] => UK
    [2] => Europe
    [3] => 100000
)

```
```mysql
country IN ('FRA', 'UK') AND NOT (continent = 'Europe' OR population < 100000)
```

Every function `where()`, `group()`, `not()` accepts either an already instanciated **Expression** object, or a string and some optionnal parameters.

```php
$where = where('date > NOW()'); // valid
$where = where($where); // valid
$where = where(group($where)); // valid
$where = where(not($where)); // valid
$where = where('date = ?', date('Y-m-d')); // valid
$where = where('date BETWEEN ? AND ?', date('Y-m-d'), date('Y-m-d')); // valid
$where = where('date BETWEEN ? AND ?', [date('Y-m-d'), date('Y-m-d')]); // valid
$where = where('date BETWEEN :start AND :end', ['start' => date('Y-m-d'), 'end' => date('Y-m-d')]); // valid
$where = where('date BETWEEN :start AND :end', ['start' => date('Y-m-d')], ['end' => date('Y-m-d')]); // not valid
$where = where($where, date('Y-m-d'), date('Y-m-d')); // not valid (parameters already bound)
```
Thanks to the fluent interface, let your IDE guide you for the rest. Don't forget **Where** is always immutable: reassign `$where` everytime you do some changes.

Select Query Builder
-----------------------------
Now you've learnt how to build conditions, you'll see how building a whole select query is a piece of cake:
```php
require_once __DIR__ . '/vendor/autoload.php';

use function BenTools\Where\group;
use function BenTools\Where\not;
use function BenTools\Where\select;
use function BenTools\Where\where;

$select = select('b.id', 'b.name  AS book_name', 'a.name AS author_name')
    ->from('books as b')
    ->innerJoin('authors as a', 'a.id = b.author_id')
    ->limit(10)
    ->orderBy('YEAR(b.published_at) DESC', 'MONTH(b.published_at) DESC', 'b.name')
    ->where(
        group(
            where('b.series = ?', 'Harry Potter')->or('b.series IN (?, ?)', ['A Song of Ice and Fire', 'Game of Thrones'])
        )
            ->and('b.published_at >= ?', new \DateTime('2010-01-01'))
        ->and(
            not('b.reviewed_at BETWEEN ? AND ?', new \DateTime('2016-01-01'), new \DateTime('2016-01-31 23:59:59'))
        )
    );
print_r((string) $select); // The SQL string
print_r($select->preview()); // For debugging purposes
```

```mysql
SELECT b.id, b.name  AS book_name, a.name AS author_name 
FROM books as b 
INNER JOIN authors as a ON a.id = b.author_id 
WHERE (b.series = ? OR b.series IN (?, ?)) 
AND b.published_at >= ? 
AND NOT b.reviewed_at BETWEEN ? AND ? 
ORDER BY YEAR(b.published_at) DESC, MONTH(b.published_at) DESC, b.name 
LIMIT 10;
```

```mysql
SELECT b.id, b.name  AS book_name, a.name AS author_name 
FROM books as b 
INNER JOIN authors as a ON a.id = b.author_id 
WHERE (b.series = 'Harry Potter' OR b.series IN ('A Song of Ice and Fire', 'Game of Thrones')) 
AND b.published_at >= '2010-01-01 00:00:00' 
AND NOT b.reviewed_at BETWEEN '2016-01-01 00:00:00' AND '2016-01-31 23:59:59' 
ORDER BY YEAR(b.published_at) DESC, MONTH(b.published_at) DESC, b.name 
LIMIT 10;
```

Let your favorite IDE do the rest with autocompletion.

RulerZ
------
**Where** is mostly compatible with the awesome [RulerZ](https://github.com/K-Phoen/rulerz) DSL.

Be sure to `group` your statements to avoid downside effects.

Example:

```php
use function BenTools\Where\group;
use function BenTools\Where\not;

$fruits = [
    'banana',
    'apple',
];

$colors = [
    'yellow',
    'red',
];

$condition = group('fruit IN :fruits', ['fruits' => $fruits])->and(group('color IN :colors', ['colors' => $colors]));
$condition = not($condition);

var_dump($rulerz->satisfies(['fruit' => 'strawberry', 'color' => 'red'], (string) $condition, $condition->getValues())); // true
var_dump($rulerz->satisfies(['fruit' => 'apple', 'color' => 'yellow'], (string) $condition, $condition->getValues())); // false
```

Installation
----------------
> composer require bentools/where

Tests
-------
> ./vendor/bin/phpunit

See also
-----------

[bentools/simple-dbal](https://github.com/bpolaszek/simple-dbal) - A PHP 7.1+ wrapper for PDO & Mysqli. Can bind `DateTime` parameters.

[bentools/pager](https://github.com/bpolaszek/bentools-pager) - A PHP 7.1+ pager.

[bentools/flatten-iterator](https://github.com/bpolaszek/flatten-iterator) - Flattens multiple `array` or `Traversable` into one iterator.

[bentools/etl](https://github.com/bpolaszek/bentools-etl) - A PHP7.1 ETL pattern implementation.

[latitude/latitude](https://github.com/shadowhand/latitude) - Another SQL Query builder **Where** was inspired of.