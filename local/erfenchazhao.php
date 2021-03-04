<?php
// 使用二分查找算法的数据集合的单元值必须是有序的
$array = [1,2,3,4,7,8,9,10,22,33,44,55,66,77,88,99,100,121,123,124];

// 定义初始最小键
$low = 0;
// 定义初始的最大键
$high = count($array) - 1;
// 定义目标
$target = 22;

// 递归方式实现二分查找算法
function half_dg($array, $target, $low, $high)
{
    // 比较最小键是否小于等于最大键
    if ($low <= $high){
        // 计算中间的键
        $mid = intval(($low + $high)/2);
        var_dump($array[$low].'---'.$array[$high]);
        if ($target == $array[$mid]){
            return $array[$mid];
        }elseif ($target > $array[$mid]){
            return half_dg($array, $target, $mid+1, $high);
        }elseif ($target < $array[$mid]){
            return half_dg($array, $target, $low, $mid-1);
        }
    }

    return false;
}

//var_dump(half_dg($array, $target, $low, $high));


// 循环方式实现二分查找算法
function half_xh($array, $target)
{
    // 定义初始最小键
    $low = 0;
    // 定义初始最大键
    $high = count($array) - 1;
    // 定义初始查询结果
    $find = false;

    while (true){
        if ($low <= $high){
            var_dump($array[$low].'---'.$array[$high]);
            // 计算中间点
            $mid = intval(($low+$high)/2);
            if ($array[$mid] == $target){
                $find = $array[$mid];
                break;
            }elseif ($array[$mid] > $target){
                $high = $mid - 1;
            }elseif ($array[$mid] < $target){
                $low = $mid + 1;
            }
        }else{
            break;
        }
    }

    return $find;
}

var_dump(half_xh($array, $target));


// 循环实现关联数组的二分查找算法
$array = ['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9];
// 定义目标
$target = 10;

function half_gl($array, $target)
{
    // 处理关联数组
    $keys = array_keys($array);

    // 定义最小键
    $low = 0;
    // 定义最大键
    $high = count($keys) - 1;
    // 初始化返回结果
    $find = false;

    while (true){
        if ($low <= $high){
            var_dump($array[$keys[$low]].'---'.$array[$keys[$high]]);
            // 计算中间点
            $mid = intval(($low + $high)/2);
            if ($target == $array[$keys[$mid]]){
                $find = $array[$keys[$mid]];
                break;
            }elseif ($target > $array[$keys[$mid]]){
                $low = $mid + 1;
            }elseif ($target < $array[$keys[$mid]]){
                $high = $mid - 1;
            }
        }else{
            break;
        }
    }

    return $find;
}

var_dump(half_gl($array, $target));