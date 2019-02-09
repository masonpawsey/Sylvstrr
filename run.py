import _pickle as pickle
import textblob
import xml.etree.ElementTree as ET

with open('classifier.p','rb') as f:
    nb_classifier = pickle.load(f)

xmlFile = 'tweets_o.xml'
xmlData = open(xmlFile, 'w')
tree = ET.parse('tweets.xml')
root = tree.getroot()

xmlData.write('<?xml version="1.0" encoding="UTF-8"?>' + '\n')
xmlData.write('<tweets>' + '\n')

it = 0.0
total = 0.0
for child in root.findall('tweet'):
    prob_dist = nb_classifier.prob_classify(child.find('text').text)
    sent = prob_dist.prob("pos") - prob_dist.prob("neg")
    xmlData.write('    <tweet>' + '\n')
    xmlData.write('        <text>')
    xmlData.write(child.find('text').text)
    xmlData.write('</text>' + '\n')
    xmlData.write('        <sentiment>')
    if abs(sent) <= 0.1:
        xmlData.write('0')
    elif sent > 0:
        xmlData.write('2')
        total = total + 2
    else:
        xmlData.write('-2')
        total = total - 2
    xmlData.write('</sentiment>' + '\n')
    xmlData.write('    </tweet>' + '\n')
    it = it + 1

total = total/it

xmlData.write('</tweets>' + '\n')
xmlData.write('<polarity>' + '\n')
xmlData.write('    <total>')
xmlData.write(str(total))
xmlData.write('</total>' + '\n')
xmlData.write('</polarity>')
xmlData.close()
