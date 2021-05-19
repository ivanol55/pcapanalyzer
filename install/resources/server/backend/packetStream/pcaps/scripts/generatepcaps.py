#Import all of the necessary libraries
import os
import configparser

#Read and parse the server configuration file
config = configparser.ConfigParser()
config.read("../../../.my.cnf")

#Set all of the necessary variables from the parsed configuration file
basedir = config["paths"]["basedir"]
interface = config["interfaces"]["interface"]

#Start capturing network data on the specified interface
os.system("tshark -i " + interface + " -w " + basedir + "packetStream/pcaps/outputs/file.pcap -b files:4 -b filesize:102400 -q &")
