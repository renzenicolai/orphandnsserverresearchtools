<?php
  $orphans = explode(PHP_EOL, file_get_contents('../output/orphans.txt'));
  foreach ($orphans as $orphan) {
    $records = '';
    $server = strToUpper(explode('|', $orphan)[0]);
    echo 'Server: '.$server.PHP_EOL;
    $records_net = shell_exec("cat net.zone-latest | grep '".$server."'");
    if (!empty($records_net)) {
      $r = explode(PHP_EOL, $records_net);
      foreach ($r as $record) {
        if (!empty($record)) {
          echo 'In .NET: '.$record.PHP_EOL;
          $records .= $record.PHP_EOL;
        }
      }
    }
    file_put_contents('temp_NET/'.$server, $records);
    $records = '';
    $records_com = shell_exec("cat com.zone-latest | grep '".$server."'");
    if (!empty($records_com)) {
      $r = explode(PHP_EOL, $records_com);
      foreach ($r as $record) {
        if (!empty($record)) {
          echo 'In .COM: '.$record.PHP_EOL;
          $records .= $record.PHP_EOL;
        }
      }
    }
    file_put_contents('temp_COM/'.$server, $records);
  }
