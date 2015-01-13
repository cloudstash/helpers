<?php

namespace Cloudstash\Helper;

class Arr
{
    /**
     * Determine if two associative arrays are similar
     *
     * Both arrays must have the same indexes with identical values
     * without respect to key ordering
     *
     * @param array $a
     * @param array $b
     * @param bool $strict
     * @return bool
     */
    public static function similar(array $a, array $b, $strict = true)
    {
        if ($strict) {
            return ($a === $b);
        }

        return ($a == $b);
    }

    /**
     * @param array $source Original array, contained $key
     * @param string $key Key to return
     * @param mixed $default Default value, if $key does not exists
     * @return mixed
     */
    public static function get($source, $key, $default = null)
    {
        return isset($source[$key]) ? $source[$key] : $default;
    }

    /**
     * @param array $source Input array
     * @param array $group_keys Key queue for grouping
     * @param bool $put_in_single Put iteration element in array or single
     * @return array Grouping array
     *
     * @example
     *
     * Input array:
     * [
     *      [
     *          'id' => 1,
     *          'category_id' => 1,
     *          'tag' => 'music'
     *      ],
     *      [
     *          'id' => 2,
     *          'category_id' => 2,
     *          'tag' => 'films'
     *      ],
     *      [
     *          'id' => 3,
     *          'category_id' => 1,
     *          'tag' => 'books'
     *      ],
     *      [
     *          'id' => 4,
     *          'category_id' => 1,
     *          'tag' => 'books'
     *      ]
     * ]
     *
     * Group keys:
     * ['category_id', 'tag']
     *
     * Print_r of result:
     * Array
     * (
     *   [1] => Array
     *       (
     *           [music] => Array
     *               (
     *                   [0] => Array
     *                       (
     *                           [id] => 1
     *                           [category_id] => 1
     *                           [tag] => music
     *                       )
     *
     *               )
     *
     *           [books] => Array
     *               (
     *                   [0] => Array
     *                       (
     *                           [id] => 3
     *                           [category_id] => 1
     *                           [tag] => books
     *                       )
     *
     *                   [1] => Array
     *                       (
     *                           [id] => 4
     *                           [category_id] => 1
     *                           [tag] => books
     *                       )
     *
     *               )
     *
     *       )
     *
     *   [2] => Array
     *       (
     *           [films] => Array
     *               (
     *                   [0] => Array
     *                       (
     *                           [id] => 2
     *                           [category_id] => 2
     *                           [tag] => films
     *                       )
     *
     *               )
     *
     *       )
     *
     * )
     */
    public static function grouping(array $source, array $group_keys, $put_in_single = false)
    {
        $out = [];

        foreach ($source as $item) {
            $node =& $out;

            foreach ($group_keys as $g_key) {
                $g_value = self::get($item, $g_key);

                if (!$g_value) break;

                if (!isset($node[$g_value]))
                    $node[$g_value] = [];

                $node =& $node[$g_value];
            }

            if ($put_in_single)
                $node = $item;
            else
                $node[] = $item;
        }

        return $out;
    }

    /**
     * @param array $source Source array
     * @param string $path Dotted Separated Query (dsq)
     * @param mixed $default Default value will return if required node can not be find
     * @return mixed Value from array by DSQ
     *
     * @example
     *
     * Demo array:
     * [
     *      'test' => [
     *          'inner' => 'Hello'
     *      ]
     * ]
     *
     * Demo DSQ:
     * 'test.inner' will return 'Hello'
     */
    public static function path($source, $path, $default = null)
    {
        $result = $source;
        $depth = explode(".", $path);

        foreach ($depth as $node)
            $result = self::get($result, $node, $default);

        return $result;
    }

    /**
     * @param array $source
     * @param string $index
     * @param mixed $default
     * @return mixed
     */
    public static function getByIndex(array $source, $index = ':first', $default = null)
    {
        if (empty($source))
            return $default;

        $keys = array_keys($source);

        if (is_string($index)) {
            $index = mb_strtolower($index, Str::MBSTRING_CHARSET);

            if ($index == ':first') {
                $index = 0;
            } elseif ($index == ':last') {
                $index = (int) (count($keys) - 1);
            }
        }

        if (!isset($keys[$index])) {
            return $default;
        }

        return self::get($source, $keys[$index], $default);
    }

    /**
     * @param array $source Original array
     * @param mixed $default Default value if key with index does not exist
     * @return mixed Return first item from array
     */
    public static function getFirst(array $source, $default = null)
    {
        return self::getByIndex($source, ':first', $default);
    }

    /**
     * @param array $source Original array
     * @param mixed $default Default value if key with index does not exist
     * @return mixed Return last item from array
     */
    public static function getLast(array $source, $default = null)
    {
        return self::getByIndex($source, ':last', $default);
    }

    /**
     * @param array $data Source array
     * @param string $column Sorting target column
     * @param int $type Sorting type
     * @param int $order Soring direction
     * @return mixed Sorted array
     */
    public static function SortByColumn(array $data, $column, $type = SORT_STRING, $order = SORT_ASC)
    {
        $values = [];

        foreach ($data as $key => $row) {
            $values[] = $row[$column];
        }

        $values = array_map('strtolower', $values);

        array_multisort($values, $order, $type, $data);

        return $data;
    }

    /**
     * @param array $parent
     * @param array $child
     * @return array
     */
    public static function extend($parent, $child)
    {
        if (is_null($parent))
            $parent = [];

        if (is_null($child))
            $child = [];

        foreach ($child as $key => $field) {
            if (!isset($parent[$key])) {
                $parent[$key] = $field;
                continue;
            }

            if (is_array($parent[$key]) and is_array($child[$key])) {
                $parent[$key] = self::extend($parent[$key], $child[$key]);
                continue;
            }

            if (is_numeric($key)) {
                if (!in_array($field, $parent)) {
                    $parent[] = $field;
                    continue;
                }
            }

            $parent[$key] = $field;
        }

        return $parent;
    }

    /**
     * @param $array1
     * @param $array2
     * @return array
     */
    public static function DiffAssocRecursive($array1, $array2)
    {
        $difference = array();
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = self::DiffAssocRecursive($value, $array2[$key]);
                    if (!empty($new_diff))
                        $difference[$key] = $new_diff;
                }
            } else if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }

    /**
     * @param array $source
     * @param string $key_field
     * @param string $value_field
     * @return array
     */
    public static function toAssocOneToOne(array $source, $key_field, $value_field)
    {
        $result = [];

        foreach ($source as $key => $value) {
            $_key = self::get($value, $key_field);
            $_value = self::get($value, $value_field);

            if (is_null($_key)) {
                continue;
            }

            $result[$_key] = $_value;
        }

        return $result;
    }

    /**
     * @param array $source
     * @return array
     */
    public static function StripEmpty(array $source)
    {
        return array_filter($source, function ($item) {
            if (is_null($item))
                return false;

            $item = trim($item);

            if (!$item)
                return false;

            return true;
        });
    }

    /**
     * @param array $arr
     * @return bool
     *
     * @example
     * ['test', 'value'] - sequential
     * [0 => 'test', 1 => 'value'] - sequential
     * ['test' => 'test', 1 => 'value'] - assoc
     * [1 => 'test', 0 => 'value'] - assoc
     */
    public static function isAssoc(array $arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param array $arr
     * @return array
     *
     * @example
     * convert from
     * ['test', 'value']
     * to
     * ['test' => 'test', 'value' => 'value']
     */
    public static function SequentialToAssoc(array $arr)
    {
        if (self::isAssoc($arr)) {
            return $arr;
        }

        return array_combine($arr, $arr);
    }
}