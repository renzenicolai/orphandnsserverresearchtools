#!/bin/bash
rm temp/*
echo "Splitting..."
split -l 10000 -a 5 ../output/outside.txt temp/i_
cd temp
amount=0
for f in i_*; do
  echo "Starting finder for $f..."
  php ../script.php "$f" > "o_$f" &
  amount=$(jobs | wc -l)
  while [ "$amount" -gt 40 ]
  do
    sleep 5
    amount=$(jobs | wc -l)
  done
done
wait
echo "Combining results..."
cat o_* > ../../output/orphans.txt
echo "Counting..."
wc -l ../../output/orphans.txt > ../../output/orphans_amount.txt
