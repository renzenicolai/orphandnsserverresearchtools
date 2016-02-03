#!/bin/bash
rm temp/* 2>&1 1>/dev/null
date
./find_outside > ../output/find_outside_net_statistics.txt
date
echo "Sorting..."
sort temp/outside_nodedup.txt > temp/outside_nodedup_sorted.txt
echo "Uniq..."
uniq temp/outside_nodedup_sorted.txt > ../output/outside_net.txt
echo "Counting..."
wc -l ../output/outside_net.txt > ../output/find_outside_net_amount_of_unique_hostnames.txt
echo "Cleaning..."
rm temp/outside_nodedup.txt
date
