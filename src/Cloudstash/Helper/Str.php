<?php

namespace Cloudstash\Helper;

class Str
{
    /**
     * Кодировка для mbstring library
     */
    const MBSTRING_CHARSET = "UTF-8";

    /**
     * Константа с пустой строкой
     */
    const STRING_EMPTY = "";

    /**
     * @static
     * @param string $needle
     * @param string $string
     * @param string $charset
     * @return bool
     *
     * Проверяет на наличие вхождения $needle в строке $string
     */
    static public function Contains($needle, $string, $charset = self::MBSTRING_CHARSET)
    {
        return (mb_strpos($string, $needle, null, $charset) !== false);
    }

    /**
     * @static
     * @param string $needle
     * @param string $string
     * @param string $charset
     * @return bool
     *
     * Выполняет проверку, начинается ли строка $string с указанного вхождения $needle
     */
    static public function StartWith($needle, $string, $charset = self::MBSTRING_CHARSET)
    {
        return (mb_substr($string, 0, mb_strlen($needle, $charset), $charset) === $needle);
    }

    /**
     * @static
     * @param string $needle
     * @param string $string
     * @param string $charset
     * @return bool
     *
     * Выполняет проверку, заканчивается ли строка $string на указанное вхождение $needle
     */
    static public function EndWith($needle, $string, $charset = self::MBSTRING_CHARSET)
    {
        $needle_length = mb_strlen($needle, $charset);
        $total_length = mb_strlen($string, $charset);
        return (mb_substr($string, $total_length - $needle_length, $needle_length, $charset) === $needle);
    }

    /**
     * @static
     * @param string $needle
     * @param string $string
     * @param string $charset
     * @return string
     *
     * Возвращает строку, вырезая из ее начала $needle
     */
    static public function RemoveFirst($needle, $string, $charset = self::MBSTRING_CHARSET)
    {
        if(!static::StartWith($needle, $string, $charset)) {
            return $string;
        }

        $needle_length = mb_strlen($needle, $charset);
        $total_length = mb_strlen($string, $charset);
        return mb_substr($string, $needle_length, $total_length - $needle_length, $charset);
    }

    /**
     * @param string $needle
     * @param string $string
     * @param string $charset
     * @return string
     */
    static public function RemoveLast($needle, $string, $charset = self::MBSTRING_CHARSET)
    {
        if(!static::EndWith($needle, $string, $charset)) {
            return $string;
        }

        $needle_length = mb_strlen($needle, $charset);
        $total_length = mb_strlen($string, $charset);
        return mb_substr($string, 0, $total_length - $needle_length, $charset);
    }

    /**
     * @static
     * @param string $string
     * @param string $charset
     * @return string
     *
     * Переворачивает строку $string
     */
    static public function Reverse($string, $charset = self::MBSTRING_CHARSET)
    {
        $output_string = self::STRING_EMPTY;
        $string_length = mb_strlen($string, $charset);

        for($i = $string_length - 1; $i >= 0; --$i) {
            $output_string .= mb_substr($string, $i, 1, $charset);
        }

        return $output_string;
    }

    /**
     * @param $string
     * @param string $removeFirst
     * @param string $removeLast
     * @param string $charset
     * @return string
     *
     * Обрезает лишние пробелы по краям с поддержкой многобайтовой кодировки (UTF-8)
     */
    static public function Trim($string, $removeFirst = null, $removeLast = null, $charset = self::MBSTRING_CHARSET)
    {
        $string = preg_replace("/(^\\s+)|(\\s+$)/us", "", $string);

        if (is_string($removeFirst)) {
            $string = self::RemoveFirst($removeFirst, $string, $charset);
        }

        if (is_string($removeLast)) {
            $string = self::RemoveLast($removeLast, $string, $charset);
        }

        return $string;
    }

