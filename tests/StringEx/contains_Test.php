<?php declare(strict_types=1);
/**
 * TypeEx
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace modethirteen\TypeEx\Tests\StringEx;

use modethirteen\TypeEx\StringEx;
use PHPUnit\Framework\TestCase;

class contains_Test extends TestCase  {

    public static function string_value_expected_Provider() : array {
        return [
            ['foo', 'foo', true],
            ['foo', 'f', true],
            ['foo', 'o', true],
            ['foo', 'oo', true],
            ['foo bar', 'bar', true],
            ['foo bar', ' ', true],
            ['foo bar', 'foo', true],
            ['foo', 'bar', false],
            ['foo', 'boo', false],
            ['foo', ['foo', 'f', 'o', 'oo'], true],
            ['foo bar', ['foo', 'bar'], true],
            ['foo bar', ['foo', ' '], true],
            ['foo bar', ['foo'], true],
            ['foo', ['foo', 'bar'], true],
            ['foo', ['boo'], false]
        ];
    }

    /**
     * @dataProvider string_value_expected_Provider
     * @test
     */
    public function String_contains(string $string, mixed $value, bool $expected) : void {

        // act
        $result = (new StringEx($string))->contains($value);

        // assert
        static::assertEquals($expected, $result);
    }
}