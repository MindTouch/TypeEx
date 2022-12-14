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
namespace modethirteen\TypeEx\Tests\BoolEx;

use modethirteen\TypeEx\BoolEx;
use PHPUnit\Framework\TestCase;

class boolify_Test extends TestCase {

    public static function value_expected_Provider() : array {
        return [
            [null, false],
            ['foo', false],
            [true, true],
            [false, false],
            [['foo', 'bar'], true],
            [['foo', 'plugh', 'bar' => ['baz']], true],
            [[], false],
            [['foo' => []], true],
            [fn() => 'foo', false],
            [fn() => true, true],
            [fn() => false, false],
            [fn() => null, false],
            [fn() => 123, true],
            [fn() => ['foo', 'bar'], true],
            [fn() => ['foo', 'plugh', 'bar' => ['baz']], true],
            [fn() => new class { function __toString() : string { return 'true'; }}, true],
            [fn() => new class { function __toString() : string { return 'false'; }}, false],
            [123, true],
            ['123', true],
            ['-5', false],
            ['0.1', true],
            ['0', false],
            ['0.0', false],
            [new class { function __toString() : string { return 'true'; }}, true],
            [new class { function __toString() : string { return 'false'; }}, false]
        ];
    }

    /**
     * @dataProvider value_expected_Provider
     * @test
     */
    public function Can_boolify(mixed $value, bool $expected) : void {

        // act
        $result = BoolEx::boolify($value);

        // assert
        static::assertEquals($expected, $result);
    }
}
