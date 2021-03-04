<?php
// 快速排序示例

$array = [5,6,3,8,1,2,9,7,99,4,12,44,89,34,22,21,90,54];

function quick_sort($array)
{
    // 获取要排序的数组长度
    $count = count($array);

    // 定义基准值左右数组的初始长度
    $l = 0;
    $r = 0;

    // 定义基准值
    $j = $array[0];

    // 循环数组
    for ($i=1; $i<$count; $i++){
        if ($j > $array[$i]){
            // 没有基准值大则将该单元添加到left数组
            $left[] = $array[$i];
            // 并记录长度
            $l++;
        }else{
            // 比基准值大则将该单元添加到right数组
            $right[] = $array[$i];
            // 并记录长度
            $r++;
        }
    }

    if ($l > 1){
        // 长度大于1说明有至少两个值 可以递归
        $left = quick_sort($left);
    }

    if (isset($left)){
        $new_arr = $left;
    }
    $new_arr[] = $j;

    if ($r > 1){
        // 长度大于1说明有至少两个值 可以递归
        $right = quick_sort($right);
    }

    if (isset($right)){
        foreach ($right as $v){
            $new_arr[] = $v;
        }
    }

    return $new_arr;
}




var_dump(quick_sort($array));