    /**
     * @static
     * @param string $string
     * @param string $charset
     * @return string
     *
     * Выполняет транслитераию строки с поддержкой многобайтовой кодировки
     */
    static public function Translate($string, $charset = self::MBSTRING_CHARSET)
    {
        static $rus, $lat;

        if ( is_null( $rus ) )
        {
            $rus = [
                'ё' => 'yo',    'ж' => 'zh',
                'ц' => 'tc',    'ч' => 'ch',
                'ш' => 'sh',    'щ' => 'sh',
                'ю' => 'yu',    'я' => 'ya',
                'Ё' => 'YO',    'Ж' => 'ZH',
                'Ц' => 'TC',    'Ч' => 'CH',
                'Ш' => 'SH',    'Щ' => 'SH',
                'Ю' => 'YU',    'Я' => 'YA'
            ];

            $lat = array_values($rus);
            $rus = array_keys($rus);

            $rusChars = "АБВГДЕЗИЙКЛМНОПРСТУФХЪЫЬЭабвгдезийклмнопрстуфхъыьэ";
            $latChars = "ABVGDEZIJKLMNOPRSTUFH_I_Eabvgdezijklmnoprstufh_i_e";
            $strLen = mb_strlen( $rusChars, $charset );
            for( $i = 0 ; $i < $strLen; $i++ )
            {
                $rus[] = mb_substr( $rusChars, $i, 1, $charset );
                $lat[] = mb_substr( $latChars, $i, 1, $charset );
            }
        }

        return str_replace( $rus, $lat, $string );
    }

    /**
     * @static
     * @param string $input
     * @param int $substring_length
     * @param bool $to_lower
     * @param string $charset
     * @return string
     *
     * Выполняет преобразования строки к корректному виду в URL
     */
    static public function Prepare2URL($input, $substring_length = 40, $to_lower = true, $charset = self::MBSTRING_CHARSET)
    {
        $patterns = [
            ' ' => '-',     '.' => '',
            ',' => '',      '/' => '_',
            '\\' => '_',    '!' => '',
            '@' => '',      '&' => '',
            ':' => '',      ';' => '',
            '%' => '',      '?' => '',
            '<' => '',      '>' => '',
            '$' => '',      '#' => '',
            '^' => '',      '*' => '',
            '(' => '',      ')' => '',
            '`' => '',      '\'' => '',
            '"' => ''
        ];

        $result = mb_substr( self::StrReplace(self::Trim($input), $patterns), 0, $substring_length, $charset );

        if ($to_lower === true) {
            return mb_strtolower($result, $charset);
        }

        return $result;
    }

    /**
     * @static
     * @param string $string
     * @param array $matcher
     * @return string
     *
     * Делает в строке пакет замен из массива
     */
    static public function StrReplace($string, array $matcher)
    {
        return str_replace(array_keys($matcher), array_values($matcher), $string);
    }

    /**
     * @return string
     */
    static public function p()
    {
        $args = func_get_args();
        $num = $args[0];
        unset($args[0]);

        return self::Pluralize($num, $args);
    }

    /**
     * @param int $n
     * @param array $forms
     * @return string
     */
    static public function Pluralize($n, array $forms)
    {
        return $n%10==1&&$n%100!=11?$forms[0]:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$forms[1]:$forms[2]);
    }

    /**
     * @param string $text
     * @return mixed
     */
    static public function MakeInlineSimpleText($text)
    {
        $buffer = strip_tags($text);
        $buffer = preg_replace("/\\n/", " ", $buffer);
        return preg_replace("/\\s{2,}/", " ", $buffer);
    }

    /**
     * @param string $string
     * @param int $maxLength
     * @param string $end
     * @return string
     */
    static public function SubstringFullWord($string, $maxLength = 250, $end = "&hellip;", $charset = self::MBSTRING_CHARSET)
    {
        $line = $string;

        if (mb_strlen($string, self::MBSTRING_CHARSET) > $maxLength) {
            $line = mb_substr($string, 0, mb_strpos($string, ' ', $maxLength, $charset), $charset) . $end;
        }

        return $line;
    }

    /**
     * @param string $delimiter
     * @param string|callback $string
     * @param callback $postprocessor
     * @return array
     */
    public static function explode($delimiter, $string, $postprocessor = null)
    {
        if (is_callable($string)) {
            $string = (string) call_user_func_array($string, [$delimiter]);
        }

        if (!$string or $string == $delimiter) {
            return [];
        }

        $result_array = explode($delimiter, $string);

        if (is_callable($postprocessor)) {
            return array_map($postprocessor, $result_array);
        }

        return $result_array;
    }
}