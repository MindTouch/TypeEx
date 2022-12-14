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
namespace modethirteen\TypeEx\Tests;

use modethirteen\TypeEx\StringDictionary;
use modethirteen\TypeEx\StringEx;
use PHPUnit\Framework\TestCase;

class StringDictionary_Test extends TestCase  {

    public static function values_Provider() : array {
        return [
            [['foo' => 'bar']],
            [['foo' => StringEx::stringify(true), 'baz' => 'qux']],
            [['foo' => StringEx::stringify(false), 'baz' => StringEx::stringify(function() {})]],
            [['foo' => 'baz', 'qux' => StringEx::stringify([0, 1, 2, 3])]],
            [['foo' => (new StringEx('foo'))->toString(), 'qux' => 'foo,bar,baz']],
            [['foo' => StringEx::stringify(123), 'qux' => StringEx::stringify(new class() {
                public function __toString() : string {
                    return 'asdf';
                }
            })]],
            [['foo' => null]],
            [['foo' => '']]
        ];
    }

    /**
     * @dataProvider values_Provider
     * @test
     * @param array<string, string> $values
     */
    public function Can_build_dictionary(array $values) : void {

        // act
        $keys = [];
        $result = new StringDictionary();
        foreach($values as $key => $value) {
            if($value !== null) {
                $keys[] = $key;
            }
            $result->set($key, $value);
        }

        // assert
        foreach($values as $key => $value) {
            self::assertEquals($value, $result->get($key));
        }
        foreach($result as $key => $value) {
            self::assertEquals($value, $values[$key]);
        }
        static::assertEquals($keys, $result->getKeys());
        static::assertEquals(array_filter($values, fn($value) => $value !== null), $result->toArray());
    }
}