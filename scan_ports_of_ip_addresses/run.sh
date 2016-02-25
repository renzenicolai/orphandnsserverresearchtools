#!/bin/bash
php pscan6.php &
rm temp/*
echo "Splitting..."
split -l 1000 -a 5 ../output/ip4_list.txt temp/i_
cd temp
amount=0
for f in i_*; do
  echo "Starting finder for $f..."
  php ../pscan4.php "$f" "o_$f" &
  amount=$(jobs | wc -l)
  while [ "$amount" -gt 8 ]
  do
    sleep 5
    amount=$(jobs | wc -l)
  done
done
wait
echo "Combining results..."
echo "IP,DNS,HTTP,HTTPS,FTP,SMTP" > ../../output/portscan_v4.csv
cat o_* >> ../../output/portscan_v4.csv
cd ..
rm temp/*
