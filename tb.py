from textblob import classifiers
import xml.etree.ElementTree as ET
import random
import pickle

tree = ET.parse('training_data.xml')
root = tree.getroot()
training = []
tweet = ()
for child in root.findall('tweet'):
    if child.find('sentiment').text == '0':
        sentiment = 'neg'
    elif child.find('sentiment').text == '4':
        sentiment = 'pos'
    else:
        continue
    tweet = ((child.find('text').text), sentiment)
    training.append(tweet)

tree = ET.parse('test_data.xml')
root = tree.getroot()
testing = []
tweet = ()
for child in root.findall('tweet'):
    if child.find('sentiment').text == '0':
        sentiment = 'neg'
    elif child.find('sentiment').text == '4':
        sentiment = 'pos'
    else:
        continue
    tweet = ((child.find('text').text), sentiment)
    testing.append(tweet)

train = []
for i in range(3000):
    train.append(training.pop(random.randint(0,len(training))))

nb_classifier = classifiers.NaiveBayesClassifier(train)

print(nb_classifier.accuracy(testing))
pickle.dump(nb_classifier, open('classifier.p','wb'))
pickle.dump(training, open('training.p','wb'))
