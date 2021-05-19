#Import all of the necessary system libraries
import datetime
import os
import configparser

#Read and parse the agent configuration file
config = configparser.ConfigParser()
config.read("../../../.my.cnf")

#Create all of the necessary variables from the parsed configuration file
basedir = str(config["paths"]["basedir"])
machineid = str(config["client"]["machineid"])

#List all files in the csv inputs directory
pcapdir = basedir + "packetStream/csvs/inputs/"
pcaps = os.listdir(pcapdir)

#Read every pcap file into a master csv file to process
for pcap in pcaps:
	if ".pcap" in pcap:
		location = pcapdir + pcap
		command = "tshark -r " + location + " -T fields -e frame.time_epoch -e eth.src -e eth.dst -e ip.src -e ip.dst -e _ws.col.Protocol -e tcp.srcport -e udp.srcport -e tcp.dstport -e udp.dstport -e _ws.col.Info -E separator=, -E quote=d -E occurrence=f -q >> " + pcapdir + "/input.csv"
		os.system(command)

#Open the old and new files to process the data
oldfile = basedir + "packetStream/csvs/inputs/input.csv"
newfile = basedir + "packetStream/csvs/outputs/output.csv"
openfile = open(oldfile, "r")
fixedfile = open(newfile, "w")

#For every csv file line, do data processing
for line in openfile:
	#Split the line into an array by the commas
	linesplit = line.split(",")
	#Join the tcp and udp source port columns
	linesplit[6] = linesplit[6] + linesplit[7] 
	#Delete the unnecessary column
	del linesplit[7]
	#Join the tcp and udp destination port columns
	linesplit[7] = linesplit[7] + linesplit[8] 
	#Delete the unnecessary column
	del linesplit[8]
	#Transform the unix epoch timestamp into a formatted ISO UTC timestamp
	linesplit[0] = str("\"" + datetime.datetime.fromtimestamp(float(linesplit[0].strip("\""))).strftime('%Y-%m-%d %H:%M:%S') + "\"")
	#Instert the machine id to the second data column
	linesplit.insert(1, machineid)
	#Join the array back into a string
	newline = ",".join(linesplit)
	#Write the new data into the file
	fixedfile.write(newline)

#Close the open files
openfile.close()
fixedfile.close()

#Cleanup
command = "rm " + basedir + "packetStream/csvs/inputs/input.csv"
os.system(command)
command = "rm " + basedir + "packetStream/csvs/inputs/*.pcap"
os.system(command)

#Move the csv file into the next folder for the next script to handle data input
command = "mv " + basedir + "packetStream/csvs/outputs/output.csv " + basedir + "packetStream/pgsql/inputs/"
os.system(command)
