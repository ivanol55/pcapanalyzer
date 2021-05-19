# Import all of the necessary system modules
import os
import subprocess
import configparser

#Read and parse the server configuration file
config = configparser.ConfigParser()
config.read("../../../.my.cnf")

#Set all of the necessary system variables from the parsed configuration file
basedir = str(config["paths"]["basedir"])
directory = basedir + ""

#Get a list of all the pcap files not open on the pcap capture folder
openfilescommand = "lsof +D " + basedir + "packetStream/pcaps/outputs/ 2>/dev/null | grep .pcap | grep \/ | awk '{print $9}'"
findopenfiles = subprocess.Popen(openfilescommand, stdout=subprocess.PIPE, shell=True)
openfiles = findopenfiles.communicate()[0]

#Separate the output of the last command into an array for each line
openfileslist = openfiles.splitlines()

#Get a list of all the available pcap files in the capture folder
allfilescommand = "ls -lah " + basedir + "packetStream/pcaps/outputs/ 2>/dev/null | grep .pcap | awk '{print $9}'"
findallfiles = subprocess.Popen(allfilescommand, stdout=subprocess.PIPE, shell=True)
allfiles = findallfiles.communicate()[0]
allfileslist = allfiles.splitlines()

#Convert binary-encoded values to ascii values on open files list
positions = len(openfileslist)
for position in range(positions):
    openfileslist[position] = openfileslist[position].decode("ascii")

#For every file in the files list folder, if not open, move it into the new folder for the next script
for movablefile in allfileslist:
    movablefile = basedir + "packetStream/pcaps/outputs/" + movablefile.decode("ascii")
    print(movablefile)
    print(openfileslist)
    if movablefile not in openfileslist:
        movefilecommand = "mv " + movablefile + " " + basedir + "packetStream/csvs/inputs/"
        movefile = subprocess.Popen(movefilecommand, shell=True)
