<?php

class Wrk
{
    CONST PRESURE_TIME = 60;

    public function exec_presure($con, $url, $extra_path = "")
    {
        $base_path = $extra_path ? $extra_path : dirname($_SERVER['DOCUMENT_ROOT']);
        $out_path  = $base_path."/daemon/output.file";
        $lua_path  = $base_path."/daemon/get.lua";
        $wrk_path  = dirname($base_path)."/wrk/wrk";

        $thread = $con >= 5 ? 5 : $con - 1;
        $time   = self::PRESURE_TIME;

        if (file_exists($out_path)) {
            unlink($out_path);
        }
        $bash = "{$wrk_path} -t {$thread} -c {$con} -d {$time}s -s {$lua_path} {$url} 2>/dev/null | grep -E 'Socket errors|Latency|requests|Requests/sec|Transfer/sec|99.99%|99%' >> {$out_path}";
        exec($bash);
        return $this->getFinal($out_path);
    }

    //根据生成的文件，读取结果内容，获取对应信息
    public function getFinal($output)
    {
        $final = [];

        $file = fopen($output,"r") or exit("can not read file");

        //wrk读取配置
        //int表示读取该行空格间隔第几个参数，数组则表示读取后，截取的开始于结束位置
        $get_config = array(
            "time_arr" => array(
                "avg_resp" => array(1,0,-2),
                "max_resp" => array(3,0,-2) 
                ),
            "fetches" => 0,
            "error" => 0,
            "qps" => 1,
            "dps" => array(1,0,-2),
            "99_resp" => array(0,4),
            "99_99_resp" => array(0,7),
            );

        $tmp_save = null;
        //根据配置读取结果内容
        foreach ($get_config as $index => $term) {
            if (is_null($tmp_save)) {
                $line = trim(fgets($file));
                $line = preg_replace("/[\s]+/is"," ",$line);
                $arr = explode(" ",$line);
            } else {
                $arr = $tmp_save;
                $tmp_save = null;
            }

            if ($index == "error") {
                if ($arr[0] == "Socket") {
                    $final["error"][] = str_replace("," ,"" ,$arr[3]);
                    $final["error"][] = str_replace("," ,"" ,$arr[5]);
                    $final["error"][] = str_replace("," ,"" ,$arr[7]);
                    $final["error"][] = str_replace("," ,"" ,$arr[9]);
                    $final["error"] = implode(",", $final['error']);
                } else {
                    $final["error"] = "0,0,0,0";
                    $tmp_save = $arr;
                    continue;
                }
            } else { 
                if (strstr($index, "arr")) {
                    foreach ($term as $k => $v) {
                        $final[$k] = $this->_getValue($arr,$v);
                    }
                } else {
                    $final[$index] = $this->_getValue($arr,$term);
                }
            }
        }
        fclose($file);
        //unlink($output);
        return $final;
    }

    //辅助工具，获取对应值
    private function _getValue($arr,$term)
    {
        if (is_array($term)) {
            if (isset($term[2])) {
                $ret = substr($arr[$term[0]],$term[1],$term[2]);
                $unit = substr($arr[$term[0]],$term[2]);
                switch ($unit) {
                case "us" :
                    $ret = round($ret,0);
                    break;
                case "ms" :
                    $ret = round($ret*1000,0);
                    break;
                case "MB" :
                    $ret = round($ret*1000,0);
                    break;
                default :
                    break;
                }
            } else {
                $ret = substr($arr[$term[0]],$term[1]);
            }
        } else {
            $ret = $arr[$term];
        }
        return $ret;
    }
}
