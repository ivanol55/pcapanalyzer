#Import all of the necessary system libraries
import psycopg2
import os
import configparser

#Read and parse the server configuration file
config = configparser.ConfigParser()
configFile = "../../../.my.cnf"
config.read(configFile)

#Set all of the necessary variables from the server configuration file
dbHost = config["client"]["host"]
dbUser = config["client"]["user"]
dbPassword = config["client"]["password"]
dbDatabase = config["client"]["database"]
basedir = config["paths"]["basedir"]

#Connect to the target database, generally packetstream, where you will insert the data from the csv
connString = "host='" + dbHost + "' dbname='" + dbDatabase + "' user='" + dbUser + "' password='" + dbPassword + "'"
conn = psycopg2.connect(connString)
cursor = conn.cursor()

#Read the CSV data into the connected database
query = "COPY main(packettimestamp, machineid, sourcemac, destinationmac, sourceip, destinationip, protocol, sourceport, destinationport, info) FROM '" + basedir + "packetStream/pgsql/inputs/output.csv' WITH (FORMAT CSV, ENCODING 'UTF-8', DELIMITER ',', HEADER false, QUOTE '\"')"
cursor.execute(query)
cursor.close()
conn.commit()
conn.close()

#File cleanup
delete = basedir + "packetStream/pgsql/inputs/output.csv"
os.remove(delete)
