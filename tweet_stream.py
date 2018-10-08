import tweepy
import csv
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
csvFile = open('sample_tweet.csv', 'a')
csvWriter=csv.writer(csvFile)
for tweet in tweepy.Cursor(api.search, q="#metoo", count=1, lang="en").items():
    print(tweet.created_at, tweet.text)
    csvWriter.writerow([tweet.created_at, tweet.text.encode('utf-8')])
