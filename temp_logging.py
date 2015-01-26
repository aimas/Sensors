#!/usr/bin/env python
#jetzt mit logging in web

#define imports

import time
import logging
import logging.handlers
import argparse
import sys
import urllib2
import subprocess
from ISStreamer.Streamer import Streamer #you can get the libs and key from initialstate.com

DEBUG = 1

#define default information for logging
LOG_FILENAME = "/tmp/PI_temp_log.txt"
LOG_LEVEL = logging.INFO


#define and parse cmd line arguments
parser = argparse.ArgumentParser(description="Temp Checker Log")
parser.add_argument("-1", "--log", help="file write to (default '" + LOG_FILENAME + "')")

#if log file is specified in command line to override default
args = parser.parse_args()
if args.log:
       LOG_FILENAME = args.log


#configuration of the log file, creating a new one at midnight and keeping last 3
logger = logging.getLogger('templogger')
set log level
logger.setLevel(LOG_LEVEL)
#make a handler that writes file and make new one plus the backup
handler = logging.handlers.TimedRotatingFileHandler(LOG_FILENAME, when="midnight", backupCount=3)
#format each log file message like this
formatter=logging.Formatter('%(asctime)s %(levelname)-8s %(message)s')
#attach formatter to handler
handler.setFormatter(formatter)
#attach handler to logger
logger.addHandler(handler)

slogger = Streamer(bucket="whatever you name it", client_key="key generated at initialstate.com")

#make a class to capture stdout and sterr in the log
class MyLogger(object):
       def __init__(self, logger, level):
               self.logger = logger
               self.level = level
       def write(self, message):
               if message.rstrip() !="":
                       self.logger.log(self.level, message.rstrip())
                       self.logger.log(self.level, message)
#replace stdout with logging to file
sys.stdout = MyLogger(logger, logging.INFO)
#replace sterr with logging
sys.stderr = MyLogger(logger, logging.ERROR)
CHECK_FREQ = 60 #check temp every 60 secs


while True:



        if DEBUG:

                string1 = 'PIs internal temperature is: '
                string2 = float(open('/sys/class/thermal/thermal_zone0/temp').read())
                string2 = string2/1000
                string2 = str(string2)
                string3 = ' Degrees Celsius'
                endstring = string1 +  string2 + string3
               logger.info(endstring)
                slogger.log("PI Internal Temp: ", string2)
                slogger.close()
                URLString = 'https://whatever it will be for you.hanatrial.ondemand.com/iotscenario/?action=addsensorvalue&sensorid=1&unit=Celsius&sensorvalue=' + string2 +'&sensorvaluemultiplier=1&sensorvaluecalibration=0'
#               print(URLString) (I use that for debugging purpose)
                urllib2.urlopen(URLString).read()



        else:

                break

        time.sleep(CHECK_FREQ)
