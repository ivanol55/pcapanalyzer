#Import necessary modules
import psycopg2
import configparser
import smtplib
from email.message import EmailMessage
import datetime

#Read and parse the server configuration file
config = configparser.ConfigParser()
ruta = "../../.my.cnf"
config.read(ruta)

#Set the necessary configuration variables from configParser
dbHost = config["client"]["host"]
dbUser = config["client"]["user"]
dbPassword = config["client"]["password"]
dbDatabase = config["client"]["database"]
basedir = config["paths"]["basedir"]

#Connect to the password set on the configuration file, generally packetstream
connString = "host='" + dbHost + "' dbname='" + dbDatabase + "' user='" + dbUser + "' password='" + dbPassword + "'"
conn = psycopg2.connect(connString)
cursor = conn.cursor()

#Run a query against that database
query = "SELECT COUNT(*), sourceip FROM main GROUP BY sourceip"
cursor.execute(query)

#Store a result matrix of all the resulting rows
result = cursor.fetchall()
cursor.close()
conn.close()

#Iterate through all of the rows with a check for a threshold
counter = 0
mailString = "Found warnings for the test probe:\n\n"
for value in result:
    if value[0] > 5:
        counter = counter + 1
        mailString = mailString + str(value[0]) + " alerts for IP address " + str(value[1]) + "\n"
mailString = mailString + "\n\nMessage sent using PCAPAnalyzer."

#If there is a detected result, craft an email and send it to the specified address with the local smtp server
if counter > 0:
    msg = EmailMessage()
    msg.set_content(mailString)
    datetime = datetime.datetime.now(datetime.timezone.utc)
    datetime = datetime.strftime("%Y-%m-%d %H:%M:%S")
    msg["Subject"] = "Alert for probe on " + datetime + " UTC"
    msg["From"] = "PCAPAnalyzer@localhost"
    msg["To"] = "ivan@localhost"
    s = smtplib.SMTP("localhost")
    s.send_message(msg)
    s.quit()

