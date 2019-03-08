import tensorflow as tf
import xml.etree.ElementTree as ET
import re
import math
import random
from collections import Counter
import numpy as np
import os
import datetime
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '2'

def getTrainBatch(batchSize, maxSeqLength, ids, posMax, negMax):
    labels = []
    arr = np.zeros([batchSize, maxSeqLength])
    for i in range(batchSize):
        if i%2 == 0:
            num = random.randint(1,posMax - math.ceil(posMax/4))
            labels.append([1,0])
        else:
            num = random.randint(posMax + math.ceil(negMax/4),negMax)
            labels.append([0,1])
        arr[i] = ids[num-1:num]
    return arr, labels

def getTestBatch(batchSize, maxSeqLength, ids, posMax, negMax):
    labels = []
    arr = np.zeros([batchSize, maxSeqLength])
    for i in range(batchSize):
        num = random.randint(posMax,negMax)
        if num <= posMax:
            labels.append([1,0])
        else:
            labels.append([0,1])
        arr[i] = ids[num-1:num]
    return arr, labels

tree = ET.parse('training_data.xml')
root = tree.getroot();

tmp = []

for child in root.findall('tweet'):
    tweet = child.find('text').text
    tmp.append(tweet)
text = ' '.join(tmp)

text = text.lower()
text = text.replace('.', ' __PERIOD__ ')
text = text.replace('?', ' __QUESTION__ ')
text = text.replace('!', ' __EXCLAMATION__ ')
text = text.replace('-', ' ')
text = text.replace('/', ' ')
text = text.replace('[', '')
text = text.replace('', '')
text = re.sub(r'[{}()"\']', '', text)
text = re.sub(r'[;,:-]', ' ', text)

wordsList = text.split()

word_count = Counter(wordsList)

sorted_vocab = sorted(word_count, key=word_count.get, reverse=True)
int_to_vocab = {ii: word for ii, word in enumerate(sorted_vocab)}
vocab_to_int = {word: ii for ii, word in int_to_vocab.items()}
int_words = [vocab_to_int[word] for word in wordsList]
'''
t = 1e-5
wordVector = []
lookup = Counter(int_words)
freq = {word: (lookup[word]/len(int_words)) for word in int_words}
sorted_freq = sorted(freq.values())

p_drop = {word: 1 - np.sqrt(t/freq[word]) for word in int_words}
sorted_p_drop = sorted(p_drop.values())
for word in int_words:
    p_w = 1 - np.sqrt(t/freq[word])
    if (1- p_w) > random.random(): #If p is 0.7, there is a 70% chance of random number < 0.7
        wordVector.append(word)
'''
wordVector = []
for word in int_words:
    wordVector.append(word)

wordVector = np.asarray(wordVector)
tree = ET.parse('training_data.xml')
root = tree.getroot();

posSent = []
negSent = []
posLines = 0
negLines = 0

for child in root.findall('tweet'):
    tweet = child.find('text').text
    if(child.find('sentiment').text == '0'):
        negSent.append(tweet)
        negLines += 1
    if(child.find('sentiment').text == '4'):
        posSent.append(tweet)
        posLines += 1

maxSeqLength = 200
ids = np.zeros((posLines+negLines, maxSeqLength), dtype='int32')

sentenceCounter = 0
for line in posSent:
    line = line.lower()
    line = line.replace('.', ' __PERIOD__ ')
    line = line.replace('?', ' __QUESTION__ ')
    line = line.replace('!', ' __EXCLAMATION__ ')
    line = line.replace('-', ' ')
    line = line.replace('/', ' ')
    line = line.replace('[', '')
    line = line.replace('', '')
    line = re.sub(r'[{}()"\']', '', line)
    line = re.sub(r'[;,:-]', ' ', line)
    indexCounter = 0
    posSplit = line.split()
    posSplit = posSplit[:maxSeqLength]
    for word in posSplit:
        try:
            ids[sentenceCounter][indexCounter] = wordsList.index(word)
        except:
            ids[sentenceCounter][indexCounter] = len(wordsList)
        indexCounter += 1
    sentenceCounter += 1

posMax = sentenceCounter

for line in negSent:
    line = line.lower()
    line = line.replace('.', ' __PERIOD__ ')
    line = line.replace('?', ' __QUESTION__ ')
    line = line.replace('!', ' __EXCLAMATION__ ')
    line = line.replace('-', ' ')
    line = line.replace('/', ' ')
    line = line.replace('[', '')
    line = line.replace('', '')
    line = re.sub(r'[{}()"\']', '', line)
    line = re.sub(r'[;,:-]', ' ', line)
    indexCounter = 0
    negSplit = line.split()
    for word in posSplit:
        try:
            ids[sentenceCounter][indexCounter] = wordsList.index(word)
        except:
            ids[sentenceCounter][indexCounter] = len(wordsList)
        indexCounter += 1
    sentenceCounter += 1

negMax = sentenceCounter

batchSize = 24
lstmUnits = 64
numClasses = 2
iterations = 10000

tf.reset_default_graph()
with tf.Session() as sess:
    labels = tf.placeholder(tf.float32, [batchSize, numClasses])
    input_data = tf.placeholder(tf.int32, [batchSize, maxSeqLength])
    numDimensions = wordVector.shape[0]
    data = tf.Variable(tf.zeros([batchSize, maxSeqLength, numDimensions]), dtype=tf.float32)
    #data = tf.nn.embedding_lookup(wordVector,input_data)
    lstmCell = tf.contrib.rnn.BasicLSTMCell(lstmUnits)
    lstmCell = tf.contrib.rnn.DropoutWrapper(cell=lstmCell, output_keep_prob=0.75)
    value, _ = tf.nn.dynamic_rnn(lstmCell, data, dtype=tf.float32)

    weight = tf.Variable(tf.truncated_normal([lstmUnits, numClasses]))
    bias = tf.Variable(tf.constant(0.1, shape=[numClasses]))
    value = tf.transpose(value, [1, 0, 2])
    last = tf.gather(value, int(value.get_shape()[0]) - 1)
    prediction = (tf.matmul(last, weight) + bias)

    correctPred = tf.equal(tf.argmax(prediction,1), tf.argmax(labels,1))
    accuracy = tf.reduce_mean(tf.cast(correctPred, tf.float32))

    loss = tf.reduce_mean(tf.nn.softmax_cross_entropy_with_logits(logits=prediction, labels=labels))
    optimizer = tf.train.AdamOptimizer().minimize(loss)

sess = tf.InteractiveSession()
saver = tf.train.Saver()
sess.run(tf.global_variables_initializer())

tf.summary.scalar('Loss', loss)
tf.summary.scalar('Accuracy', accuracy)
merged = tf.summary.merge_all()
logdir = "tensorboard/" + datetime.datetime.now().strftime("%Y%m%d-%H%M%S") + "/"
writer = tf.summary.FileWriter(logdir, sess.graph)

for i in range(iterations):
    print(i)
    #Next Batch of reviews
    nextBatch, nextBatchLabels = getTrainBatch(batchSize, maxSeqLength, ids, posMax, negMax)
    sess.run(optimizer, {input_data: nextBatch, labels: nextBatchLabels})
   
    #Write summary to Tensorboard
    if (i % 50 == 0):
        summary = sess.run(merged, {input_data: nextBatch, labels: nextBatchLabels})
        writer.add_summary(summary, i)

    #Save the network every 10,000 training iterations
    if (i % 10000 == 0 and i != 0):
        save_path = saver.save(sess, "models/pretrained_lstm.ckpt", global_step=i)
        print("saved to %s" % save_path)
writer.close()
