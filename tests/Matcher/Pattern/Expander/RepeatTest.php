<?php

declare(strict_types=1);

namespace AppVerk\PHPMatcher\Tests\Matcher\Pattern\Expander;

use AppVerk\PHPMatcher\Backtrace;
use AppVerk\PHPMatcher\Matcher\Pattern\Expander\Repeat;
use PHPUnit\Framework\TestCase;

class RepeatTest extends TestCase
{
    public static function examplesProvider()
    {
        $jsonPattern = '{"name": "@string@", "activated": "@boolean@"}';

        $jsonTest = [
            ['name' => 'toto', 'activated' => true],
            ['name' => 'titi', 'activated' => false],
            ['name' => 'tate', 'activated' => true],
        ];

        $scalarPattern = '@string@';
        $scalarTest = [
            'toto',
            'titi',
            'tata',
        ];

        $strictTest = [
            ['name' => 'toto', 'activated' => true, 'offset' => 'offset'],
        ];

        return [
            [$jsonPattern, $jsonTest, true],
            [$scalarPattern, $scalarTest, true],
            [$jsonPattern, $strictTest, true, false],
        ];
    }

    public static function invalidCasesProvider()
    {
        $pattern = '{"name": "@string@", "activated": "@boolean@"}';

        $valueTest = [
            ['name' => 1, 'activated' => 'yes'],
        ];

        $keyTest = [
            ['offset' => true, 'foe' => 'bar'],
        ];

        $strictTest = [
            ['name' => 1, 'activated' => 'yes', 'offset' => true],
        ];

        return [
            [$pattern, $valueTest, 'Repeat expander, entry n°0, key "name", find error : integer "1" is not a valid string.'],
            [$pattern, $keyTest, 'Repeat expander, entry n°0, require "array" to have key "name".'],
            [$pattern, $strictTest, 'Repeat expander expect to have 2 keys in array but get : 3'],
            [$pattern, '', 'Repeat expander require "array", got "".'],
        ];
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_matching_values($needle, $haystack, $expectedResult, $isStrict = true) : void
    {
        $expander = new Repeat($needle, $isStrict);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage) : void
    {
        $expander = new Repeat($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
