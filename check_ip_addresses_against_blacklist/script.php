<?php
  echo "Starting blacklist check...".PHP_EOL;
  //if (!isset($argv[1])) die('usage: script.php <date>');

  //$xbl = explode(PHP_EOL, '/home/renze/data/blacklists/'.$argv[1].'-xbl');
  //$sbl = explode(PHP_EOL, '/home/renze/data/blacklists/'.$argv[1].'-sbl');

  $xbl = explode(PHP_EOL, file_get_contents('/home/renze/data/blacklists/xbl'));
  $sbl = explode(PHP_EOL, file_get_contents('/home/renze/data/blacklists/sbl'));

  //1. IP4 against XBL and SBL

  $ip4list = explode(PHP_EOL, file_get_contents('../output/ip4_list.txt'));
  echo "Amount of ip4 addresses: ".count($ip4list).PHP_EOL;
  $ip4onsbl = '';
  $ip4onxbl = '';

  echo "START4".PHP_EOL;
  foreach($ip4list as $ip4) { //Check all IPv4 addresses against XBL and SBL
    if (!empty($ip4)) {
    echo '.';
    if (in_array($ip4, $xbl)) {
      echo PHP_EOL.'Found IPv4 address "'.$ip4.'" on XBL.'.PHP_EOL;
      $ip4onxbl .= $ip4.PHP_EOL;
    }
    if (in_array($ip4, $sbl)) {
      echo PHP_EOL.'Found IPv4 address "'.$ip4.'" on SBL.'.PHP_EOL;
      $ip4onsbl .= $ip4.PHP_EOL;
    }
    }
  }

  file_put_contents('../output/ipv4_addresses_on_xbl.txt', $ip4onxbl);
  file_put_contents('../output/ipv4_addresses_on_sbl.txt', $ip4onsbl);

  unset($ip4list);
  unset($ip4onsbl);
  unset($ip4onxbl);

  //2. IP6 against XBL and SBL

  $ip6list = explode(PHP_EOL, file_get_contents('../output/ip6_list.txt'));

  $ip6onsbl = '';
  $ip6onxbl = '';

  echo "START6".PHP_EOL;
  foreach($ip6list as $ip6) { //Check all IPv6 addresses against XBL and SBL
    if (!empty($ip6)) {
    if (in_array($ip6, $xbl)) {
      echo 'Found IPv6 address "'.$ip6.'" on XBL.'.PHP_EOL;
      $ip6onxbl .= $ip6.PHP_EOL;
    }
    if (in_array($ip6, $sbl)) {
      echo 'Found IPv6 address "'.$ip6.'" on SBL.'.PHP_EOL;
      $ip6onsbl .= $ip6.PHP_EOL;
    }
    }
  }

  file_put_contents('../output/ipv6_addresses_on_xbl.txt', $ip6onxbl);
  file_put_contents('../output/ipv6_addresses_on_sbl.txt', $ip6onsbl);

  unset($ip6list);
  unset($ip6onsbl);
  unset($ip6onxbl);

  unset($xbl);
  unset($sbl);

  //3. check domains against dbl

  //$dbl = explode(PHP_EOL, '/home/renze/blacklists/'.$argv[1].'-dbl');
  //$orphans = explode(PHP_EOL, '../output/orphans.txt');
  //-
?>
