<?php

$reg = '/[1-9]/';

$res = preg_match($reg, 'abcdef', $arr);

var_dump($res);
var_dump($arr);


$str = 'hello你好世界';

echo strlen($str);