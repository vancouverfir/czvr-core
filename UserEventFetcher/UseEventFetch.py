#!/usr/bin/python
# Written by Austin Abbey for Vancouver FIR

from datetime import datetime, timedelta
import configparser
import sys
import os
import asyncio
import requests
import mariadb


# Get current mode to run as
# Valid modes consist of "event" or "roster"
ISMODE = str(sys.argv[1]).lower()
print(ISMODE)

# getting our config file
config = configparser.RawConfigParser()
assert os.path.exists('./pyapiconf.ini')
CONFIGFILEPATH = './pyapiconf.ini'
config.read(CONFIGFILEPATH)

# this is our Vatsim API key, edit to match what is in use
simKey = config.get('VatcanAPI', 'APIkey')
# This is for getting the request URL for the API, in case of future changes
APIEvent = config.get('VatcanAPI', 'Events')
# This is for getting the request URL for the API, in case of future changes
APIUsers = config.get('VatcanAPI', 'Users')

# Establish connection to SQL server

DSN = "SQL Server Native Client 11.0"
DBUser = config.get('ServerDB', 'ID')
DBServer = config.get('ServerDB', 'Address')
DBPort = config.get('ServerDB', 'Port')
DBPass = config.get('ServerDB', 'Password')
DBName = config.get('ServerDB', 'DBName')

print("|" + DBName + "|")
print("|" + DBUser + "|")
print("|" + DBServer + "|")

print("Connecting to MySQL Sevrer...")

CIDSTOR = []
VISITCIDSTOR = []

try:
    connectSQL = mariadb.connect(user=DBUser, password=DBPass, host=DBServer, port=int(
        DBPort), database=DBName, autocommit=True)
except mariadb.Error as e:
    print(f"Error connecting to MariaDB Platform: {e}")
    sys.exit(1)


def fetch_event():
    """Using the vatsim API to fetch events"""

    print("Fetching Events...")
    try:

        req = requests.get(APIEvent + simKey, timeout=5)
    except requests.exceptions.RequestException as request_exception:
        print("Event Fetch Failed! ", request_exception)
        sys.exit()
    print("Fetched Events")
    resp = req.json()  # take json output formatted as a dict
    trim_events(resp)


def trim_events(data):
    """
    trimming the DB of any old events. Date/Time format is YYYY-MM-DD HH:MM:SS
    """
    print("Trimming events")

    for i in data["data"]:
        print("End time ", i["end"])
        # End time ex. is YYYY-MM-DD HH:MM:SS
        # Example: 2021-04-04 01:00:00

        past = datetime.strptime(str(i["end"])[:16], "%Y-%m-%d %H:%M")

        # get current time in YYYY-MM-DD format
        present = datetime.now()
        present = present.strftime("%Y-%m-%d %H:%M")
        present = datetime.strptime(present, "%Y-%m-%d %H:%M")

        slug = magic_slug(i["start"], i["name"])

        if past.date() > present.date():
            print("Event is within period")

            arrival = magic_string(i["airports"]["arrival"])
            departure = magic_string(i["airports"]["departure"])

            # the keys for ID, name, start, end, description,imageurl,airports,dept, and arrivals
            asyncio.run(stow_event(i["id"], i["name"], str(i["start"])[:16], str(i["end"])[
                :16], i["description"], i["image_url"],
                departure, arrival, slug))
        else:
            print("Event is outside of period, ignoring...")


def magic_slug(date, name):
    """
    I'm a slug
    But in all seriousness, it just formats the date and time as needed.
    """
    store_date = str(date)[:16]
    store_name = name.replace(" ", "-")  # convert spaces to dashes
    store_name = store_name[:30]
    # it just returns title (with a 30-character limit) plus - plus date
    return store_name + "-" + store_date


def magic_string(stron):
    """
    IT SPOOLS FOR MILES AND MILES
    But in all seriousness, it just formats any lists of ICAO's into a single string
    """
    if isinstance(stron, str):
        # it unwounds!
        return stron
    elif isinstance(stron, list):
        spool = ", "
        # it just makes it easier to submit multiple
        return spool.join(stron)
    else:
        return "YXE"  # My hometown, people have to go SOMEWHERE.


