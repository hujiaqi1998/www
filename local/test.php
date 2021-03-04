<?php

$array = [5,6,3,8,1,2,9,7,99,4,12,44,89,34,22,21,90,54];
var_dump(count($array));
function quick_sort($array)
{
    $j = $array[0];
    $count = count($array);
    $r = 0;
    $l = 0;

    for ($i=0; $i<$count; $i++){
        if ($j > $array[$i]){
            $left[] = $array[$i];
            $l++;
        }elseif ($j < $array[$i]){
            $right[] = $array[$i];
            $r++;
        }
    }

    if ($l > 1){
        $left = quick_sort($left);
    }

    if (isset($left)){
        $new_arr = $left;
    }
    $new_arr[] = $j;

    if ($r > 1){
        $right = quick_sort($right);
    }

    if (isset($right)){
        foreach ($right as $v){
            $new_arr[] = $v;
        }
    }

    return $new_arr;
}

function mp($array)
{
    $count = count($array);

    for ($i=0; $i<$count; $i++){
        for ($j=0; $j<$count-1-$i; $j++){
            if ($array[$j] < $array[$j+1]){
                $tmp = $array[$j];
                $array[$j] = $array[$j+1];
                $array[$j+1] = $tmp;
            }
        }
    }

    return $array;
}
echo '快速排序法';
var_dump(quick_sort($array));

echo '冒泡排序法';
var_dump(mp($array));

$arr = quick_sort($array);

// 二分查找算法
function half($arr, $target, $low, $high)
{
    if ($low <= $high){
        // 计算中间点
        $mid = intval(($low+$high)/2);
        if ($arr[$mid] == $target){
            return $arr[$mid];
        }elseif ($arr[$mid] > $target){
            $high = $mid - 1;
        }elseif ($arr[$mid] < $target){
            $low = $mid + 1;
        }

        return half($arr, $target, $low, $high);
    }else{
        return false;
    }
}
echo '二分查找';
var_dump(half($arr, 5, 0, count($arr)-1));


// 定义学生成绩二维数组
$array = [
    'hujq'=>['php'=>76, 'js'=>77, 'mysql'=>59],
    'wangjw'=>['php'=>66, 'js'=>87, 'mysql'=>49],
    'xiezz'=>['php'=>36, 'js'=>97, 'mysql'=>69],
    'liuyw'=>['php'=>27, 'js'=>47, 'mysql'=>89],
    'liangty'=>['php'=>36, 'js'=>43, 'mysql'=>29],
];

function array_sort($array, $k, $order_by='asc')
{
    if (!is_array($array)){
        return false;
    }

    // 定义数组  数组成员是一维键对应排序键的值
    $k_val = [];
    foreach ($array as $key=>$value){
        $k_val[$key] = $value[$k];
    }

    // 根据order_by 参数的值对数组进行排序
    switch ($order_by){
        case 'asc':
            asort($k_val);
            break;

        case 'desc':
            arsort($k_val);
            break;

        default:
            return false;
            break;
    }

    $new_arr = [];

    foreach ($k_val as $key=>$value){
        $new_arr[$key] = $array[$key];
    }

    return $new_arr;
}
echo '可复用的二维数组排序算法';
var_dump(array_sort($array, 'mysql', 'desc'));

// 对一个字符串进行小写化并分割为数组  并按首字母排序
$str='Apple Orange Banana Strawberry';
function str_test($str)
{
    // 先将所有大写字母变小写
    $str = strtolower($str);

    // 进行字符串分割
    $arr = explode(' ', $str);

    if (!is_array($arr)){
        return [];
    }

    // 进行排序
    asort($arr);

    return $arr;
}
var_dump(str_test($str));