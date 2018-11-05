# 4910-Senior-Project-Fall-2018

Helpful commands

scrape-twitter search --query "geocode:35.393528,-119.043732,10km lang:en" --type latest --count 1 | jq '[.[] | {screenName, id, text, time}]'


scrape-twitter search --query "lang:en" --type latest --count 1 | jq '[.[] | {screenName, id, text, time}]'


scrape-twitter search --query 'geocode:35.3732921,-119.0187149,10km lang:en lol' --type latest --count 5 | jq '[.[] | {screenName, id, text, time}]'
