import tweepy
import csv
import json
import pandas as pd

def getKeys():
    f = open("twitter_keys.txt", "r")
    if f.mode == 'r':
        codes = {}
        contents = f.read().split('\n')
        codes['consumer_key'] = contents[0]
        codes['consumer_secret'] = contents[1]
        codes['access_token'] = contents[2]
        codes['access_token_secret'] = contents[3]
    return codes

codes = getKeys()

auth = tweepy.OAuthHandler(codes["consumer_key"], codes["consumer_secret"])
auth.set_access_token(codes["access_token"], codes["access_token_secret"])

api = tweepy.API(auth)
#csvFile = open('sample_tweet.csv', 'a')
#csvWriter=csv.writer(csvFile)
#for tweet in tweepy.Cursor(api.search, q="#metoo", count=1, lang="en", tweet_mode='extended').items(1):
#    print(tweet.created_at, tweet.full_text)
    #csvWriter.writerow([tweet.created_at, tweet.text.encode('utf-8')])

tweets = api.search(q="#metoo", count=50, lang="en", tweet_mode="extended")
for tweet in tweets:
    print(tweet.created_at, end=' ')

    if tweet.coordinates is not None:
        print("Coordinates: ", tweet.coordinates['coordinates'][0], ',', tweet.coordinates['coordinates'][0], end = ' ')
    elif tweet.user.location is not '':
        print("Location:", tweet.user.location, end = ' ')
    if 'retweeted_status' in tweet._json:
        retweet_text = 'RT @ ' + api.get_user(tweet.retweeted_status.user.id_str).screen_name
        #if 'coordinates' in tweet._json:
        #    print(tweet.created_at, retweet_text, tweet._json['retweeted_status']['full_text'])
        #else:
        print(retweet_text, tweet._json['retweeted_status']['full_text'])
    else:
        #if 'coordinates' in tweet._json:
        #    print(tweet.created_at, tweet._json["coordinates"][0], tweet._json["coordinates"][1], tweet.full_text)
        #else:
        print(tweet.full_text)
