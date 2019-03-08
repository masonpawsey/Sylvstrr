import xml.etree.ElementTree as ET

tree = ET.parse('training_data.xml')
root = tree.getroot()
for child in root.findall('tweet'):
    tweet = child.find('text').text
    tweet = " ".join(filter(lambda x:x[0]!='@', tweet.split()))
    child.find('text').text = tweet
tree.write('output.xml', encoding='UTF-8')
