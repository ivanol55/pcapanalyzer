#!/usr/bin/env python
#Import all the necessary modules
import sys
import datetime
import os
import configparser
import psycopg2

#Parse the config file for the credentials and configuration settings needed
config = configparser.ConfigParser()
config.read("../../backend/.my.cnf")
basedir = config["paths"]["basedir"]
dbDatabase = "analysis_" + sys.argv[1]
pcapdir = basedir + "analysisGenerator/files/pcaps/" + dbDatabase + "/"
csvdir = basedir + "analysisGenerator/files/csvs/" + dbDatabase + "/"

#Get a list of files on the pcap folder
pcaps = os.listdir(pcapdir)

#Create the dedicated transitional csv folder
command = "mkdir " + csvdir
os.system(command)

#Read all pcap files into a csv file
for pcap in pcaps:
	if ".pcap" in pcap:
		pcapfile = pcapdir + pcap
		command = "tshark -r " + pcapfile + " -T fields -e frame.time_epoch -e eth.src -e eth.dst -e ip.src -e ip.dst -e _ws.col.Protocol -e tcp.srcport -e udp.srcport -e tcp.dstport -e udp.dstport -e _ws.col.Info -E separator=, -E quote=d -E occurrence=f -q >> " + csvdir + "input.csv"
		os.system(command)

#Modify the csv data into a standard format and write it into a new file
oldfile = csvdir + "input.csv"
newfile = csvdir + "output.csv"
openfile = open(oldfile, "r")
fixedfile = open(newfile, "w")
for line in openfile:
	linesplit = line.split(",")
	linesplit[6] = linesplit[6] + linesplit[7] 
	del linesplit[7]
	linesplit[7] = linesplit[7] + linesplit[8] 
	del linesplit[8]
	linesplit[0] = str("\"" + datetime.datetime.fromtimestamp(float(linesplit[0].strip("\""))).strftime('%Y-%m-%d %H:%M:%S') + "\"")
	linesplit.insert(1, "\"N/A\"")
	newline = ",".join(linesplit)
	fixedfile.write(newline)
openfile.close()
fixedfile.close()

#File cleanup
command = "rm " + csvdir + "input.csv"
os.system(command)

#Allow file access to the output file
command = "chmod 777 " + csvdir + "output.csv"
os.system(command)

#Retrieve configuration values for database access
dbHost = config["client"]["host"]
dbUser = config["client"]["user"]
dbPassword = config["client"]["password"]
basedir = config["paths"]["basedir"]

#Connect to database
database = psycopg2.connect(host=dbHost, user=dbUser, password=dbPassword, dbname="postgres")
cursor = database.cursor()

#Create the target database
database.autocommit = True
query = "CREATE DATABASE " + dbDatabase
cursor.execute(query)
cursor.close()
database.close()

#Create the table where the data will be stored
database = psycopg2.connect(host=dbHost, user=dbUser, password=dbPassword, dbname=dbDatabase)
cursor = database.cursor()
database.autocommit = True
query = "CREATE TABLE main (id SERIAL PRIMARY KEY, packettimestamp TIMESTAMP NOT NULL, machineid TEXT NOT NULL, sourcemac VARCHAR(100) NOT NULL, destinationmac VARCHAR(100) NOT NULL, sourceip VARCHAR(100) NULL DEFAULT NULL, destinationip VARCHAR(100) NULL DEFAULT NULL, protocol VARCHAR(100) NULL DEFAULT NULL, sourceport INT NULL DEFAULT NULL, destinationport INT NULL DEFAULT NULL, info TEXT NULL DEFAULT NULL);"
cursor.execute(query)
database.autocommit = False

#Open the csv file for readin
csv = csvdir + "output.csv"
contents = open(csv, "r")

#Insert the data from the CSV to the database
query = "COPY main(packettimestamp, machineid, sourcemac, destinationmac, sourceip, destinationip, protocol, sourceport, destinationport, info) FROM STDIN WITH (FORMAT CSV, ENCODING 'UTF-8', DELIMITER ',', HEADER false, QUOTE '\"') \n"
cursor.copy_expert(query, contents)
database.commit()

#Grant SELECT permissions to the user on queryRunner
cursor.execute(query)
query = "GRANT CONNECT ON DATABASE " +  dbDatabase + " TO selectuser"
cursor.execute(query)
query = "GRANT USAGE ON SCHEMA public TO selectuser" 
cursor.execute(query)
query = "GRANT SELECT ON TABLE main TO selectuser"
cursor.execute(query)
query = "GRANT USAGE ON SEQUENCE main_id_seq TO selectuser"
cursor.execute(query)
cursor.close()
database.commit()
database.close()

#Cleanup
command = "rm -rf " + csvdir
os.system(command)
command = "rm -rf " + pcapdir
os.system(command)
