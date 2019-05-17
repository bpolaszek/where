<?php

namespace BenTools\Where\Tests\Helper;

use BenTools\Where\Helper\Previewer;
use PHPUnit\Framework\TestCase;
use function BenTools\Where\where;

class PreviewerTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_preview_unnamed_statements()
    {
        $expression = where('string = ? and number = ? and boolean = ? and nullable = ?', ['foo', 0, true, null]);
        $this->assertEquals('string = \'foo\' and number = 0 and boolean = TRUE and nullable = NULL', Previewer::preview($expression, $expression->getValues()));
    }

    /**
     * @test
     */
    public function it_can_yells_if_unnamed_statement_has_not_the_correct_number_of_parameters()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Number of variables doesn\'t match number of parameters in statement');
        $expression = where('string = ? and number = ? and boolean = ? and nullable = ?', ['foo']);
        Previewer::preview($expression, $expression->getValues());
    }

    /**
     * @test
     */
    public function it_can_preview_named_statements()
    {
        $expression = where(
            'string = :string and number = :number and boolean = :boolean_var and nullable = :nullable and string = :string',
            [
                'string'      => 'foo',
                'number'      => 55.30,
                'boolean_var' => true,
                'nullable'    => null,
            ]
        );
        $this->assertEquals('string = \'foo\' and number = 55.3 and boolean = TRUE and nullable = NULL and string = \'foo\'', Previewer::preview($expression, $expression->getValues()));
    }

    /**
     * @test
     */
    public function it_can_yells_if_named_statement_has_not_the_correct_number_of_parameters()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Number of variables doesn\'t match number of parameters in statement');
        $expression = where(
            'string = :string and number = :number and boolean = :boolean_var and nullable = :nullable and string = :string',
            [
                'string' => 'foo',
            ]
        );
        Previewer::preview($expression, $expression->getValues());
    }

}
