#Import all of the necessary system libraries
import subprocess
import time
import os
import configparser

#Read and parse the server configuration file
config = configparser.ConfigParser()
config.read("../.my.cnf")

#Set all of the needed values from the configuration file
basedir = str(config["paths"]["basedir"])

#change the directory to the one with all of the scripts to run
os.chdir("probes")
while True:
    #Execute every script in the probes folder
    path = basedir + "intrusionDetection/probes/"
    scriptsList = os.listdir(path)
    for script in scriptsList:
        command = ["python3", basedir + "intrusionDetection/probes/" + script]
        runscript = subprocess.Popen(command)
    #Sleep for 30 minutes until probing again
    time.sleep(1800)
