<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\Before;
use PHPUnit\Framework\TestCase;

class BeforeTest extends TestCase
{
    public static function examplesProvider()
    {
        return [
            ['+2 day', 'today', true],
            ['2018-02-06T04:20:33', '2017-02-06T04:20:33', true],
            ['2017-02-06T04:20:33', '2018-02-06T04:20:33', false],
        ];
    }

    public static function invalidCasesProvider()
    {
        return [
            ['today', 'ipsum lorem', 'Value "ipsum lorem" is not a valid date.'],
            ['2017-02-06T04:20:33', 'ipsum lorem', 'Value "ipsum lorem" is not a valid date.'],
            ['today', 5, 'Before expander require "string", got "5".'],
        ];
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_examples($boundary, $value, $expectedResult) : void
    {
        $expander = new Before($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage) : void
    {
        $expander = new Before($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
