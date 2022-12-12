#!/usr/bin/python
#Written by Austin Abbey for Vancouver FIR 


import configparser
import sys
import requests
from datetime import datetime, timedelta
import pyodbc
import pandas as pd
import os

#Get current mode to run as
#Valid modes consist of "event" or "roster"
isMode = str(sys.argv[1:]).lower()

#getting our config file
config = configparser.RawConfigParser()
assert os.path.exists('./pyapiconf.ini')
configFilePath = './pyapiconf.ini'
config.read(configFilePath)

simKey = config.get('VatcanAPI','APIkey') #this is our Vatsim API key, edit to match what is in use
APIEvent = config.get('VatcanAPI','Events') #This is for getting the request URL for the API, in case of future changes
APIUsers = config.get('VatcanAPI','Users') #This is for getting the request URL for the API, in case of future changes

#Establish connection to SQL server

dsn = "SQL Server Native Client 11.0"
DBUser= config.get('ServerDB','ID')
DBServer = config.get('ServerDB','Address')
DBPort = config.get('ServerDB','Port')
DBPass = config.get('ServerDB','Password')
DBName = config.get('ServerDB','DBName')

print("|"+DBName+"|")
print("|"+DBUser+"|")
print("|"+DBServer+"|")

print("Connecting to MySQL Sevrer...")

connectSQL = ("Driver={SQL Server}"+";Server={};Database={};Port={};UID={};Pwd={}".format(DBServer,DBName,DBPort,DBUser,DBPass))

pyodbc.connect(connectSQL)

def fetchEvent():
    
    #Using the vatsim API to fetch events
    
    print("Fetching Events...")
    try:
        
        req = requests.get(APIEvent+simKey)
    except:
        print("Event Fetch Failed!")
        exit()
    print("Fetched Events")
    resp = req.json() #take json output formatted as a dict
    trimEvents(resp)

def trimEvents(data):
    
    #trimming the DB of any old events. Date/Time format is YYYY-MM-DD HH:MM:SS
    
    for i in data:
        past = datetime.strptime(str(i["end"])[:10], "%Y-%m-%d")
        present = datetime.now()
        if past.date() < present.date() == False:
            print("Event is within period")
            stowEvent(i["name"],str(i["start"])[:10],str(i["end"])[:10],i["description"],i["image_url"],i["airports"]["departure"],i["airports"]["arrival"])
        else:
            print("Event is outside of period, ignoring...")
            


def stowEvent(NAME,START_TIMESTAMP,END_TIMESTAMP,DESCRIPTION,IMAGE_URL,DEPARTURE_ICAO,ARRIVAL_ICAO): #Uploading events to the DB
    #stowing the fetched events in the DB
    print("Stowing Events in DB...")
    cursor = connectSQL.cursor()
    
    cursor.execute("INSERT INTO Events (name, Start_timestamp, end_timestamp, description, image_url, departure_icao, arrival_icao")
    cursor.execute("VALUES ('{}','{}','{}','{}','{}','{}','{}');".format((NAME,START_TIMESTAMP,END_TIMESTAMP,DESCRIPTION,IMAGE_URL,DEPARTURE_ICAO,ARRIVAL_ICAO)))
    connectSQL.commit()
    rmEvent() #Calls cleanup
    
    
    
def rmEvent(): #Removing stale events from the server
    print("Removing old events")
    date = datetime.today() - timedelta(days=1) # Today - 1 day
    
    date = date.strftime("%Y-%m-%d")
    cursor = connectSQL.cursor()
    
    cursor.execute("SELECT * FROM events WHERE end_timestamp <='{}'".format(date))
    connectSQL.commit()
    
    del connectSQL #close connection with SQL server

    
    

def fetchRoster():
    #Using the vatsium API to fetch the user roster
    print("Fetching Users...")
    try:
        req = requests.get(APIUsers+simKey)
    except:
        print("User Fetch Failed!")
        exit()
    print("Fetched Events")
    resp = req.json()
    trimRoster(resp)
    

def convRating(rating):
    if rating == 0:
        print("Not a valid Rating!")
    elif rating == 1:
        return "OBS"
    elif rating == 2:
        return "S1"
    elif rating == 3:
        return "s2"
    elif rating == 4:
        return "S3"
    elif rating == 5:
        return "C1"
    elif rating == 6:
        return "C2"
    elif rating == 7:
        return "C3"
    elif rating == 8:
        return "I1"
    elif rating == 9:
        return "I2"
    elif rating == 10:
        return "I3"
    elif rating == 11:
        return "SUP"
    elif rating == 12:
        return "ADM"
    else:
        print("not a valid rating!")
        exit
   
def trimRoster(data):
    #Removes users from the roster if they are removed from the vatcan roster
    
    #CDfor i in data:
        
    
    
    
    stowRoster(data)

 
def stowRoster(CID,FNAME,LNAME,RATING_ID,RATING_SHORT):
    #stores new users in the roster
    print("Stowing users in DB...")
    

try:
    if isMode == "events" or isMode == "event":
        fetchEvent()
    elif isMode == "roster" or isMode == "rosters" or isMode == "users" or isMode == "user":
        fetchRoster()
    else:
        print("You need to state either Events or Roster")
except:
    print("Something went wrong")
    exit()