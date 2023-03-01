env = {}
from flask import Flask, render_template
from flow_common import load_env, mail_data
from flask_mail import Mail, Message
load_env()

[username, password] = mail_data()

def send_simple_mail(header, message):
    
    with app.app_context():
        msg = Message(header, sender = sender, recipients = receiver)
        msg.html = render_template('error.html', message=message)   
         
        print(msg)
        mail.send(msg)


app = Flask(__name__)
app.config['MAIL_SERVER']='smtp.gmail.com' 
app.config['MAIL_USERNAME'] = username
app.config['MAIL_PASSWORD'] = password

app.config['MAIL_USE_SSL'] = True
app.config['MAIL_PORT'] = 465

sender = username
receiver = ['appsupport@flowglobal.net']

mail = Mail(app)

