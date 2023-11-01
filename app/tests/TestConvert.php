<?php
/**
 * ZnetDK, Starter Web Application for rapid & easy development
 * See official website http://www.znetdk.fr
 * Copyright (C) 2023 Pascal MARTINEZ (contact@znetdk.fr)
 * License GNU GPL http://www.gnu.org/licenses/gpl-3.0.html GNU GPL
 * --------------------------------------------------------------------
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * --------------------------------------------------------------------
 * Test of the Convert ZnetDK core class
 *
 * File version: 1.0
 * Last update: 03/22/2023
 */

namespace app\tests;
use app\TestCase;
class TestConvert extends TestCase {

    static protected function toMoney() {
        $language = \UserSession::getLanguage();
        if ($language === 'fr' || $language === 'es') {
            $expectedValue = '98 612,37 €';
        } else { // $language === 'en'
            $expectedValue = '$98,612.37';
        }
        $convertedValue = \Convert::toMoney(98612.367);
        return self::areValuesEqual($convertedValue, $expectedValue,
                bin2hex($convertedValue), bin2hex($expectedValue));
    }

    static protected function toDecimalForDB() {
        $language = \UserSession::getLanguage();
        if ($language === 'fr' || $language === 'es') {
            $valueToConvert = '98 612,37';
        } else { // $language === 'en'
            $valueToConvert = '98,612.37';
        }
        /* BUG FIXED: grouping character ',' is wrongly changed in '.' */
        $convertedValue = \Convert::toDecimalForDB($valueToConvert);
        return $convertedValue === '98612.37';
    }

    static protected function toDecimal() {
        $language = \UserSession::getLanguage();
        if ($language === 'fr' || $language === 'es') {
            $valueToConvert = '98 612,37';
        } else { // $language === 'en'
            $valueToConvert = '98,612.37';
        }
        $convertedValue = \Convert::toDecimal($valueToConvert);
        return $convertedValue === 98612.37;
    }

    static protected function toUTF8() {
        $iso_8859_1_string = "\x5A\x6F\xEB";
        $convertedValue = \Convert::toUTF8($iso_8859_1_string);
        return $convertedValue === 'Zoë';
    }
    
    static protected function toISO88591() {
        $utf8_string = 'Zoë';
        $convertedValue = \Convert::toISO88591($utf8_string);
        return $convertedValue === "\x5A\x6F\xEB";
    }

    static protected function W3CtoLocaleDate() {
        $language = \UserSession::getLanguage();
        if ($language === 'fr' || $language === 'es') {
            $expectedValue = '31/12/2022';
        } else { // $language === 'en'
            $expectedValue = '12/31/22';
        }
        $convertedValue = \Convert::W3CtoLocaleDate('2022-12-31');
        return self::areValuesEqual($convertedValue, $expectedValue);
    }

    static protected function toLocaleDate() {
        $language = \UserSession::getLanguage();
        if ($language === 'fr' || $language === 'es') {
            $expectedValue = '31/12/2022';
        } else { // $language === 'en'
            $expectedValue = '12/31/22';
        }
        $curDate = new \DateTime('2022-12-31');
        $convertedValue = \Convert::toLocaleDate($curDate);
        return self::areValuesEqual($convertedValue, $expectedValue);
    }

    static protected function toW3CDate() {
        return \Convert::toW3CDate(new \DateTime('2021-02-27')) === '2021-02-27';
    }

    static protected function valuesToAnsi() {
        /* utf8_decode deprecated in PHP 8.2 */
        $utf8_string = "\x5A\x6F\xC3\xAB"; // 'Zoë'
        $utf8_euro = "\xE2\x82\xAC"; // '€'
        $utf8_french_accents = "éàèôùç";
        $simpleText = 'my text 2';
        $result = \Convert::valuesToAnsi([$utf8_string, $simpleText, $utf8_euro, $utf8_french_accents]);
        return $result[0] === hex2bin('5a6feb') 
                && $result[1] === 'my text 2'
                && $result[2] === chr(128)
                && $result[3] === hex2bin('E9E0E8F4F9E7');
    }