async def stow_event(
    id, name, start_timestamp, end_timestamp, description, image_url, departure_icao, arrival_icao, slug
):
    """
    Uploading events to the DB
    """

    # stowing the fetched events in the DB
    print("Stowing Events in DB...")
    cur = connectSQL.cursor()
    try:  # writing the DB update
        print("selecting ID's to update")
        cur.execute("SELECT id FROM events WHERE id=?", (id,))
        print("Captain, we found something!")
        try:  # attempting to update the DB
            cur.execute("UPDATE events SET name = ?, start_timestamp = ?, end_timestamp = ?, description = ?, "
                        "image_url = ?, departure_icao = ?, arrival_icao = ?, slug = ? WHERE id = ?",
                        (name, start_timestamp, end_timestamp, description, image_url, departure_icao, arrival_icao,
                         slug, id,))
            print("Execute complete!")
        except mariadb.Error as db_error:
            print(f" Iterative Error: {db_error}")
            print("He's dead, Jim...")
            sys.exit(1)
    # if anything fails, we can still just re-add everything.
    except mariadb.Error as db_error:
        print(f"Update Error: {db_error}")
        sys.exit(1)

    print(" On the off chance we get this far...")
    print(slug)

    print(type(id))
    print("id = ", id)

    try:
        print(id)
        print(name)
        print(start_timestamp)
        print(end_timestamp)
        print(description)
        print(image_url)
        print(departure_icao)
        print(arrival_icao)
        print(slug)
        cur.execute("INSERT INTO events (id, name, Start_timestamp, end_timestamp, description, image_url, "
                    "departure_icao, arrival_icao, slug) VALUES (?,?,?,?,?,?,?,?,?)",
                    (id, name, start_timestamp, end_timestamp, description, image_url, departure_icao, arrival_icao,
                     slug,))
    except mariadb.Error as db_error:
        print(f"Error: {db_error}")
    print("complete!")



def rm_event():
    """
    Removing stale events from the server
    """
    print("Removing old events")
    date = datetime.today() - timedelta(days=1)  # Today - 1 day

    date = date.strftime("%Y-%m-%d %H:%M")
    cur = connectSQL.cursor()

    date = datetime.strptime(date, "%Y-%m-%d %H:%M")

    cur.execute(
        "DELETE FROM events WHERE end_timestamp < DATE_ADD(CURDATE(), interval -1 day)")


def fetch_roster():
    """
    # Using the vatsium API to fetch the user roster
    """

    print("Fetching Users...")
    try:
        req = requests.get(APIUsers + simKey, timeout=5)
    except requests.exceptions.RequestException as request_exception:
        print("User Fetch Failed!", request_exception)
        exit()
    resp = req.json()
    for i in resp["data"]["controllers"]:
        print("CID =", i["cid"])
        rating_short = conv_rating(i["rating"])
        fullname = i["first_name"] + " " + i["last_name"]
        print("Users Full Name:", fullname)
        asyncio.run(stow_roster(i["cid"], i["first_name"], i["last_name"],
                                i["rating"], i["email"], fullname, rating_short))
        CIDSTOR.append(i["cid"])


def fetch_visit_roster():
    """
    Fetches the visitor roster
    """
    # Using the vatsium API to fetch the user roster

    print("Fetching Visitors...")
    try:
        req = requests.get(APIUsers + simKey, timeout=5)
    except requests.exceptions.RequestException as request_exception:
        print("Visitor Fetch Failed!", request_exception)
        exit()
    resp = req.json()
    for i in resp["data"]["visitors"]:
        print("CID =", i["cid"])
        rating_short = conv_rating(i["rating"])
        fullname = i["first_name"] + " " + i["last_name"]
        print("Users Full Name:", fullname)
        asyncio.run(stow_visit_roster(
            i["cid"], i["first_name"], i["last_name"], i["rating"], i["email"], fullname, rating_short))
        CIDSTOR.append(i["cid"])

    trim_roster()


