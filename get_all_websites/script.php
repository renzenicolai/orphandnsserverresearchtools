<?php
$input = explode(PHP_EOL, file_get_contents("../output/orphans.txt"));
foreach ($input as $record) {
  $s = explode('|', $record)[0];
  shell_exec('wget "'.$s.'" -T 1 -t 1 -O "temp/'.$s.'"');
}
