<?php
date_default_timezone_set("PRC");

$base_path = dirname(dirname(realpath(__FILE__)));
require($base_path."/library/autoload.php");

$db_host = "";
$db_name = "";
$db_user = "";
$db_pass = "";

$db_flag = true;
while($db_flag) {
    try {
        $db_obj = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
        $db_flag = false;
    } catch (Exception $e) {
        $db_flag = true;
    }
}

$STATUS_WAIT = 0;
$STATUS_EXEC = 1;
$STATUS_FIN  = 2;
$STATUS_ERR  = 3;

if (isset($argv) && !empty($argv[1])) {
    $pid = $argv[1];
    $wrk = new Wrk;
    $p_sql = $db_obj->query("select * from project where id = {$pid}");
    $p_obj = $p_sql->fetch();
    if (!$p_obj) {
        $sql = "insert into log (msg) values ('{$pid}不存在')";
        $result = $db_obj->exec($sql);
        exit;
    }


    for ($i = 0; $i < $p_obj['num']; $i++) {
        if ($i == 0) {
            //重新加载model，观察status是否修改
            $check_status = $p_obj['status'];
        } else {
            $check_obj = $p_sql->fetch(); 
            $check_status = $check_obj['status'];
        }

        if ($check_status === $STATUS_FIN) {
            $sql = "insert into log (msg) values ('{$pid}已暂停结束')";
            $result = $db_obj->exec($sql);
            exit;
        }
        if ($check_status != $STATUS_WAIT) {
            $sql = "insert into log (msg) values ('{$pid}的状态不是等待状态')";
            $result = $db_obj->exec($sql);
            exit;
        }

        $update_task = "update project set status = ".$STATUS_EXEC." where id = {$pid}";
        $db_obj->exec($update_task);

        $con = $p_obj['init'] + ($i + 1) * $p_obj['incr'];
        $result  = $wrk->exec_presure($con, $p_obj['url'], $base_path);

        if (empty($result)) {
            $sql = "insert into log (msg) values ('{$pid}压测结果为空')";
            $result = $db_obj->exec($sql);
            exit;
        }

        insert($result, $pid, $con, $wrk::PRESURE_TIME, $db_obj);
    } 

    $update_task = "update project set status = ".$STATUS_FIN." where id = {$pid}";
    $db_obj->exec($update_task);
}else {
    $sql = "insert into log (msg) values ('pid没传')";
    $result = $db_obj->exec($sql);
    exit;
}
$db_obj = null;

function insert($result, $pid, $con, $time, $db_obj)
{
    $sql = "INSERT INTO action (
        pid,
        time,
        con,
        avg_resp,
        max_resp,
        most_resp,
        `fetch`,
        qps,
        dps,
        error,
        ctime
    ) values (
        '".(int)$pid."',
        '".(int)$time."',
        '".(int)$con."',
        '".(int)$result['avg_resp']."',
        '".(int)$result['max_resp']."',
        '".(int)$result['99_resp']."',
        '".(int)$result['fetches']."',
        '".(int)$result['qps']."',
        '".(int)$result['dps']."',
        '".$result['error']."',
        '".date("Y-m-d H:i:s", time())."'
    )";
    $final = $db_obj->exec($sql);
    if (!$final) {
        $sql = "insert into log (msg) values ('{$pid}的action结果插入失败')";
        $result = $db_obj->exec($sql);
        exit;
    }
    return;
}



