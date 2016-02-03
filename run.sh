#!/bin/bash
echo "Wiping unbound cache..."
sudo unbound-control reload

echo "Cleaning..."
rm output/*

echo "Finding records that point outside..."
cd find_outside_com
./run.sh &
cd ..
cd find_outside_net
./run.sh &
cd ..

wait

echo "Combining .net and .com ..."
cat output/outside_com.txt output/outside_net.txt > output/outside_nodedup.txt
sort output/outside_nodedup.txt > output/outside_nodedup_sorted.txt
uniq output/outside_nodedup_sorted.txt > output/outside.txt
wc -l output/outside.txt > output/outside_amount.txt
rm output/outside_nodedup.txt
rm output/outside_nodedup_sorted.txt

echo "Finding orphans in records that point outside..."
cd find_orphans_in_outside
./run.sh
cd ..

echo "Generating list of IP addresses..."
cd generate_iplists_from_orphans
./run.sh
cd ..

echo "Running a portscan on the IP addresses..."
cd scan_ports_of_ip_addresses
./run.sh &
cd ..

echo "Count amount of orphans per tld..."
cd count_in_which_tlds_orphans_are
./run.sh &
cd ..

cd find_domains_for_orphans
./run.sh &
cd ..

wait

echo "--- SCRIPT COMPLETED ---"
