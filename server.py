import os, sys
sys.path.insert(0, '/LSTM-Sentiment-Analysis')
from LSTMSentimentAnalysis.lstm import LSTM
from flask import Flask, request, send_file, flash, redirect
from werkzeug.utils import secure_filename
import xml.etree.ElementTree as ET
import shutil

numDimensions = 300
maxSeqLength = 250
batchSize = 24
lstmUnits = 64
numClasses = 2
iterations = 100000
import numpy as np
wordsList = np.load('wordsList.npy').tolist()
wordsList = [ word.decode('UTF-8') for word in wordsList ]
wordVectors = np.load('wordVectors.npy')
import tensorflow as tf
tf.reset_default_graph()
labels = tf.placeholder(tf.float32, [batchSize, numClasses])
input_data = tf.placeholder(tf.int32, [batchSize, maxSeqLength])
data = tf.Variable(tf.zeros([batchSize, maxSeqLength, numDimensions]), dtype=tf.float32)
data = tf.nn.embedding_lookup(wordVectors, input_data)
lstmCell = tf.contrib.rnn.BasicLSTMCell(lstmUnits)
lstmCell = tf.contrib.rnn.DropoutWrapper(cell=lstmCell, output_keep_prob=0.25)
value, _ = tf.nn.dynamic_rnn(lstmCell, data, dtype=tf.float32)
weight = tf.Variable(tf.truncated_normal([lstmUnits, numClasses]))
bias = tf.Variable(tf.constant(0.1, shape=[numClasses]))
value = tf.transpose(value, [1, 0, 2])
last = tf.gather(value, int(value.get_shape()[0]) - 1)
prediction = tf.matmul(last, weight) + bias
correctPred = tf.equal(tf.argmax(prediction, 1), tf.argmax(labels, 1))
accuracy = tf.reduce_mean(tf.cast(correctPred, tf.float32))
sess = tf.InteractiveSession()
saver = tf.train.Saver()
saver.restore(sess, tf.train.latest_checkpoint('LSTMSentimentAnalysis/models'))
import re
strip_special_chars = re.compile('[^A-Za-z0-9 ]+')

def cleanSentences(string):
    string = string.lower().replace('<br />', ' ')
    return re.sub(strip_special_chars, '', string.lower())
def getSentenceMatrix(sentence):
    arr = np.zeros([batchSize, maxSeqLength])
    sentenceMatrix = np.zeros([batchSize, maxSeqLength], dtype='int32')
    cleanedSentence = cleanSentences(sentence)
    split = cleanedSentence.split()
    for indexCounter, word in enumerate(split):
        try:
            sentenceMatrix[(0, indexCounter)] = wordsList.index(word)
        except ValueError:
            sentenceMatrix[(0, indexCounter)] = 399999

    return sentenceMatrix


UPLOAD_FOLDER = 'input'
ALLOWED_EXTENSIONS = set(['xml'])
app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.config['SECRET_KEY'] = 'SECRET KEY'

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS


@app.route('/', methods=['GET', 'POST'])
def api():
    if request.method == 'POST':
        if 'file' not in request.files:
            return 'no file in request field\n'
        file = request.files['file']
        if file.filename == '':
            return 'file entry blank\n' 
        if file and allowed_file(file.filename):
            filename = secure_filename(file.filename)
            file.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
            print('\n***File ' + filename + ' recieved***\n')

            tree = ET.parse('input/' + filename)
            root = tree.getroot()
            search = './'
            test = str(root).split("'")
            if test[1] != 'tweets':
                search = './hashtag/tweets/tweet'
            print('Beginning analysis of file ' + filename)
            total_polarity = 0
            number_of_tweets = 0
            for child in root.findall(search):
                tweet = child.find('text').text
                tweet = (' ').join(filter(lambda x: x[0] != '@', tweet.split()))
                inputMatrix = getSentenceMatrix(tweet)
                predictSentiment = sess.run(prediction, {input_data: inputMatrix})[0]
                if abs(predictSentiment[0] - predictSentiment[1]) <= 1:
                    sentiment = 0
                elif predictSentiment[0] > predictSentiment[1]:
                    sentiment = 1
                else:
                    sentiment = -1
                number_of_tweets += 1
                total_polarity += sentiment
                child.find('sentiment').text = str(sentiment)
            print('\nAnalysis complete of file ' + filename)
            polarity = total_polarity/number_of_tweets
            print('Polarity of file ' + filename + ': ' + str(polarity))
            filename = filename.split('.')
            filename = filename[0] + '_o.xml'
            tree.write(filename, xml_declaration=True, encoding='UTF-8')
            xmlData = open(filename, 'a')
            xmlData.write('\n<polarity>' + str(polarity) + '</polarity>')
            xmlData.close()
            source = ''
            dest = 'output/'
            shutil.move(source + filename, dest + filename)
            #sess.close()
            print('File Returned: ' + filename)
            return send_file('output/' + filename)
        return


if __name__ == '__main__':
    reactor_args = {}
    
    def run_twisted_wsgi():
        from twisted.internet import reactor
        from twisted.web.server import Site
        from twisted.web.wsgi import WSGIResource

        resource = WSGIResource(reactor, reactor.getThreadPool(), app)
        site = Site(resource)
        reactor.listenTCP(5016, site)
        reactor.run(**reactor_args)
        
    if app.debug:
        # Disable twisted signal handlers in development only.
        reactor_args['installSignalHandlers'] = 0
        # Turn on auto reload.
        import werkzeug.serving
        run_twisted_wsgi = werkzeug.serving.run_with_reloader(run_twisted_wsgi)

    run_twisted_wsgi()

#curl -F 'file=@path/to/file.xml' bender.cs.csub.edu:5016 >> path/to/outputfile.xml
