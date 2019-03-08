import os
#import test
from flask import Flask, request, send_file, flash, redirect
from  werkzeug.utils import secure_filename

UPLOAD_FOLDER = ''
ALLOWED_EXTENSIONS = set(['xml'])

app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.config['SECRET_KEY'] = "SECRET KEY"

def allowed_file(filename):
    return '.' in filename and \
            filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

@app.route('/', methods = ['GET', 'POST'])
def api():
    if request.method == 'GET':
        return send_file('tweets_o.xml') 

    if request.method == 'POST':
        if 'file' not in request.files:
            flash('No file part')
            return redirect('http://sylvstrr.com/NOFILE')
        file = request.files['file']
        if file.filename == '':
            return redirect('http://sylvstrr.com/WUT')
        if file and allowed_file(file.filename):
            filename = secure_filename('tweets.xml')
            file.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
            test.Hello()
            return redirect('httpe://sylvstrr.com')
        return

if __name__ == '__main__':
    #port = int(os.environ.get('PORT', 5016))
    #app.run(host='0.0.0.0', port=port, debug=True)
    app.run()   #to run type "PYTHONPATH=. twistd -n web --port "tcp:port=5016" --wsgi server.app"
