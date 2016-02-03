<?php
//Start
//------------------------------------------------------------------------------------------------------
if (!(count($argv)==2)) die('[FATAL] Usage: '.$argv[0].' <input-file>'.PHP_EOL);
$records = file_get_contents($argv[1]);
if ($records === false) die('[FATAL] Could not read file.'.PHP_EOL);
$records = explode(PHP_EOL, $records);

//Functions
//------------------------------------------------------------------------------------------------------
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}
function checkExists($server, $ipv6 = false) {
  $type = 'A';
  if ($ipv6) $type = 'AAAA';
  $cmd = 'dig '.$type.' "'.$server.'" +short';
  $exitCode = 0;
  $output = '';
  exec($cmd, $output, $exitCode);
  if (empty($output)) return false;
  $i = 0;
  $error = false;
  $result = array();
  foreach ($output as $item) {
    if ($item) {
      if (startsWith($item,';')) {
        $error = true;
      } else {
        $result[$i] = $item;
        $i++;
      }
    }
  }
  if ($error) { //Dig returned an error
    //echo '[ERROR] Checking '.$type.' record for '.$server.' failed.'.PHP_EOL;
    //print_r($output);
    //print_r($result);
    return false;
  }
  if ($i==0) return false;
  return $result;
}
function checkNS($domain) {
  $cmd = 'dig NS "'.$domain.'" +short';
  $exitCode = 0;
  $output = '';
  exec($cmd, $output, $exitCode);
  if (empty($output)) return false;
  if ($output[0] == ';') { //Dig returned an error
    //echo '[ERROR] Checking NS record for '.$domain.' failed. (Dig output: '.$output.')'.PHP_EOL;
    return false;
  }
  //echo 'Found NS for '.$domain.PHP_EOL;
  //print_r($output);
  return $output;
}

//Script
//------------------------------------------------------------------------------------------------------
foreach ($records as $record) {
  if (!empty($record)) {
    $record2 = explode('|', $record);
    $server = $record2[0];
    $tld = $record2[1];
    $registerabledomain = $record2[2];

    $ip4 = checkExists($server, false); //Query for A record (possibly following a CNAME record to get to the A record)
    $ip6 = checkExists($server, true); ////Query for AAAA record (possibly following a CNAME record to get to the AAAA record)
    if ($ip4 || $ip6) {
      $nameservers = checkNS($registerabledomain);
      if (!$nameservers) {
        //The server is orphan
        $ip4addresses = '';
        if ($ip4) {
          foreach ($ip4 as $ip4address) {
            if (!filter_var($ip4address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
              //Valid IPv4 address
              $ip4addresses .= $ip4address.',';
            } else {
              //Invalid IPv4 address (CNAME?)
            }
          }
        }
        $ip4addresses = rtrim($ip4addresses, ',');
        $ip6addresses = '';
        if ($ip6) {
          foreach ($ip6 as $ip6address) {
            if (!filter_var($ip6address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
              //Valid IPv6 address
              $ip6addresses .= $ip6address.',';
            } else {
              //Invalid IPv6 address (CNAME?)
            }
          }
        }
        $ip6addresses = rtrim($ip6addresses, ',');
        echo $record.'|'.$ip4addresses.'|'.$ip6addresses.PHP_EOL;
      }
    } else {
      //echo $server.' does not exist.'.PHP_EOL;
    }
  }
}
