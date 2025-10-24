<?php

//****** ФУНКЦИИ ДЛЯ РАБОТЫ С МАССИВАМИ

/**
 * сортировка по числу или букве
 * @param $array - многомерный массив
 * @param $key - ключ по которому будет идти сортировка
 * @param string $sort - направление сортировки
 * @return array - отсортированный многомерный массив
 */
function array_sort($array,$key,$sort = 'ASC') {
    usort($array, function($a,$b) use ($key){
        return strnatcasecmp($a[$key], $b[$key]);
    });
    if ($sort == 'DESC') return array_reverse($array);
    else return $array;
}

/**
 * перегруппировка массива
 * Transpose input arrays and save input keys.
 *
 * Example inputs:
 *
 * <code>
 * <input name="name[]" value="Alex"><br>
 * <input name="post[]" value="Actor"><br>
 * <input name="email[]" value="alex_actor@mail.dev"><br>
 * </code>
 *
 * input as
 *
 * <code>
 * [
 *  'name' => ['Alex', 'Born', 'Cindal'],
 *  'post' => ['Actor', 'Banker', 'Conductor'],
 *  'email' => ['alex_actor@mail.dev', 'born_banker@mail.dev', 'cindal_conductor.dev']
 * ];
 * </code>
 * output as
 * <code>
 * [
 *  0 => [
 *      'name'  => 'Alex',
 *      'post'  => 'Actor',
 *      'email' => 'alex_actor@mail.dev'
 *  ],
 *       1 => [
 *           'name'  => 'Born',
 *           'post'  => 'Banker',
 *           'email' => 'born_banker@mail.dev'
 *       ],
 *       2 => [
 *           'name'  => 'Cindal',
 *           'post'  => 'Conductor',
 *           'email' => 'cindal_conductor.dev'
 *       ],
 *   ];
 * </code>
 *
 * @param array $inputArray
 * @return array
 */
function array_transpose(array $inputArray){
    $outputArray = array();
    foreach ($inputArray as $dataKey=>$dataValues) {
        foreach ($dataValues as $k=>$v) {
            $outputArray[$k][$dataKey] = $v;
        }
    }
    return $outputArray;
}

/**
 * получение из двухуровневого массива нового массива значений по ключу второго уровня или массив ключ => значение
 * @param array $arr
 * @param $key
 * @param null $value
 * @return array
 */
function array_pluck($arr, $key, $value = null)
{
    $data = [];
    foreach ($arr as $k=>$v) {
        if ($value !== null) {
            $data[$key] = $v[$value];
        } else {
            $data[] = $v[$key];
        }
    }
    //
    return $data;
}

function array_set_key_by_field($arr, $field)
{
    $data = [];
    foreach ($arr as $k=>$v) {
        $data[$v[$field]] = $v;
    }
    //
    return $data;
}

// получить минимальное значение из массива
function array_find_min($arr = [])
{
    $arr = array_filter($arr, function($value) {
        return ! is_null($value);
    });
    $arr = array_reduce($arr, function($result, $value) {
        return is_null($result) || $value < $result ? $value : $result;
    });
    //
    return $arr;
}

// получить запись с минимальным значением в поле
function array_find_min_record($arr, $field)
{
    $min_value = array_find_min(array_pluck($arr, $field));
    $arr = array_filter($arr, function($record) use ($field, $min_value) {
        return $record[$field] == $min_value;
    });
    $arr = array_values($arr);
    //
    return (count($arr) >= 1) ? $arr[0] : null;
}
