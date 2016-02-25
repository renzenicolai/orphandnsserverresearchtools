<?php
require_once('vendor/autoload.php');
use GeoIp2\Database\Reader;
$reader = new Reader("GeoLite2-Country.mmdb");

$all_ip4 = explode(PHP_EOL, file_get_contents('../output/ip4_list.txt'));
$all_ip6 = explode(PHP_EOL, file_get_contents('../output/ip6_list.txt'));

$ip4location_ip = array();
$ip4location_servers = array();

$ip4errors = 0;
$ip6errors = 0;

$ipwitherrors = array();

foreach ($all_ip4 as $ip) {
  $land = 'UNKNOWN';
  try {
    $record = $reader->country($ip);
    $land = $record->country->isoCode;
  }
  catch(Exception $e)
  {
    echo 'Error ['.$ip.']: ' .$e->getMessage().PHP_EOL;
    $land = 'Invalid / Not in DB';
    $ip4errors++;
    $ipwitherrors[] = $ip;
  }

  echo '['.$ip.'] '.$land.PHP_EOL;
  if (!array_key_exists($land, $ip4location_ip)) {
    $ip4location_ip[$land] = 0;
    $ip4location_servers[$land] = 0;
  }
  $ip4location_ip[$land]++;
  $ip4location_servers[$land] += shell_exec('grep "'.$ip.'" ../output/orphans.txt | wc -l');
}

$csv_land_ip4 = 'country, ip addresses, hostnames'.PHP_EOL;
foreach ($ip4location_ip as $key => $value) {
  $csv_land_ip4 .= $key.', '.$value.', '.$ip4location_servers[$key].PHP_EOL;
}

file_put_contents('../output/geolocation_ip4.csv', $csv_land_ip4 );

$ip6location_ip = array();
$ip6location_servers = array();

foreach ($all_ip6 as $ip) {
  $land = 'UNKNOWN';
  try {
    $record = $reader->country($ip);
    $land = $record->country->isoCode;
  }
  catch(Exception $e)
  {
    echo 'Error ['.$key.']: ' .$e->getMessage();
    $land = 'ERROR';
    $ip6errors++;
    $ipwitherrors[] = $ip;
  }
  echo '['.$ip.'] '.$land.PHP_EOL;
  if (!array_key_exists($land, $ip6location_ip)) {
    $ip6location_ip[$land] = 0;
    $ip6location_servers[$land] = 0;
  }
  $ip6location_ip[$land]++;
  $ip6location_servers[$land] += shell_exec('grep "'.$ip.'" ../output/orphans.txt | wc -l');
}

$csv_land_ip6 = 'country, ip addresses, hostnames'.PHP_EOL;
foreach ($ip6location_ip as $key => $value) {
  $csv_land_ip6 .= $key.', '.$value.', '.$ip6location_servers[$key].PHP_EOL;
}

file_put_contents('../output/geolocation_ip6.csv', $csv_land_ip6);

$stats = 'Totaal unieke ipv4 adressen: '.count($all_ip4).PHP_EOL;
$stats .= 'Totaal unieke ipv6 adressen: '.count($all_ip6).PHP_EOL;
$stats .= 'Fouten in IPv4: '.$ip4errors.PHP_EOL;
$stats .= 'Fouten in IPv6: '.$ip6errors.PHP_EOL;

echo $stats;
file_put_contents('../output/geolocation_status.txt', $stats);

file_put_contents('../output/geolocation_ipwitherrors.txt', implode ( PHP_EOL, $ipwitherrors ));
