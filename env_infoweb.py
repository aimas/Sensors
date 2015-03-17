#! /usr/bin/python
# -*- coding: utf-8 -*-


import subprocess
import os
#to simplify life I use the Adafruit library to read the sensor values
import Adafruit_BMP.BMP085 as BMP085
import sys
import MySQLdb as mdb

#function for storing reading into MySQL

def insertDB(sensorid, temp, atm_pressure, altitude, sea_level_pressure):
        try:
#connection string to your DB depends on the hosting place, I use it to write to the DB at my web hosting service
                con = mdb.connect(host='XXXXXX', port=XXX, user='XXX', passwd='XXX', db='XXXX');
                cursor = con.cursor()
#the SQL structure depends on your table layout
                sql = "INSERT INTO env_measures(`sensor_id`, `ext_temp`, `atm_press`, `altitude`, `sea_press`) \
                VALUES ('%s', '%s', '%s', '%s', '%s')" % \
                (sensorid, temp, atm_pressure, altitude, sea_level_pressure)
                cursor.execute(sql)
                con.commit()

                con.close()

        except mdb.ERROR, e:
                con.rollback()
                print "Error %d: %s" % (e.args[0], e.args[1])
                sys.exit(1)

sensor = BMP085.BMP085()

got_id = "Office"
got_temp = str(sensor.read_temperature())
got_atm = str(sensor.read_pressure()/100)
got_alt = str(sensor.read_altitude())
got_sea = str(sensor.read_sealevel_pressure()/100)

#I use the print function for debugging to ensure the sensor values are being read
#print 'temp = ' + got_temp

insertDB(got_id, got_temp, got_atm, got_alt, got_sea)
