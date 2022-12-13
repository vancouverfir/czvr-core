#!/usr/bin/python
#Written by Austin Abbey for Vancouver FIR 


import configparser
import sys
import requests
from datetime import datetime, timedelta
#import pyodbc
#import pandas as pd
import os
import mariadb

#Get current mode to run as
#Valid modes consist of "event" or "roster"
isMode = str(sys.argv[1]).lower()
print(isMode)

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

#connectSQL = ("Driver={SQL Server}"+";Server={};Database={};Port={};UID={};Pwd={}".format(DBServer,DBName,DBPort,DBUser,DBPass)) old version relying on pyODBC



try:
    connectSQL = mariadb.connect(user=DBUser,password=DBPass,host=DBServer,port=int(DBPort),database=DBName,autocommit=True)
except mariadb.Error as e:
    print(f"Error connecting to MariaDB Platform: {e}")
    sys.exit(1)


#pyodbc.connect(connectSQL) old version relying on pyODBC






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
    print("Trimming events")
    #trimming the DB of any old events. Date/Time format is YYYY-MM-DD HH:MM:SS
    
    for i in data["data"]:
        print("End time ",i["end"])
        #End time ex. is YYYY-MM-DD HH:MM:SS
        #Example: 2021-04-04 01:00:00
        
        past = datetime.strptime(str(i["end"])[:16], "%Y-%m-%d %H:%M")

        #past = datetime.strftime(i["end"], "%Y-%m-%d")
        #print("past time: ",past)
        
        #get current time in YYYY-MM-DD format
        present = datetime.now()
        present = present.strftime("%Y-%m-%d %H:%M")
        present = datetime.strptime(present, "%Y-%m-%d %H:%M")
        
        slug = magicSlug(i["start"],i["name"])
        
        
#        present = datetime.strftime(str(datetime.now()), "%Y-%m-%d")
        #print("present time: ",present)
        if past.date() > present.date():
            print("Event is within period")
            
            arrival=magicString(i["airports"]["arrival"])
            departure=magicString(i["airports"]["departure"])
            
            #print("Departure: ",departure)
            #print("Arrival: ",arrival)
            
            stowEvent(i["id"],i["name"],str(i["start"])[:16],str(i["end"])[:16],i["description"],i["image_url"],departure,arrival,slug) #the keys for ID, name, start, end, description,imageurl,airports,dept, and arrivals
        else:
            print("Event is outside of period, ignoring...")


def magicSlug(date,name): #I'm a slug
    storDate = str(date)[:16]
    storName = name.replace(" ", "-") #convert spaces to dashes
    storName=storName[:30]
    return storName+"-"+storDate #it just returns title (with a 30 character limit) plus - plus date

def magicString(stron): #IT SPOOLS FOR MILES AND MILES
    if isinstance(stron, str):
        #it unwounds!
        return stron
    elif isinstance(stron, list):
        spool = ", "
        return(spool.join(stron)) # it just makes it easier to submit multiple 
    else:
        print("do not make a mockery of this string. why did you pass something not a string or a list?")
        return("YXE") #My hometown, people have to go SOMEWHERE.
    

def stowEvent(ID,NAME,START_TIMESTAMP,END_TIMESTAMP,DESCRIPTION,IMAGE_URL,DEPARTURE_ICAO,ARRIVAL_ICAO, SLUG): #Uploading events to the DB
    #stowing the fetched events in the DB
    print("Stowing Events in DB...")
    cur = connectSQL.cursor()
    try: #writing the DB update
        print("selecting ID's to update")
        cur.execute("SELECT id FROM events WHERE id=?",(ID,))
        print("Captain, we found something!")
        try: #attempting to update the DB
            cur.execute("UPDATE events SET name = ?, start_timestamp = ?, end_timestamp = ?, description = ?, image_url = ?, departure_icao = ?, arrival_icao = ?, slug = ? WHERE id = ?",(NAME,START_TIMESTAMP,END_TIMESTAMP,DESCRIPTION,IMAGE_URL,DEPARTURE_ICAO,ARRIVAL_ICAO,SLUG, ID,))
            print("Execute complete!")
        except mariadb.Error as a:
            print(f" Iterative Error: {a}")
            print("He's dead, Jim...")
            sys.exit(1)
    except mariadb.Error as e: #if anything fails, we can still just re-add everything.
        print(f"Update Error: {e}") 
        sys.exit(1)
    
    print(" On the off chance we get this far...")
    print(SLUG)
    
    print(type(ID))
    print("id = ", ID)
    
    try:
        print(ID)
        print(NAME)
        print(START_TIMESTAMP)
        print(END_TIMESTAMP)
        print(DESCRIPTION)
        print(IMAGE_URL)
        print(DEPARTURE_ICAO)
        print(ARRIVAL_ICAO)
        print(SLUG)
        cur.execute("INSERT INTO events (id, name, Start_timestamp, end_timestamp, description, image_url, departure_icao, arrival_icao, slug) VALUES (?,?,?,?,?,?,?,?,?)",(ID,NAME,START_TIMESTAMP,END_TIMESTAMP,DESCRIPTION,IMAGE_URL,DEPARTURE_ICAO,ARRIVAL_ICAO,SLUG,))
    except mariadb.Error as e:
        print(f"Error: {e}")
    print("complete!")
    #cur.execute("VALUES ('?','?','?','?','?','?','?','?')",(ID,NAME,START_TIMESTAMP,END_TIMESTAMP,DESCRIPTION,IMAGE_URL,DEPARTURE_ICAO,ARRIVAL_ICAO,))

    #connectSQL.commit() MariaDB enables auto-commit, so this is no longer necessary
    rmEvent() #Calls cleanup

#####    
#####I don't know why I need this code here, but if I remove it, everything breaks. Leave as is.    
#####    

def rmEvent(): #Removing stale events from the server
    print("Removing old events")
    date = datetime.today() - timedelta(days=1) # Today - 1 day
    
    date = date.strftime("%Y-%m-%d %H:%M")
    cur = connectSQL.cursor()
    
    date = datetime.strptime(date, "%Y-%m-%d %H:%M")
    
    #cur.execute("DELETE * FROM events WHERE end_timestamp <= ?",(date,))
    cur.execute("DELETE FROM events WHERE end_timestamp < DATE_ADD(CURDATE(), interval -1 day)")
    #connectSQL.commit() MariaDB uses autocommit, so this is no longer necessary
    
     #close connection with SQL server

    
    

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
except Exception as e:
    print(f"Something went wrong: {e}")
    exit()
    

connectSQL.close()