#!/bin/bash
td=$(date +%F)
echo "$td" > output/td
cd /home/renze/data/zone-files
tdz=$(date +%d-%m-%y)
mv com.zone.gz."$tdz"_13:00 com.zone."$tdz"_13:00.gz
mv net.zone.gz."$tdz"_13:00 net.zone."$tdz"_13:00.gz
gunzip com.zone."$tdz"_13:00.gz
gunzip net.zone."$tdz"_13:00.gz
cd /home/renze/orphanfinder

echo "Wiping unbound cache..."
sudo unbound-control reload

echo "Cleaning..."
rm output/* -R

echo "$tdz" > output/tdz

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

#cd find_domains_for_orphans
#./run.sh &
#cd ..

cd check_ip_addresses_against_blacklist
./run.sh &
cd ..

cd get_all_websites
./run.sh &
cd ..

cd geolocation
./run.sh &
cd ..

wait

echo "Saving results..."
mkdir /home/renze/results/"$td"
cp -v output/* /home/renze/results/"$td" -R

echo "--- SCRIPT COMPLETED ---"
