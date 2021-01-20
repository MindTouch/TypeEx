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

class Bar {
    public $foo = ['baz', 'qux'];
}

class stringify_Test extends TestCase {

    /**
     * @return array
     */
    public static function value_expected_Provider() : array {
        return [
            [null, ''],
            ['foo', 'foo'],
            [true, 'true'],
            [false, 'false'],
            [['foo', 'bar'], 'foo,bar'],
            [['foo', 'plugh', 'bar' => ['baz']], 'foo,plugh,baz'],
            [function() { return 'foo'; }, 'foo'],
            [function() { return 123; }, '123'],
            [function() { return ['foo', 'bar']; }, 'foo,bar'],
            [function() { return ['foo', 'plugh', 'bar' => ['baz']]; }, 'foo,plugh,baz'],
            [function() { return new class { function __toString() : string { return 'xyzzy'; }}; }, 'xyzzy'],
            [123, '123'],
            [new class { function __toString() : string { return 'qux'; }}, 'qux'],
            [(object)['foo' => 'bar', 'baz' => 'qux'], 'O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"baz";s:3:"qux";}'],
            [new Bar(), 'O:38:"modethirteen\TypeEx\Tests\StringEx\Bar":1:{s:3:"foo";a:2:{i:0;s:3:"baz";i:1;s:3:"qux";}}']
        ];
    }

    /**
     * @dataProvider value_expected_Provider
     * @test
     * @param mixed $value
     * @param string $expected
     */
    public function Can_stringify($value, string $expected) : void {

        // act
        $result = StringEx::stringify($value);

        // assert
        static::assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function Can_stringify_object_with_serializer() : void {

        // act
        $object = StringEx::stringify(new class {
            public $foo = ['xyzzy', 'plugh'];
        }, function($value) : string {
            if(is_object($value)) {
                return StringEx::stringify($value->foo);
            }
            return is_array($value) ? serialize($value) : '';
        });

        // assert
        static::assertEquals('xyzzy,plugh', $object);
    }

    /**
     * @test
     */
    public function Can_stringify_array_with_serializer() : void {

        // act
        $result = StringEx::stringify(['foo', 'bar', 'baz' => ['a', 'b', 'c']], function($value) : string {
            $xml = '';
            foreach($value as $item) {
                $item = StringEx::stringify($item, function($value) : string {
                    return implode(',', array_map(function($v) : string {
                        return StringEx::stringify($v);
                    }, $value));
                });
                $xml .= "<value>{$item}</value>";
            }
            return "<values>{$xml}</values>";
        });

        // assert
        static::assertEquals('<values><value>foo</value><value>bar</value><value>a,b,c</value></values>', $result);
    }

    /**
     * @test
     */
    public function Can_stringify_object_with_default_serializer() : void {

        // arrange
        StringEx::setDefaultSerializer(function($value) : string {
            if(is_object($value)) {
                return StringEx::stringify($value->foo);
            }
            return is_array($value) ? serialize($value) : '';
        });

        // act
        $object = StringEx::stringify(new class {
            public $foo = ['xyzzy', 'plugh'];
        });

        // assert
        static::assertEquals('a:2:{i:0;s:5:"xyzzy";i:1;s:5:"plugh";}', $object);
    }

    /**
     * @test
     */
    public function Can_stringify_array_with_default_serializer() : void {

        // arrange
        StringEx::setDefaultSerializer(function($value) : string {
            $xml = '';
            foreach($value as $item) {
                $item = StringEx::stringify($item);
                $xml .= "<value>{$item}</value>";
            }
            return "<values>{$xml}</values>";
        });

        // act
        $result = StringEx::stringify(['foo', 'bar', 'baz' => ['a', 'b', 'c']]);

        // assert
        static::assertEquals('<values><value>foo</value><value>bar</value><value><values><value>a</value><value>b</value><value>c</value></values></value></values>', $result);
    }

    protected function tearDown() : void {
        parent::tearDown();
        StringEx::removeDefaultSerializer();
    }
}
