<?php

$bash = 'curl -o /dev/null -s -w %{time_namelookup}::%{time_connect}::%{time_starttransfer}::%{time_total}::%{speed_download}"\n" "http://10.16.111.34:7777/simulate/index.php?type=fib"';
exec($bash);
