#Import all of the necessary libraries
import subprocess
import time
import os
import configparser

#Read and parse the server configuration file
config = configparser.ConfigParser()
config.read("../.my.cnf")

#Setup all of the necessary variables from the configuration file
basedir = str(config["paths"]["basedir"])

#Cleanup from the last packetstream run
command = "mv " + basedir + "packetStream/pcaps/outputs/*.pcap " + basedir + "packetStream/csvs/inputs/"
moveresidue = os.system(command)

#Change to the pcap script folder and start generating pcaps
os.chdir("pcaps/scripts/")
command = ["python3", basedir + "packetStream/pcaps/scripts/generatepcaps.py"]
tshark = subprocess.Popen(command)

#Return to the main directory
os.chdir("../../")
while True:
	#Change directory to the pcap scripts directory and run the pcap mover script
	os.chdir("pcaps/scripts/")
	command = ["python3", basedir + "packetStream/pcaps/scripts/movepcaps.py"]
	subprocess.Popen(command)
	#Sleep for 30 seconds
	time.sleep(3)
	# Change directory to the csv scripts directory and run the csv generator script
	os.chdir("../../csvs/scripts/")
	command = "python3 " + basedir + "packetStream/csvs/scripts/generatecsv.py"	
	os.system(command)
	#Sleep for 30 seconds
	time.sleep(3)
	# Change directory to the pgsql scripts directory and run the database insert script
	os.chdir("../../pgsql/scripts/")
	command = "python3 " + basedir + "packetStream/pgsql/scripts/csvtodb.py"
	os.system(command)
	#sleep for 30 seconds
	time.sleep(3)
	#Return to the original folder
	os.chdir("../../")

