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
namespace modethirteen\TypeEx;

use Closure;
use modethirteen\TypeEx\Exception\StringExCannotDecodeBase64StringException;

class StringEx implements \Stringable {

    /**
     * @var Closure|null
     */
    protected static ?Closure $serializer = null;

    public static function isNullOrEmpty(?string $string) : bool {
        return $string === null || $string === '';
    }

    /**
     * Use this anonymous function to serialize collections (objects and arrays)
     *
     * @param Closure $serializer - <$serializer($value) : string> : custom stringify for collections (objects and arrays)
     */
    public static function setDefaultSerializer(Closure $serializer) : void {
        static::$serializer = $serializer;
    }

    /**
     * Reset to default serialization of collections (objects and arrays)
     */
    public static function removeDefaultSerializer() : void {
        static::$serializer = null;
    }

    /**
     * Convert any value to string
     *
     * @param Closure|null $serializer - <$serializer($value) : string> : custom stringify for collections (objects and arrays)
     */
    public static function stringify(mixed $value, ?Closure $serializer = null): string {
        if($value === null) {
            return '';
        }
        if(is_string($value)) {
            return $value;
        }
        if(is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if(is_array($value)) {
            $func = $serializer ?? static::getDefaultSerializer();
            return $func($value);
        }
        if($value instanceof Closure) {
            return self::stringify($value());
        }
        if(is_object($value) && !method_exists($value, '__toString')) {
            $func = $serializer ?? static::getDefaultSerializer();
            return $func($value);
        }
        return strval($value);
    }

    /**
     * The default serializer converts arrays into comma separated lists, and objects into native-serialized objects
     */
    private static function getDefaultSerializer() : Closure {
        if(static::$serializer === null) {
            static::$serializer = function($value) : string {
                if(is_array($value)) {
                    return implode(',', array_map(fn($v): string => static::stringify($v), $value));
                }
                return serialize($value);
            };
        }
        return static::$serializer;
    }

    private static function endsWithHelper(string $haystack, string $needle) : bool {
        $length = strlen($needle);
        $start = $length * -1;
        return (substr($haystack, $start) === $needle);
    }

    private static function startsWithHelper(string $haystack, string $needle) : bool {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    final public function __construct(private readonly string $string)
    {
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return $this->toString();
    }

    /**
     * @note when comparing a string array, returns true if one or more values match
     * @param string|string[] $needle
     */
    public function contains(string|array $needle) : bool {
        if(is_array($needle)) {
            foreach($needle as $n) {
                $pos = strpos($this->string, self::stringify($n));
                if($pos !== false) {
                    return true;
                }
            }
            return false;
        }
        return str_contains($this->string, self::stringify($needle));
    }

    /**
     * @return static
     */
    public function ellipsis(int $max = 100) : object {
        $string = $this->string;
        if(strlen($string) <= $max) {
            return new static($string);
        }
        $result = substr($string, 0, $max);
        $string = (!str_contains($string, ' '))
            ? $result . '…'
            : preg_replace('/\w+$/', '', $result) . '…';
        return new static($string);
    }

    /**
     * @return static
     */
    public function encodeBase64() : object {
        return new static(base64_encode($this->string));
    }

    public function endsWith(string $needle) : bool {
        return self::endsWithHelper($this->string, $needle);
    }

     public function endsWithInvariantCase(string $needle) : bool {
        return self::endsWithHelper(strtolower($this->string), strtolower($needle));
    }

    /**
     * @param bool $strict - if true, will throw if string contains non-base64 encoding characters
     * @return static
     * @throws StringExCannotDecodeBase64StringException
     */
    public function decodeBase64(bool $strict = false) : object {
        $result = base64_decode($this->string, $strict);
        if($result === false) {
            throw new StringExCannotDecodeBase64StringException($this->string);
        }
        return new static($result);
    }

    /**
     * Case sensitive string comparison
     */
    public function equals(string $string) : bool {
        return $this->string === $string;
    }

    /**
     * Case insensitive string comparison
     */
    public function equalsInvariantCase(string $string) : bool {
        return strtolower($this->string) === strtolower($string);
    }

    /**
     * @return static
     */
    public function removePrefix(string $prefix) : object {
        $string = $this->string;
        return new static(
            (str_starts_with($string, $prefix))
                ? substr($string, strlen($prefix), strlen($string))
                : $string
        );
    }

    /**
     * Replace variables surrounded with {{ }}
     *
     * @param StringDictionaryInterface $replacements - collection of variables to their string values - ex: ['variable' => 'value']
     * @return static
     */
    public function interpolate(StringDictionaryInterface $replacements) : object {
        $string = $this->string;
        if(empty($replacements)) {
            return new static($string);
        }
        $variables = [];
        foreach($replacements as $variable => $value) {
            $variables['{{' . trim((string) $variable) . '}}'] = $value;
        }
        return new static(strtr($string, $variables));
    }

    /**
     * Replace variables surrounded with {{ }}
     *
     * @param IStringDictionary $replacements - collection of variables to their string values - ex: ['variable' => 'value']
     * @return static
     * @deprecated use \modethirteen\TypeEx\StringEx::interpolate
     * @noinspection PhpDeprecationInspection
     */
    public function template(IStringDictionary $replacements) : object {
        return $this->interpolate($replacements);
    }

    /**
     * @return static
     */
    public function trim() : object {
        return new static(trim($this->string));
    }

    public function toString() : string {
        return $this->string;
    }

    public function startsWith(string $needle) : bool {
        return self::startsWithHelper($this->string, $needle);
    }

    public function startsWithInvariantCase(string $needle) : bool {
        return self::startsWithHelper(strtolower($this->string), strtolower($needle));
    }
}
