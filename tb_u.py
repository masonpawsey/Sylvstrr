from textblob import classifiers
import xml.etree.ElementTree as ET
import random
import pickle

with open('classifier.p', 'rb') as f:
    nb_classifier = pickle.load(f)
with open('training.p', 'rb') as f:
    training = pickle.load(f)

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
for i in range(500):
    train.append(training.pop(random.randint(0,len(training))))

nb_classifier.update(train)

print(nb_classifier.accuracy(testing))
pickle.dump(nb_classifier, open('classifier2.p','wb'))
pickle.dump(training, open('training2.p','wb'))
