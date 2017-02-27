<?php

$params = $_GET;

// fib模式模拟cpu计算消耗
$incr = $params['incr'];
if (is_null($incr) || empty($incr) || !is_numeric($incr)) {
    $incr = 24;
}
$incr_max =  $incr + 2;
// 27-29，单次请求耗时平均在200-300ms
// 24-26，单次请求耗时平均在50ms
// 22-24, 单次请求耗时平均在30ms
// 20-22，单词请求耗时凭据在5-7ms内
 
$rand = rand($incr,$incr_max);
$result = fib2($rand);

// 根据延时参数是否存在，进行sleep控制，单位毫秒
$sleep = $params['sleep'];
if (!is_null($sleep) && !empty($sleep)) {
    usleep($sleep * 1000);
}

// 控制返回内容体积大小，单位是KB
$size = $params['size'];
$final = [];
$string = "abcdefghijklmnopqrstuvwxyz0123";
if (!is_null($size) && !empty($size)) {
    for ($i = 0; $i < $size * 32; $i++) {
        $final[] = $string;
    }
    $ret = json_encode($final);
} else {
    $ret = $string;
}

print_r($ret);

// 数组实现
function fibs1($n) {
    if ($n < 1)
        return - 1;
    $a [1] = $a [2] = 1;
    for($i = 3; $i <= $n; $i ++) {
        $a [$i] = $a [$i - 1] + $a [$i - 2];
    }
    return $a [$n];
}

// 递归实现
function fib2($n) {
    if($n<1)
        return -1;
    if ($n == 1 || $n == 2) {
        return 1;
    }
    return fib2($n-1) + fib2($n-2);
}



