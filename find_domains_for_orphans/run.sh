#!/bin/bash
rm com.zone-latest
today=$(cat ../output/tdz)
ln -s /home/renze/data/zone-files/com.zone."$today"_13:00 com.zone-latest
rm net.zone-latest
ln -s /home/renze/data/zone-files/net.zone."$today"_13:00 net.zone-latest
rm temp_COM/*
rm temp_NET/*
rm temp/*
php find_domains_1.php
cd temp_COM
cat * >> ALL
cd ..
cd temp_NET
cat * >> ALL
cd ..
./do_net
./do_com
cat temp/OUTPUT_COM* >> ../output/domains_com.txt
cat temp/OUTPUT_NET* >> ../output/domains_net.txt
