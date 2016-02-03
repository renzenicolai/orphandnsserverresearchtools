<?php
$input = file_get_contents("../output/ip6_list.txt");
$input = explode(PHP_EOL, $input);
$csv_all = 'IP,DNS,HTTP,HTTPS,FTP,SMTP'.PHP_EOL;
$count_total = 0;
$count_dns = 0;
$count_http = 0;
$count_https = 0;
$count_ftp = 0;
$count_smtp = 0;
foreach ($input as $ip) {
  if (!empty($ip)) {
      $dns = 0;
      $http = 0;
      $https = 0;
      $ftp = 0;
      $smtp = 0;
      $psresult = str_replace(array("\n", "\r"), '', shell_exec("nmap -p 53,80,443,21,25 ".$ip." -6 -oG -"));
      $dns = (($psresult!=NULL)&&($psresult!="")&&((strpos($psresult,'53/open') !== false)));
      $http = (($psresult!=NULL)&&($psresult!="")&&((strpos($psresult,'80/open') !== false)));
      $https = (($psresult!=NULL)&&($psresult!="")&&((strpos($psresult,'443/open') !== false)));
      $ftp = (($psresult!=NULL)&&($psresult!="")&&((strpos($psresult,'21/open') !== false)));
      $smtp = (($psresult!=NULL)&&($psresult!="")&&((strpos($psresult,'25/open') !== false)));
      $count_total++;
      echo $ip.' ';
      if ($dns) {echo "DNS "; $count_dns++;} else { echo "    ";}
      if ($http) {echo "HTTP "; $count_http++;} else { echo "     ";}
      if ($https) {echo "HTTPS "; $count_https++;} else { echo "      ";}
      if ($ftp) {echo "FTP "; $count_ftp++;} else { echo "    ";}
      if ($smtp) {echo "SMTP "; $count_smtp++; } else { echo "     ";}
      echo PHP_EOL;
      $csv_all .= $ip.',';
      if ($dns) { $csv_all .= '1'; } else { $csv_all .= '0'; }
      $csv_all .= ',';
      if ($http) { $csv_all .= '1'; } else { $csv_all .= '0'; }
      $csv_all .= ',';
      if ($https) { $csv_all .= '1'; } else { $csv_all .= '0'; }
      $csv_all .= ',';
      if ($ftp) { $csv_all .= '1'; } else { $csv_all .= '0'; }
      $csv_all .= ',';
      if ($smtp) { $csv_all .= '1'; } else { $csv_all .= '0'; }
      $csv_all .= PHP_EOL;
    }
  }

$results = 'Total: '.$count_total.PHP_EOL;
$results .= 'Dns: '.$count_dns.PHP_EOL;
$results .= 'Http: '.$count_http.PHP_EOL;
$results .= 'Https: '.$count_https.PHP_EOL;
$results .= 'Ftp: '.$count_ftp.PHP_EOL;
$results .= 'Smtp: '.$count_smtp.PHP_EOL;

echo $results;

file_put_contents('../output/portscan_v6.csv', $csv_all);
file_put_contents('../output/portscan_v6_stats.txt', $results);
