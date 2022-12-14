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

use modethirteen\TypeEx\StringDictionary;
use modethirteen\TypeEx\StringEx;
use PHPUnit\Framework\TestCase;

/**
 * @deprecated use \modethirteen\TypeEx\Tests\StringEx\interpolate_Test
 */
class template_Test extends TestCase {

    public static function template_replacements_expected_Provider() : array {
        return [
            ['{{foo}}', ['foo' => 'bar'], 'bar'],
            ['baz {{foo}} qux', ['foo' => 'bar'], 'baz bar qux'],
            ['baz {{foo}} {{qux}}', ['foo' => 'bar', 'qux' => 'plugh'], 'baz bar plugh'],
            ['baz foo {{qux}}', ['foo' => 'bar', 'qux' => 'plugh'], 'baz foo plugh'],
            ['{{baz}}foo{{qux}}', ['baz' => 'foo', 'qux' => 'xyzzy'], 'foofooxyzzy'],
            ['foo {{bar}}', [], 'foo {{bar}}'],
            ['qux {{baz}}', ['qux' => 'baz'], 'qux {{baz}}']
        ];
    }

    /**
     * @dataProvider template_replacements_expected_Provider
     * @test
     * @param array<string, string> $replacements
     */
    public function Can_interpolate(string $template, array $replacements, string $expected) : void {

        // arrange
        $string = new StringEx($template);
        $dictionary = new StringDictionary();
        foreach($replacements as $key => $value) {
            $dictionary->set($key, $value);
        }

        // act
        /** @noinspection PhpDeprecationInspection */
        $result = $string->template($dictionary);

        // assert
        /** @noinspection PhpDeprecationInspection */
        static::assertEquals($expected, $result);
    }
}
