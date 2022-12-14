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

class ellipsis_Test extends TestCase {

    public static function string_length_expected_Provider() : array {
        return [
            ['Pat ordered a ghost pepper pie', 200, 'Pat ordered a ghost pepper pie'],
            ['I really want to go to work, but I am too sick to drive', 10, 'I really …'],
            ['She moved forward only because she trusted that the ending she now was going through must be followed by a new beginning', null, 'She moved forward only because she trusted that the ending she now was going through must be …'],
            ['It took him a while to realize that everything he decided not to change, he was actually choosing.', 25, 'It took him a while to …'],
            ['He waited for the stop sign to turn to a go sign', 300, 'He waited for the stop sign to turn to a go sign'],
            ['They\'re playing the piano while flying in the plane', 5, 'They\'…'],
            ['In the end, he realized he could see sound and hear words', 57, 'In the end, he realized he could see sound and hear words'],
            ['He created a pig burger out of beef', 34, 'He created a pig burger out of …'],
            ['abcdefghijklmnopqrstuvwxyz', 20, 'abcdefghijklmnopqrst…'],
            ['frank baz jesse plugh xyzzy', 20, 'frank baz jesse …'],
            ['frank baz jesse plugh xyzzy', 25, 'frank baz jesse plugh …']
        ];
    }

    /**
     * @dataProvider string_length_expected_Provider
     * @test
     */
    public function Can_shorten(string $string, ?int $length, string $expected) : void {

        // arrange
        $string = new StringEx($string);

        // act
        $result = $length !== null ? $string->ellipsis($length) : $string->ellipsis();

        // assert
        static::assertEquals($expected, $result);
    }
}
