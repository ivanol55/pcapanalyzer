#Import all of the necessary system libraries
import psycopg2
import os
import configparser

#Read and parse the agent configuration file
config = configparser.ConfigParser()
configFile = "../../../.my.cnf"
config.read(configFile)

#Set all the necessary variables from the parsed configuration file
dbHost = config["client"]["host"]
dbUser = config["client"]["user"]
dbPassword = config["client"]["password"]
dbDatabase = config["client"]["database"]
basedir = config["paths"]["basedir"]

#Connect to the target remote database
connString = "host='"  + dbHost + "' dbname='" + dbDatabase + "' user='" + dbUser + "' password='" + dbPassword + "'"
conn = psycopg2.connect(connString)
cursor = conn.cursor()

#Open the csv file for reading
csv = basedir + "packetStream/pgsql/inputs/output.csv"
contents = open(csv, "r")

#Insert the data from the CSV to the database
query = "COPY main(packettimestamp, machineid, sourcemac, destinationmac, sourceip, destinationip, protocol, sourceport, destinationport, info) FROM STDIN WITH (FORMAT CSV, ENCODING 'UTF-8', DELIMITER ',', HEADER false, QUOTE '\"') \n"
cursor.copy_expert(query, contents)
cursor.close()
conn.commit()
conn.close()

#Cleanup
delete = basedir + "packetStream/pgsql/inputs/output.csv"
os.remove(delete)
