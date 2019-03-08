import tensorflow as tf
import xml.etree.ElementTree as ET
import re
import math
import random
from collections import Counter
import numpy as np
import os
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '2'

def get_target(words, idx, window_size=5):
    R = np.random.randint(1, window_size+1)
    start = idx - R if (idx - R) > 0 else 0
    stop = idx + R
    target_words = set(words[start:idx] + words[idx+1:stop+1])
    return list(target_words)

def get_batches(words, batch_size, window_size=5):
    n_batches = len(words)//batch_size
    words = words[:n_batches*batch_size]
    for idx in range(0, len(words), batch_size):
        x, y = [], []
        batch = words[idx:idx+batch_size]
        for ii in range(len(batch)):
            batch_x = batch[ii]
            batch_y = get_target(batch, ii, window_size)
            y.extend(batch_y)
            x.extend([batch_x]*len(batch_y))
        yield x, y

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

words = text.split()

word_count = Counter(words)

sorted_vocab = sorted(word_count, key=word_count.get, reverse=True)
int_to_vocab = {ii: word for ii, word in enumerate(sorted_vocab)}
vocab_to_int = {word: ii for ii, word in int_to_vocab.items()}
int_words = [vocab_to_int[word] for word in words]

t = 1e-5
train_words = []
lookup = Counter(int_words)
freq = {word: (lookup[word]/len(int_words)) for word in int_words}
sorted_freq = sorted(freq.values())
print(max(sorted_freq))
print(min(sorted_freq))

p_drop = {word: 1 - np.sqrt(t/freq[word]) for word in int_words}
sorted_p_drop = sorted(p_drop.values())
print(max(sorted_p_drop))
print(min(sorted_p_drop))
for word in int_words:
    p_w = 1 - np.sqrt(t/freq[word])
    if (1- p_w) > random.random(): #If p is 0.7, there is a 70% chance of random number < 0.7
        train_words.append(word)

i = sorted_vocab.index('death')
print(train_words[i])

train_graph = tf.Graph()

with train_graph.as_default():
    inputs = tf.placeholder(tf.int32, [None], name='inputs')
    labels = tf.placeholder(tf.int32, [None, None], name='labels')

n_vocab = len(int_to_vocab)
n_embedding = 50

with train_graph.as_default():
    embedding = tf.Variable(tf.random_uniform((n_vocab, n_embedding), -1, 1))
    word_vector = tf.nn.embedding_lookup(embedding, inputs)

n_sampled = 100
with train_graph.as_default():
    softmax_w = tf.Variable(tf.truncated_normal((n_vocab,n_embedding),
        stddev=0.1))
    softmax_b = tf.Variable(tf.zeros(n_vocab))

    loss = tf.nn.sampled_softmax_loss(softmax_w, softmax_b, labels,
            word_vector, 5, n_vocab)
    cost = tf.reduce_mean(loss)
    optimizer = tf.train.AdamOptimizer().minimize(cost)

with train_graph.as_default():
    ## From Thushan Ganegedara's implementation
    valid_size = 16 # Random set of words to evaluate similarity on.
    valid_window = 100
    # pick 8 samples from (0,100) and (1000,1100) each ranges. lower id implies more frequent 
    valid_examples = np.array(random.sample(range(valid_window), valid_size//2))
    valid_examples = np.append(valid_examples, 
                               random.sample(range(1000,1000+valid_window), valid_size//2))

    valid_dataset = tf.constant(valid_examples, dtype=tf.int32)
    
    # We use the cosine distance:
    norm = tf.sqrt(tf.reduce_sum(tf.square(embedding), 1, keepdims=True))
    normalized_embedding = embedding / norm
    valid_embedding = tf.nn.embedding_lookup(normalized_embedding, valid_dataset)
    similarity = tf.matmul(valid_embedding, tf.transpose(normalized_embedding))

epochs = 10
batch_size = 1000
window_size = 10
with train_graph.as_default():
	saver = tf.train.Saver()

with tf.Session(graph=train_graph) as sess:
    iteration = 1
    loss = 0
    sess.run(tf.global_variables_initializer())

    for e in range(1, 11):
        batches = get_batches(train_words, batch_size, window_size)
        for x, y in batches:
            feed = {inputs: x, labels:np.array(y)[: None]}
            train_loss, _ = sess.run([cost,optimizer], feed_dict=
                    {inputs: x, labels: np.array(y)[:, None]})
            loss += train_loss

            if iteration%100 == 0:
                print("Epoch {}/{}".format(e, epochs),
                      "Iteration: {}".format(iteration),
                      "Avg. Training loss: {:.4f}".format(loss/100))
                loss = 0
            if iteration%1000 == 0:
                sim = similarity.eval()
                for i in range(valid_size):
                    valid_word = int_to_vocab[valid_examples[i]]
                    top_k = 8
                    nearest = (-sim[i, :]).argsort()[1:top_k+1]
                    log = 'Nearest to %s' % valid_word
                    for k in range(top_k):
                        close_word = int_to_vocab[nearest[k]]
                        log = '%s %s,' % (log, close_word)
                    print(log)
            iteration += 1
    save_path = saver.save(sess, "checkpoints/text8.cpkt")
    embed_mat = sess.run(normalized_embedding)
