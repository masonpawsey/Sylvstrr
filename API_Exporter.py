from appJar import gui
import tweepy
import json
import re
import numpy
import geocoder
import datetime

def getKeys():
    f = open("twitter_keys.txt", "r")
    if f.mode == 'r':
        keys = {}
        contents = f.read().split('\n')
        keys['consumer_key'] = contents[0]
        keys['consumer_secret'] = contents[1]
        keys['access_token'] = contents[2]
        keys['access_token_secret'] = contents[3]
    f.close()
    return keys

def get_map():
    f = open("map_key.txt", "r")
    if f.mode == 'r':
        map_key = f.read()

    f.close();
    return map_key
'''
def press(button):
    if button == "Cancel":
        app.stop()
    if button == "Generate":
        subject = app.getEntry("Subject:")
        api = connect()
        search_hashtag(api, subject)
'''
def connect():
    keys = getKeys()

    auth = tweepy.OAuthHandler(keys["consumer_key"], keys["consumer_secret"])
    auth.set_access_token(keys["access_token"], keys["access_token_secret"])

    api = tweepy.API(auth)

    return api

    '''
    times = []
    for element in tweets:
        times.append(dateutil.parser.parse(element['time']))
    #print("Frequency:", len(times), "in", times[0] - times[-1])
    time_deltas = [times[i-1] - times[i] for i in range(1, len(times))]
    avg_time = sum(time_deltas, datetime.timedelta(0)) / len(times)
    #print("Average distance:", avg_time)
    return(avg_time)
    '''

def get_frequency(tweet_l):
    #print(tweet_l[0]['time'], tweet_l[-1]['time'])
    times = []
    for tweet in tweet_l:
        times.append(tweet['time'])
    time_deltas = [times[i-1] - times[i] for i in range(1, len(times))]
    frequency = sum(time_deltas,datetime.timedelta(0)) / len(times)
    return frequency



def search_hashtag(subject, top_tags):
    api = connect()
    map_key = get_map()

    total_tweets = 0
    geo_tweets = 0
    t_loc_tweets = 0
    user_loc_tweets = 0

    tweet_dict = {}

    for tag in top_tags:
        tag = tag[0]
        #print(tag)
        tweet_dict[tag] = {}
        search = tag + " -filter:media"
        tweet_l = []
        ###Search subject
        tweets = api.search(q=search, count=30, lang="en", place_country='US', tweet_mode="extended")
        if len(tweets) == 0:
            print("No tweets returned. Terminating...")
            exit(1)
        for tweet in tweets:
            total_tweets += 1
            id = tweet.id
            time = tweet.created_at
            text = tweet.full_text
            #print(id, time, text)

            ###########GET LOCATION################
            if tweet.coordinates:
                location = str(tweet.coordinates['coordinates'][1]) + ', ' + str(tweet.coordinates['coordinates'][0])
                geo_tweets += 1
            elif tweet.place:
                box = tweet.place.bounding_box.coordinates
                lat = [box[0][0][1], box[0][2][1]]
                long = [box[0][0][0], box[0][1][0]]
                try:
                    location = str(numpy.average(lat)) + ", " + str(numpy.average(long))
                except TypeError:
                    print("********************TypeError:")
                    print(box)
                    print(lat, long)
                    location = ''
                t_loc_tweets += 1
            elif tweet.user.location:
                g = geocoder.mapquest(tweet.user.location, key=map_key)
                location = str(g.lat) + ", " + str(g.lng)
                user_loc_tweets += 1
            else:
                location = ''

            if location != '':
                tweet_l.append({'id': id,
                                'time':time,
                                'text': text,
                                'location': location})

        tweet_dict[tag]['frequency'] = get_frequency(tweet_l)
        tweet_dict[tag]['tweets'] = tweet_l
        tweet_dict[tag]['sentiment'] = ''

        #print("Total tweets: %d; Geolocated: %d; Tweet Location: %d, User Location: %d" % (total_tweets, geo_tweets, t_loc_tweets, user_loc_tweets))
        #print(tweet_dict[tag])
        #for t in tweet_dict:
        #     print(t)
            #print("Tag:", t[tag])
            #print("Frequency:", t['frequency'])
            #print('Sentiment:', t['sentiment'])
            #print('Tweets:', t['tweets'])
    print("Total tweets: %d; Geolocated: %d; Tweet Location: %d, User Location: %d" % (total_tweets, geo_tweets, t_loc_tweets, user_loc_tweets))
    for t in tweet_dict:
        print(t)
        for x in tweet_dict[t]:
            print(x)
            if (x == 'tweets'):
                for y in tweet_dict[t][x]:
                    #print(y) #tweet_dict[t][x][0])
                    for z in y:
                        print(z, y[z])#, tweet_dict[t][x][y][z])
            else:
                print("\n", tweet_dict[t][x])

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
'''
app = gui("Topic Generator", "378x265")
app.addLabel("title", "Topic-O-fier")
app.addLabelEntry("Subject:")
app.addButtons(["Generate", "Cancel"], press)

app.go()
'''
