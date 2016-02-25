#!/bin/bash
rm com.zone-latest
today=$(date +%d-%m-%y)
ln -s /home/renze/data/zone-files/com.zone."$today"_13:00 com.zone-latest
rm temp/* 2>&1 1>/dev/null
date
./find_outside > ../output/find_outside_com_statistics.txt
date
echo "Sorting..."
sort temp/outside_nodedup.txt > temp/outside_nodedup_sorted.txt
echo "Uniq..."
uniq temp/outside_nodedup_sorted.txt > ../output/outside_com.txt
echo "Counting..."
wc -l ../output/outside_com.txt > ../output/find_outside_com_amount_of_unique_hostnames.txt
echo "Cleaning..."
rm temp/outside_nodedup.txt
date