def conv_rating(rating):
    """Converts the rating to match the rating system

    Args:
        rating (int): the input integer rating

    Returns:
        Str: the output rating string
    """
    if rating == 0:
        print("Not a valid Rating!")
    elif rating == 1:
        return "OBS"
    elif rating == 2:
        return "S1"
    elif rating == 3:
        return "S2"
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
        sys.exit()


def trim_roster():
    """
    Cleans up the roster and returns a list of players
    """
    print("Cleaning up roster")
    cur = connectSQL.cursor()
    bye = connectSQL.cursor()
    cur.execute("SELECT cid FROM roster")
    for i in cur:
        str_cid = str(i)[1:-2]
        db_cid = int(str_cid)
        if db_cid not in CIDSTOR:
            print("invalid CID:", db_cid)
            bye.execute("DELETE FROM roster WHERE cid=?", (db_cid,))
            bye.execute(
                "UPDATE users SET permissions = ? WHERE id = ?", ("0", db_cid))
            print("Invalid CID Removed from DB... BUH BYE")


async def stow_roster(cid, fname, lname, rating_id, email, fullname, rating_short):
    """
    stores new users in the roster
    """
    print("Stowing users in DB...")
    cur = connectSQL.cursor()
    sto = connectSQL.cursor()
    try:
        print("Searching for ID's to update...")
        cur.execute("SELECT id FROM users WHERE id=?", (cid,))
        
        try:
            cur.execute("SELECT permissions FROM users WHERE id=?", (cid,))
            permissions = cur.fetchone()
            
            if permissions is not None:
                print(f"perms {permissions[0]}")
                if permissions[0] > 0:
                    sto.execute(
                        "UPDATE users SET email=?, lname = ?, rating_id = ?, rating_short= ?, visitor = ? WHERE id = ?",
                        (email, lname, rating_id, rating_short, "0", cid))
                    sto.execute(
                        "UPDATE roster SET full_name = ?, visit = ?  WHERE user_id = ?", (fullname, "0", cid))
                    sto.execute("SELECT status FROM roster WHERE cid = ?", (cid,))
                    status = sto.fetchone()
                    if status[0] == "visit":
                        sto.execute(
                            f"UPDATE roster SET status = 'home' WHERE cid = {cid}")
                else:
                    sto.execute(
                        "UPDATE roster SET full_name = ?, visit = ?  WHERE user_id = ?", (fullname, "0", cid))
                    sto.execute(
                        "UPDATE users SET email=?, lname = ?, rating_id = ?, rating_short= ?, permissions = ?, visitor = "
                        "? WHERE id = ?",
                        (email, lname, rating_id, rating_short, "1", "0", cid))
            else:
                print("Not in DB Moving on...")
        except mariadb.Error as db_error:
            print(" Iterative Error:", db_error)
            print("He's dead, Jim...")
            sys.exit(1)
    except mariadb.Error as db_error:
        print("Update Error:", db_error)
        sys.exit(1)

    try:
        print("Updating users role...")
        cur.execute(f"SELECT permissions FROM users WHERE id={cid}")
        #perm = cur.fetchall()[0][0]
        perm = cur.fetchone()
        if perm is not None:
            print(f"permissions for {cid}={perm}")
            if perm[0] <= 1:
                print(f"{cid}: Guest or Controller, moving on")
            elif perm[0] == 2:
                print(f"{cid}: Mentor")
                m = "mentor"
                cur.execute(f"UPDATE roster SET staff = '{m}' WHERE cid = {cid}")
            elif perm[0] == 3:
                print(f"{cid}: Instructor")
                ins = "ins"
                cur.execute(f"UPDATE roster SET staff = '{ins}' WHERE cid = {cid}")
            elif perm[0] == 4:
                print(f"{cid}: Staff")
                s = "staff"
                cur.execute(f"UPDATE roster SET staff = '{s}' WHERE cid = {cid}")
            elif perm[0] == 5:
                print(f"{cid}: Executive")
                e = "exec"
                cur.execute(f"UPDATE roster SET staff = '{e}' WHERE cid = {cid}")
        else:
            print("Not in DB Moving on...")
    except mariadb.Error as db_error:
        print(" Iterative Error: ", db_error)
        sys.exit(1)

    try:
        cur.execute(
            "INSERT INTO users (id, email, fname, lname, rating_id, Rating_short, permissions, display_fname) VALUES "
            "(?,?,?,?,?,?,?,?)",
            (cid, email, fname, lname, rating_id, rating_short, "1", fname))
    except mariadb.Error as db_error:
        print("Error: ", db_error)
    try:
        print("Now adding to Roster")
        cur.execute("INSERT INTO roster (cid, user_id, full_name, status, active, visit) VALUES (?,?,?,?,?,?)",
                    (cid, cid, fullname, "home", "1", "0"))
    except mariadb.Error as db_error:
        print("Error: ", db_error)
    print("complete!")


