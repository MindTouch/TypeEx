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

class BoolEx {

    /**
     * Convert any value to bool
     */
    public static function boolify(mixed $value) : bool {
        if($value === null) {
            return false;
        }
        if(is_bool($value)) {
            return $value;
        }
        if(is_string($value)) {
            if(strtolower($value) === 'true') {
                return true;
            }
            return is_numeric($value) ? $value + 0 > 0 : false;
        }
        if(is_object($value) && method_exists($value, '__toString')) {
            return self::boolify(strval($value));
        }
        if($value instanceof Closure) {
            return self::boolify($value());
        }
        return boolval($value);
    }
}
