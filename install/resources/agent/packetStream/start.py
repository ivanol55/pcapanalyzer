#Import the necessary system libraries for the agent
import subprocess
import time
import os
import configparser

#Read and parse the server configuration file
config = configparser.ConfigParser()
config.read("../.my.cnf")

#Set all of the necessary variables from the server configuration file
basedir = str(config["paths"]["basedir"])

# move the orphaned pcap files from the last system run
command = "mv " + basedir + "packetStream/pcaps/outputs/*.pcap " + basedir + "packetStream/csvs/inputs/"
moveresidue = os.system(command)

#Move to the pcaps scripts folder and start capturing data on this agent host
os.chdir("pcaps/scripts/")
command = ["python3", basedir + "packetStream/pcaps/scripts/generatepcaps.py"]
tshark = subprocess.Popen(command)

#Move to the main directory
os.chdir("../../")
while True:	
	#Move to the pcaps scripts folder
	os.chdir("pcaps/scripts/")
	#run the pcap moving script
	command = ["python3", basedir + "packetStream/pcaps/scripts/movepcaps.py"]
	moveracsv = subprocess.Popen(command)
	#Sleep for 30 seconds
	time.sleep(30)
	#Move to the csvs scripts folder
	os.chdir("../../csvs/scripts/")
	#run the pcap to csv processing script
	command = ["python3", basedir + "packetStream/csvs/scripts/generatecsv.py"]	
	procesarcsv = subprocess.Popen(command)
	#Sleep for 30 seconds
	time.sleep(30)
	#Move to the pgsql scripts folder
	os.chdir("../../pgsql/scripts/")
	#run the database data copying script
	command = ["python3", basedir + "packetStream/pgsql/scripts/csvtodb.py"]
	subirabbdd = subprocess.Popen(command)
	#Sleep for 30 seconds
	time.sleep(30)
	#Return to the original directory
	os.chdir("../../")