async def stow_visit_roster(cid, fname, lname, rating_id, email, fullname, rating_short):
    """
    stores new users in the roster
    """
    print("Stowing visitors in DB...")
    cur = connectSQL.cursor()
    sto = connectSQL.cursor()
    try:
        print("Searching for Visitor ID's to update...")
        cur.execute("SELECT id FROM users WHERE id=?", (cid,))
        try:
            cur.execute("SELECT permissions FROM users WHERE id=?", (cid,))
            for i in cur:
                if i is not None:
                    print(f"Permissions for {cid}: {i[0]}")
                    if i[0] > 0:
                        sto.execute(
                            "UPDATE users SET email=?, lname = ?, rating_id = ?, rating_short= ?, visitor = ? WHERE id = ?",
                            (
                                email, lname, rating_id, rating_short, "1", cid))
                        sto.execute(
                            "UPDATE roster SET full_name = ?, visit = ?, status = ? WHERE user_id = ?",
                            (fullname, "1", "visit", cid))
                    else:
                        sto.execute(
                            "UPDATE roster SET full_name = ?, visit = ?, status = ? WHERE user_id = ?",
                            (fullname, "1", "visit", cid))
                        sto.execute(
                            "UPDATE users SET email=?, lname = ?, rating_id = ?, rating_short= ?, permissions = ?, "
                            "visitor = ? WHERE id = ?",
                            (
                                email, lname, rating_id, rating_short, "1", "1", cid))
                else:
                    print("not in visitor DB moving on...")
        except mariadb.Error as db_error:
            print(" Iterative Error: ", db_error)
            print("He's dead, Jim...")
            sys.exit(1)
    except mariadb.Error as db_error:
        print(f"Update Error: {db_error}")
        sys.exit(1)

    try:
        cur.execute(
            "INSERT INTO users (id, email, fname, lname, rating_id, Rating_short, permissions, display_fname, "
            "visitor) VALUES (?,?,?,?,?,?,?,?,?)",
            (cid, email, fname, lname, rating_id, rating_short, "1", fname, "1"))
    except mariadb.Error as db_error:
        print("Error: ", db_error)
    try:
        print("Now adding to Visitor Roster")
        cur.execute("INSERT INTO roster (cid, user_id, full_name, status, active, visit) VALUES (?,?,?,?,?,?)",
                    (cid, cid, fullname, "visit", "1", "1"))
    except mariadb.Error as db_error:
        print("Error: ", db_error)
    print("complete!")


def reset_activity():
    """
    Monthly Currency Reset
    """
    cur = connectSQL.cursor()
    cur.execute("UPDATE roster SET currency = NULL WHERE cid IS NOT NULL")
    print("Monthly Currency Reset")


try:
    if ISMODE == "events" or ISMODE == "event":
        fetch_event()
    elif ISMODE == "roster" or ISMODE == "rosters" or ISMODE == "users" or ISMODE == "user":
        fetch_roster()
        fetch_visit_roster()
    elif ISMODE == "activityreset":
        reset_activity()
    else:
        print("You need to state either Events or Roster")
except Exception as e:
    print("Something went wrong: ", e)
    sys.exit()


connectSQL.close()
