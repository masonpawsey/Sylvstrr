from appJar import gui
import tweepy
import json
import re
import subprocess
import API_Exporter
import pandas
import datetime
import dateutil.parser
from operator import itemgetter

def get_tweets(subject, count):
    tweets = subprocess.check_output(["scrape-twitter", "search", "--query", subject,
                "--type", "latest", "--lang:en", "--count", count])
    return tweets

def get_hashtags(tweets, subject):
    hashtags = {}
    for element in tweets:
        try:
            for new_tag in element['hashtags']:
                tag = new_tag['hashtag'].lower().replace('https', '')
                if not (tag.startswith('#')):
                    tag = "#" + tag
                if tag in hashtags:
                    hashtags[tag] += 1
                else:
                    hashtags[tag] = 1
        except IndexError:
            pass
    return hashtags

def top_hashtags(hashtags):
    top_tags = []
    tag_list = []
    for tag in hashtags:
        tag_list.append([tag, hashtags[tag]])
    tag_list.sort(key=itemgetter(1), reverse=True)
    if len(tag_list) >= 20:
        top_tags = tag_list[:20]
    else:
        top_tags = tag_list
    return top_tags

def check_relevance(top_tags):
    relevance = []
    new_hashtags = {}
    for x in top_tags:
        relevance.append([x[0], 0])
    for x in relevance:
        tweets_w_tag = json.loads(get_tweets(x[0], "100"))
        new_hashtags = get_hashtags(tweets_w_tag, x[0])
        for new_tag in new_hashtags:
            for tag in relevance:
                if new_tag == tag[0] and new_tag != x[0]:
                    tag[1] += new_hashtags[new_tag]
    relevance.sort(key=itemgetter(1), reverse=True)
    #print('**********RELEVANCE*********')
    #for x in relevance:
    #    print(x)
    return relevance

def get_frequency(tweets):
    times = []
    for element in tweets:
        times.append(dateutil.parser.parse(element['time']))
    #print("Frequency:", len(times), "in", times[0] - times[-1])
    time_deltas = [times[i-1] - times[i] for i in range(1, len(times))]
    avg_time = sum(time_deltas, datetime.timedelta(0)) / len(times)
    #print("Average distance:", avg_time)
    return(avg_time)

####################START HERE##################################
def press(button):
    if button == "Cancel":
        app.stop()
    if button == "Generate":
        subject = app.getEntry("Subject:")
        count = app.getEntry("Count:")
        agnostic = app.getCheckBox('agnostic')
        #api = connect()
        if not (subject.startswith("#")):
            subject = "#" + subject
        tweets = json.loads(get_tweets(subject, count))
        hashtags = get_hashtags(tweets, subject)
        get_frequency(tweets)
        #for hashtag in hashtags:
        #    print(hashtag, hashtags[hashtag])
        top_tags = top_hashtags(hashtags)
        print('***********TOP TAGS***********')
        for x in top_tags:
            print(x)
        #relevant_tags = check_relevance(top_tags[:10])
        print("***********RELEVANT TAGS***********")
        #for tag in relevant_tags[:6]:
        #    if tag[0] != subject:
        #        print(tag)

        if agnostic:
            print('GO TIME')
            API_Exporter.search_hashtag(subject, top_tags[:5])
        ##########GET THAT API###################

app = gui("Topic Generator", "378x265")
app.addLabel("title", "Topic-O-fier")
app.addLabelEntry("Subject:")
app.addLabelEntry("Count:")
app.addCheckBox("agnostic")
app.addButtons(["Generate", "Cancel"], press)

app.go()
