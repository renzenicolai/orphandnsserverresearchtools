#!/bin/bash
rm temp/*

echo "Generating IP lists..."
./iplist

echo "Sort..."
sort temp/ip4nodedup.txt > temp/ip4nodedup_sorted.txt
sort temp/ip6nodedup.txt > temp/ip6nodedup_sorted.txt

echo "Uniq..."
uniq temp/ip4nodedup_sorted.txt > ../output/ip4_list.txt
uniq temp/ip6nodedup_sorted.txt > ../output/ip6_list.txt 

echo "Cleaning..."
rm temp/*

echo "Counting..."
wc -l ../output/ip4_list.txt > ../output/ip4_amount.txt
wc -l ../output/ip6_list.txt > ../output/ip6_amount.txt

