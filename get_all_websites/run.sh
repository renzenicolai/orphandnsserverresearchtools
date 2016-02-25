#!/bin/bash
rm temp/*
php script.php
mkdir ../output/sites
cp temp/* ../output/sites/ -Rv
