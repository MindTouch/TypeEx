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
use modethirteen\TypeEx\Exception\InvalidDictionaryValueException;

class Dictionary implements IDictionary {

    /**
     * @var array
     */
    private array $data = [];

    private string|bool|null $key = null;

    /**
     * @var string[] - list of keys in the map
     */
    private array $keys = [];

    /**
     * @var Closure|null
     */
    private ?Closure $validator;

    /**
     * @param Closure|null $validator - <$validator($value) : bool> - optionally check if value is allowed in dictionary
     */
    public function __construct(?Closure $validator = null) {
        $this->validator = $validator;
    }

    /**
     * @return mixed - IDictionary can return any value type
     */
    public function current(): mixed
    {
        return $this->data[$this->key];
    }

    public function get(string $key) {
        return $this->data[$key] ?? null;
    }

    public function getKeys() : array {
        return $this->keys;
    }

    /**
     * @return mixed - IDictionary can return any key type
     */
    public function key(): mixed
    {
        return $this->key;
    }

    public function next() : void {
        $this->key = next($this->keys);
    }

    public function rewind() : void {
        $this->key = reset($this->keys);
    }

    /**
     * {@inheritDoc}
     * @throws InvalidDictionaryValueException
     */
    public function set(string $key, $value) : void {
        if($value === null) {
            unset($this->data[$key]);
        } else {
            if($this->validator !== null) {
                $validator = $this->validator;
                if(!$validator($value)) {
                    throw new InvalidDictionaryValueException($value);
                }
            }
            $this->data[$key] = $value;
        }
        $this->keys = array_keys($this->data);
        $this->rewind();
    }

    public function toArray() : array {
        return $this->data;
    }

    public function valid() : bool {
        return $this->key !== false;
    }
}
