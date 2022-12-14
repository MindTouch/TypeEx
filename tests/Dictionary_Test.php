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

use modethirteen\TypeEx\Dictionary;
use modethirteen\TypeEx\Exception\InvalidDictionaryValueException;
use modethirteen\TypeEx\StringEx;
use PHPUnit\Framework\TestCase;

class Dictionary_Test extends TestCase  {

    public static function values_expected_Provider() : array {
        return [
            [['foo' => 'bar']],
            [['foo' => true, 'baz' => 'qux']],
            [['foo' => false, 'baz' => function() {}]],
            [['foo' => 'baz', 'qux' => [0, 1, 2, 3]]],
            [['foo' => new StringEx('foo'), 'qux' => new Dictionary()]],
            [['foo' => 123, 'qux' => new class() {}]],
            [['foo' => null]],
            [['foo' => '']]
        ];
    }

    /**
     * @dataProvider values_expected_Provider
     * @test
     * @param array<string, mixed> $values
     * @throws InvalidDictionaryValueException
     */
    public function Can_build_dictionary(array $values) : void {

        // act
        $keys = [];
        $result = new Dictionary();
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

    /**
     * @test
     * @throws InvalidDictionaryValueException
     */
    public function Can_set_valid_dictionary_value() : void {

        // arrange
        $dictionary = new Dictionary(fn($value): bool => $value === 'foo');

        // act
        $dictionary->set('bar', 'foo');

        // assert
        static::assertEquals('foo', $dictionary->get('bar'));
    }

    /**
     * @test
     * @throws InvalidDictionaryValueException
     */
    public function Cannot_set_invalid_dictionary_value() : void {

        // arrange
        static::expectException(InvalidDictionaryValueException::class);
        $dictionary = new Dictionary(fn($value): bool => is_int($value));

        // act
        $dictionary->set('bar', 'foo');
    }
}
