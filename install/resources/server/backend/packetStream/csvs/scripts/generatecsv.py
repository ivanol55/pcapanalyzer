#Import all of the necessary system libraries
import datetime
import os
import configparser

#Read and parse the server configuration file
config = configparser.ConfigParser()
config.read("../../../.my.cnf")

#Set all of the necessary variables from the parsed configuration file
basedir = str(config["paths"]["basedir"])
machineid = str(config["client"]["machineid"])
pcapdir = basedir + "packetStream/csvs/inputs/"

#Get a list of all the pcap files in the csv pcap folder to process
pcaps = os.listdir(pcapdir)

#Read all pcap files into a unified csv file to process
for pcap in pcaps:
	if ".pcap" in pcap:
		location = pcapdir + pcap
		command = "tshark -r " + location + " -T fields -e frame.time_epoch -e eth.src -e eth.dst -e ip.src -e ip.dst -e _ws.col.Protocol -e tcp.srcport -e udp.srcport -e tcp.dstport -e udp.dstport -e _ws.col.Info -E separator=, -E quote=d -E occurrence=f -q >> " + pcapdir + "/input.csv"
		os.system(command)

#Open the old source file and destination edited file
oldfile = basedir + "packetStream/csvs/inputs/input.csv"
newfile = basedir + "packetStream/csvs/outputs/output.csv"
openfile = open(oldfile, "r")
fixedfile = open(newfile, "w")

#For every line in the csv file, apply the format changes that are necessary
for line in openfile:
	#Split into an array by the commas
	linesplit = line.split(",")
	#compress the tcp/udp source ports into one column
	linesplit[6] = linesplit[6] + linesplit[7] 
	#Delete the unnecessary column
	del linesplit[7]
	#compress the tcp/udp source ports into one column
	linesplit[7] = linesplit[7] + linesplit[8] 
	#Delete the unnecessary column
	del linesplit[8]
	#Format the epoch time format into an ISO UTC timestamp
	linesplit[0] = str("\"" + datetime.datetime.fromtimestamp(float(linesplit[0].strip("\""))).strftime('%Y-%m-%d %H:%M:%S') + "\"")
	#Insert the machineid into the captured data
	linesplit.insert(1, machineid)
	#Join the data back into a string
	newline = ",".join(linesplit)
	#Write the data into the new file
	fixedfile.write(newline)

#Close the open csv files
openfile.close()
fixedfile.close()

#file cleanup
command = "rm " + basedir + "packetStream/csvs/inputs/input.csv"
os.system(command)
command = "rm " + basedir + "packetStream/csvs/inputs/*.pcap"
os.system(command)

#Move the finished file for the next script to read
command = "mv " + basedir + "packetStream/csvs/outputs/output.csv " + basedir + "packetStream/pgsql/inputs/"
os.system(command)
