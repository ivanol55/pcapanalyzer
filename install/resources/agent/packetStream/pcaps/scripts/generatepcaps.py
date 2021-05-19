#IMport all of the necessary system libraries
import os
import configparser

#Read and parse the agent configuration file
config = configparser.ConfigParser()
config.read("../../../.my.cnf")

#Set all of the necessary variables from the agent configuration file
basedir = config["paths"]["basedir"]
interface = config["interfaces"]["interface"]

#Start listening for data on the current agent host system
os.system("tshark -i " + interface + " -w " + basedir + "packetStream/pcaps/outputs/file.pcap -b files:2 -b filesize:102400 -q &")
