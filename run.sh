#!/usr/bin/env bash
run_this="/usr/local/bin/scrape-twitter search --query 'geocode:$1,$2,10km lang:en $3' --type latest --count 1000 | jq '[.[] | {screenName, id, text, time}]'"
output=$(eval "$run_this")
echo "$output"