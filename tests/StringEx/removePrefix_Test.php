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

class removePrefix_Test extends TestCase {

    public static function string_prefix_expected_Provider() : array {
        return [
            ['foo', 'foo', ''],
            ['foo bar', 'foo', ' bar'],
            ['foobar', 'foo', 'bar'],
            ['foobar', 'f', 'oobar'],
            ['foo', '', 'foo']
        ];
    }

    /**
     * @dataProvider string_prefix_expected_Provider
     * @test
     */
    public function Can_remove_prefix(string $string, string $prefix, string $expected) : void {

        // arrange
        $string = new StringEx($string);

        // act
        $result = $string->removePrefix($prefix);

        // assert
        static::assertEquals($expected, $result);
    }
}
