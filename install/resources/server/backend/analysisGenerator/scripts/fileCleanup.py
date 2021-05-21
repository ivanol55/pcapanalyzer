#!/usr/bin/env python
#Import all the necessary modules
import sys
import os
import configparser

#Parse the config file for the credentials and configuration settings needed
config = configparser.ConfigParser()
config.read("../../backend/.my.cnf")
basedir = config["paths"]["basedir"]
dbDatabase = "analysis_" + sys.argv[1]
pcapdir = basedir + "analysisGenerator/files/pcaps/" + dbDatabase + "/"
csvdir = basedir + "analysisGenerator/files/csvs/" + dbDatabase + "/"

#Cleanup
command = "rm -rf " + csvdir
print(command)
os.system(command)
command = "rm -rf " + pcapdir
print(command)
os.system(command)