    static protected function base64UrlToBinary() {
        $binary = file_get_contents(ZNETDK_ROOT . 'engine/public/images/favicons/favicon-16x16.png');
        $base64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAACAVBMVEUAW/9+1f+05//C7P/C7P/C6//A6//B6/+U3f+a3/+C1/8AUv581f/M7//b8/971f+v5f+u5f+86v+76v+86/+76f+z5/+t5f+E1/+d4P/U8f510/8AIf571P+x5v++6v++6v+96v+87P+u5v540/4AHf+C1/9lzv9nz/9oz/+X3v+s5f+o4/+w5v+j4v+H2f++6v/w+v+S3P921P951f9hzf8Frv9u0P+a3v+T3P940/+E2P+b3//V8f/8/v/9/v////+c4P8StP/B6/+76f6k4v666v/O8P/3/P9Jxf9Bwv/0+///1Zv+v2b9xXf7xnn+2aX9+PD9///L7v8UtP//uVz/s07/7NH/37T/rTr/wm79/ft+1v8ctv/Z8///ulv/vF//0I//pCj99ens+f8xvf9by/////7/xXb/rT39+fKz5/8Psv+t5f//vmP/rDr/0I7/w2//s0n/4rphzP8tvP/p+P//z43/x3n/4Lb/3Kv/05f/9+zc9P8et/941P///vz/t1L/wm//9OT/v2f/0ZL//PcSs//G7f//tlL/sUb/t1P/2aT//v3z+/9GxP9FxP/1/P//sEL/tEv/z4z/+/bD7P8Qsv84v/951P910/+l4fz+tFD/wGz/8+L/vGD/wGr/9OX5/f9mzv9jzv+Y3v772KX93rP9+O/905f64bs5r9tXAAAAJnRSTlMBQ6K1tLS0tLWiQgFA394/mZiqqqqql5Y929s8AT2Zq6qqq5k9AdgRy6UAAAABYktHRED+2VzYAAAACXBIWXMAAA7CAAAOwgEVKEqAAAAAB3RJTUUH4wYSFhsyUB7s4AAAAP1JREFUGNNjYGBkYmZhYWFlY2Nn5+Dk4mbg4VVT19DU1NLW0dXTN+DjZxAwNDI2MTUzt7C0sraxtRNkELJ3cHBwdDJwdnHVc3N3EAYLeHh6efv4+vkHBAYJgQWCQ4wcQsPCIyKjoiECMbFxDvEJDg6JSckiIIGU1DQ7kEB6RmYWWEV2Tq6DQ15+QWFRMdgM+5LSMof08orKquoakIBDbV29g0NDY1NzS2ubPUhAq70DKNDZ1d3T6wAS6OufMBEkMGnylKkO9qIMYtOmz5hZP2v2nLnz5i9YuEicQUJSU3Px4iVLly0PWrFylZQ0g4ysnLyCgoKiiIiQkJKyiioAJzxKua4B91IAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTktMDYtMThUMjI6Mjc6NTArMDI6MDBkEFehAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDE5LTA2LTE4VDIyOjI3OjUwKzAyOjAwFU3vHQAAABh0RVh0U29mdHdhcmUAcGFpbnQubmV0IDQuMS42/U4J6AAAAFd6VFh0UmF3IHByb2ZpbGUgdHlwZSBpcHRjAAB4nOPyDAhxVigoyk/LzEnlUgADIwsuYwsTIxNLkxQDEyBEgDTDZAMjs1Qgy9jUyMTMxBzEB8uASKBKLgDqFxF08kI1lQAAAABJRU5ErkJggg==';
        return \Convert::base64UrlToBinary($base64) === $binary;
    }

    static protected function binaryToBase64Url() {
        $binary = file_get_contents(ZNETDK_ROOT . 'engine/public/images/favicons/favicon-16x16.png');
        $base64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAACAVBMVEUAW/9+1f+05//C7P/C7P/C6//A6//B6/+U3f+a3/+C1/8AUv581f/M7//b8/971f+v5f+u5f+86v+76v+86/+76f+z5/+t5f+E1/+d4P/U8f510/8AIf571P+x5v++6v++6v+96v+87P+u5v540/4AHf+C1/9lzv9nz/9oz/+X3v+s5f+o4/+w5v+j4v+H2f++6v/w+v+S3P921P951f9hzf8Frv9u0P+a3v+T3P940/+E2P+b3//V8f/8/v/9/v////+c4P8StP/B6/+76f6k4v666v/O8P/3/P9Jxf9Bwv/0+///1Zv+v2b9xXf7xnn+2aX9+PD9///L7v8UtP//uVz/s07/7NH/37T/rTr/wm79/ft+1v8ctv/Z8///ulv/vF//0I//pCj99ens+f8xvf9by/////7/xXb/rT39+fKz5/8Psv+t5f//vmP/rDr/0I7/w2//s0n/4rphzP8tvP/p+P//z43/x3n/4Lb/3Kv/05f/9+zc9P8et/941P///vz/t1L/wm//9OT/v2f/0ZL//PcSs//G7f//tlL/sUb/t1P/2aT//v3z+/9GxP9FxP/1/P//sEL/tEv/z4z/+/bD7P8Qsv84v/951P910/+l4fz+tFD/wGz/8+L/vGD/wGr/9OX5/f9mzv9jzv+Y3v772KX93rP9+O/905f64bs5r9tXAAAAJnRSTlMBQ6K1tLS0tLWiQgFA394/mZiqqqqql5Y929s8AT2Zq6qqq5k9AdgRy6UAAAABYktHRED+2VzYAAAACXBIWXMAAA7CAAAOwgEVKEqAAAAAB3RJTUUH4wYSFhsyUB7s4AAAAP1JREFUGNNjYGBkYmZhYWFlY2Nn5+Dk4mbg4VVT19DU1NLW0dXTN+DjZxAwNDI2MTUzt7C0sraxtRNkELJ3cHBwdDJwdnHVc3N3EAYLeHh6efv4+vkHBAYJgQWCQ4wcQsPCIyKjoiECMbFxDvEJDg6JSckiIIGU1DQ7kEB6RmYWWEV2Tq6DQ15+QWFRMdgM+5LSMof08orKquoakIBDbV29g0NDY1NzS2ubPUhAq70DKNDZ1d3T6wAS6OufMBEkMGnylKkO9qIMYtOmz5hZP2v2nLnz5i9YuEicQUJSU3Px4iVLly0PWrFylZQ0g4ysnLyCgoKiiIiQkJKyiioAJzxKua4B91IAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTktMDYtMThUMjI6Mjc6NTArMDI6MDBkEFehAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDE5LTA2LTE4VDIyOjI3OjUwKzAyOjAwFU3vHQAAABh0RVh0U29mdHdhcmUAcGFpbnQubmV0IDQuMS42/U4J6AAAAFd6VFh0UmF3IHByb2ZpbGUgdHlwZSBpcHRjAAB4nOPyDAhxVigoyk/LzEnlUgADIwsuYwsTIxNLkxQDEyBEgDTDZAMjs1Qgy9jUyMTMxBzEB8uASKBKLgDqFxF08kI1lQAAAABJRU5ErkJggg==';
        return \Convert::binaryToBase64Url($binary) === $base64;
    }

}
