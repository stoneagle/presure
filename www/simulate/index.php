<?php

$params = $_GET;

switch ($params['type']) {
    case "normal" :
        // 常见延时
        $rand = rand(200,400);
        usleep(1000 * $rand);
        var_dump("end");
        break;
    case "cpu" :
        // 高CPU计算
        $sum = 0;
        for ($i = 0; $i < 1000000; $i++) {
            $sum += $i;
        }
        var_dump($sum);
        break;
    case "big" :
        // 大文件返回，初步规模是1M
        $string = "abcdefghijklmnopqrstuvwxyz";
        $result = "";
        for ($i = 0; $i < 100; $i++) {
            $result .= $string;
        }
        header('Content-type:text/json');
        for ($i = 0; $i < 400; $i++) {
            $final[] = $result;
        }
        $final = json_encode($final);
        print_r($final);
        break;
    case "fib" :
        $incr = $params['incr'];
        if (is_null($incr) || empty($incr) || !is_numeric($incr)) {
            $incr = 24;
        }
        $incr_max =  $incr + 2;
        // 27-29，单次请求耗时平均在200-300ms
        // 24-26，单次请求耗时平均在50ms
        // 20-22，单词请求耗时凭据在5-7ms内
         
        $rand = rand($incr,$incr_max);
        $result = fib2($rand);
        print_r($result);
        break;
}

function fibs1($n) {
    if ($n < 1)
        return - 1;
    $a [1] = $a [2] = 1;
    for($i = 3; $i <= $n; $i ++) {
        $a [$i] = $a [$i - 1] + $a [$i - 2];
    }
    return $a [$n];
}

function fib2($n) {
    if($n<1)
        return -1;
    if ($n == 1 || $n == 2) {
        return 1;
    }
    return fib2($n-1) + fib2($n-2);
}



