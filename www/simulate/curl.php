<?php

$bash = 'curl -o /dev/null -s -w %{time_namelookup}::%{time_connect}::%{time_starttransfer}::%{time_total}::%{speed_download}"\n" "http://www/simulate/index.php?type=fib"';
exec($bash);
