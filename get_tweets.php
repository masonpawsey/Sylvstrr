<?php 

echo shell_exec('/usr/local/bin/scrape-twitter search --query "lang:en" --type latest --count 1 2>&1');
// echo shell_exec('which node');

?>