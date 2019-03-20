from appJar import gui
import tweepy
import json
import re
import numpy
import geocoder

def getKeys():
    f = open("twitter_keys.txt", "r")
    if f.mode == 'r':
        keys = {}
        contents = f.read().split('\n')
        keys['consumer_key'] = contents[0]
        keys['consumer_secret'] = contents[1]
        keys['access_token'] = contents[2]
        keys['access_token_secret'] = contents[3]
    return keys

def press(button):
    if button == "Cancel":
        app.stop()
    if button == "Generate":
        subject = app.getEntry("Subject:")
        api = connect()
        search_hashtag(api, subject)

def connect():
    keys = getKeys()

    auth = tweepy.OAuthHandler(keys["consumer_key"], keys["consumer_secret"])
    auth.set_access_token(keys["access_token"], keys["access_token_secret"])

    api = tweepy.API(auth)

    return api

def search_hashtag(api, subject):
    #api = connect()
    print(subject)
    search = subject + " -filter:media"
    total_tweets = 0
    geo_tweets = 0
    t_loc_tweets = 0
    user_loc_tweets = 0
    tweet_d = []
    ###Search subject
    tweets = api.search(q=search, count=10, lang="en", place_country='US', tweet_mode="extended")
    for tweet in tweets:
        #print('here!')
        total_tweets += 1
        id = tweet.id
        time = tweet.created_at
        text = tweet.full_text
        #print(id, time, text)
        if tweet.coordinates:
            location = str(tweet.coordinates['coordinates'][1]) + ', ' + str(tweet.coordinates['coordinates'][0])
            geo_tweets += 1
        elif tweet.place:
            print("************GOT ONE!****************")
            #[[[-87.634643, 24.396308], [-79.974307, 24.396308], [-79.974307, 31.001056], [-87.634643, 31.001056]]]
            box = tweet.place.bounding_box.coordinates
            lat = [box[0][0][1], box[0][2][1]]
            long = [box[0][0][0], box[0][1][0]]
            location = str(numpy.average(lat) + ", " + numpy.average(long))
            #location = tweet.place.full_name + ", " + tweet.place.country
            print(box)
            t_loc_tweets += 1
        elif tweet.user.location:
            g = geocoder.mapquest(tweet.user.location, key='qAFso4GZALGS7K304pgKQulVb7FWbaiV') #CHANGE THIS YOU ASSHOLE
            location = str(g.lat) + ", " + str(g.lng)
            user_loc_tweets += 1
        else:
            location = ''
        tweet_d.append({'id': id,
                        'time':time,
                        'text': text,
                        'location': location})
    print("Total tweets: %d; Geolocated: %d; Tweet Location: %d, User Location: %d" % (total_tweets, geo_tweets, t_loc_tweets, user_loc_tweets))
    for x in tweet_d:
        if x['location'] != '':
            print(x)

'''
def city_search(location):
    cities = open("US_cities.txt", "r")
    location = location.strip().lower()
    print("Searching for: " + location)
    for city in cities:
        city = city.strip().lower()
        if city in location:
            print("match!")
            cities.close()
            return city
    cities.close()
    return False
'''

app = gui("Topic Generator", "378x265")
app.addLabel("title", "Topic-O-fier")
app.addLabelEntry("Subject:")
app.addButtons(["Generate", "Cancel"], press)

app.go()
