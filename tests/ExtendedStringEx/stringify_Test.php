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
namespace modethirteen\TypeEx\Tests\ExtendedStringEx;

use modethirteen\TypeEx\StringEx;
use PHPUnit\Framework\TestCase;

class stringify_Test extends TestCase {

    /**
     * @test
     */
    public function Extended_class_falls_back_to_default_serializer() : void {

        // arrange
        $extended = ExtendedStringEx::stringify(['foo', 'bar']);

        // assert
        static::assertEquals('foo,bar', $extended);
    }

    /**
     * @test
     */
    public function Can_set_default_serializer_without_inheritance() : void {

        // arrange
        ExtendedStringEx::setDefaultSerializer(fn() => 'xyzzy');

        // act
        $base = StringEx::stringify(['foo', 'bar']);
        $extended = ExtendedStringEx::stringify(['foo', 'bar']);

        // assert
        static::assertEquals('foo,bar', $base);
        static::assertEquals('xyzzy', $extended);
    }

    /**
     * @test
     */
    public function Can_set_different_serializers_between_a_base_class_and_extended_class() : void {

        // arrange
        StringEx::setDefaultSerializer(fn() => 'plugh');
        ExtendedStringEx::setDefaultSerializer(fn() => 'xyzzy');

        // act
        $base = StringEx::stringify(['foo', 'bar']);
        $extended = ExtendedStringEx::stringify(['foo', 'bar']);

        // assert
        static::assertEquals('plugh', $base);
        static::assertEquals('xyzzy', $extended);
    }

    protected function tearDown() : void {
        parent::tearDown();
        StringEx::removeDefaultSerializer();
        ExtendedStringEx::removeDefaultSerializer();
    }
}
