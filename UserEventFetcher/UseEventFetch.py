#!/usr/bin/python
# Written by Austin Abbey for Vancouver FIR

from datetime import datetime, timedelta
from slugify import slugify
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
simKey = config.get("VatcanAPI", "APIkey")
# This is for getting the request URL for the API, in case of future changes
APIEvent = config.get("VatcanAPI", "Events")
# This is for getting the request URL for the API, in case of future changes
APIUsers = config.get("VatcanAPI", "Users")

# Establish connection to SQL server

DSN = "SQL Server Native Client 11.0"
DBUser = config.get("ServerDB", "ID")
DBServer = config.get("ServerDB", "Address")
DBPort = config.get("ServerDB", "Port")
DBPass = config.get("ServerDB", "Password")
DBName = config.get("ServerDB", "DBName")

print("|" + DBName + "|")
print("|" + DBUser + "|")
print("|" + DBServer + "|")

print("Connecting to MySQL Sevrer...")

CIDSTOR = []
# VISITCIDSTOR = []

WEBHOOK_URL = config.get("Webhook", "Webhook")

def send_webhook(message):
    #Send notifications via Webhook
    payload = {
        "content": f"**Script Error**\n{message}"
    }
    try:
        response = requests.post(WEBHOOK_URL, json=payload)
        response.raise_for_status()
        print("Webhook error sent")
    except requests.exceptions.RequestException as request_exception:
        print("Webhook error failed to send ", request_exception)

try:
    connectSQL = mariadb.connect(
        user=DBUser,
        password=DBPass,
        host=DBServer,
        port=int(DBPort),
        database=DBName,
        autocommit=True,
    )
except mariadb.Error as e:
    print(f"Error connecting to MariaDB Platform: {e}")
    send_webhook(f"Python script failed to connect to MariaDB Platform {e}")
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

    rm_deleted_events(data)

    for i in data["data"]:
        print("End time ", i["end"])
        # End time ex. is YYYY-MM-DD HH:MM:SS
        # Example: 2021-04-04 01:00:00

        event = datetime.strptime(str(i["end"])[:16], "%Y-%m-%d %H:%M")

        # get current time in YYYY-MM-DD format
        present = datetime.utcnow()
        present = present.strftime("%Y-%m-%d %H:%M")
        present = datetime.strptime(present, "%Y-%m-%d %H:%M")

        slug = slugify(
            str(i["start"]) + "-" + str(i["name"])
        )  # nicely formatting our datetime string

        if event.date() > present.date() or event.date() == present.date() and event.time() > present.time():
                print("Event is within period")

                arrival = magic_string(i["airports"]["arrival"])
                departure = magic_string(i["airports"]["departure"])

                # the keys for ID, name, start, end, description,imageurl,airports,dept, and arrivals
                data = {
                    "data": [
                        {
                            "id": i["id"],
                            "name": i["name"],
                            "start_timestamp": str(i["start"])[:16],
                            "end_timestamp": str(i["end"])[:16],
                            "description": i["description"],
                            "image_url": i["image_url"],
                            "departure_icao": departure,
                            "arrival_icao": arrival,
                            "slug": slug
                        }
                    ]
                }

                asyncio.run(stow_event(data))
        else:
            print("Event is outside of period, ignoring...")


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


async def stow_event(data):
    """
    Uploading events to the DB
    """

    # stowing the fetched events in the DB
    print("Stowing Events in DB...")
    cur = connectSQL.cursor()

    # Extract all event IDs from the incoming data
    event_ids = [event["id"] for event in data["data"]]

    try:
        # Batch fetch existing event IDs from the database
        print("Selecting event IDs to check for updates...")
        cur.execute("SELECT id FROM events WHERE id IN (%s)" % ','.join(['?'] * len(event_ids)), event_ids)
        existing_event_ids = set(row[0] for row in cur.fetchall())
        print("Existing event IDs fetched.")
    except mariadb.Error as db_error:
        print(f"Error fetching existing event IDs: {db_error}")
        send_webhook(f"Error fetching existing event IDs: {db_error}")
        sys.exit(1)

    # Loop through the events in the data payload
    for event in data["data"]:
        id = event["id"]
        name = event["name"]
        start_timestamp = event["start_timestamp"]
        end_timestamp = event["end_timestamp"]
        description = event["description"]
        image_url = event["image_url"]
        departure_icao = event["departure_icao"]
        arrival_icao = event["arrival_icao"]
        slug = event["slug"]

        # Check if the event already exists using the cached set
        if id in existing_event_ids:
            print(f"Event {id} exists, updating...")
            try:
                cur.execute(
                    "UPDATE events SET name = ?, start_timestamp = ?, end_timestamp = ?, description = ?, "
                    "image_url = ?, departure_icao = ?, arrival_icao = ?, slug = ? WHERE id = ?",
                    (
                        name,
                        start_timestamp,
                        end_timestamp,
                        description,
                        image_url,
                        departure_icao,
                        arrival_icao,
                        slug,
                        id,
                    ),
                )
                print(f"Update complete for event {id}!")
            except mariadb.Error as db_error:
                print(f"Iterative Error while updating event {id}: {db_error}")
                send_webhook(f"Iterative Error while updating event {id}: {db_error}")
                sys.exit(1)
        else:
            print(f"Event {id} not found, inserting...")
            try:
                cur.execute(
                    "INSERT INTO events (id, name, start_timestamp, end_timestamp, description, image_url, "
                    "departure_icao, arrival_icao, slug) VALUES (?,?,?,?,?,?,?,?,?)",
                    (
                        id,
                        name,
                        start_timestamp,
                        end_timestamp,
                        description,
                        image_url,
                        departure_icao,
                        arrival_icao,
                        slug,
                    ),
                )
                print(f"Insert complete for event {id}!")
            except mariadb.Error as db_error:
                print(f"Error while inserting event {id}: {db_error}")
                send_webhook(f"Error while inserting event {id}: {db_error}")
                sys.exit(1)

    # Commit changes to the database
    connectSQL.commit()
    print("Event stowing complete!")

def rm_deleted_events(data):
    """
    Removing deleted events from the server
    """
    print("Removing deleted events")
    cur = connectSQL.cursor()

    cur.execute("SELECT id FROM events")

    events = cur.fetchall()

    for id in events:
        if id not in data:
            cur.execute("DELETE FROM events WHERE id = (?)", id)


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
        asyncio.run(
            stow_roster(
                i["cid"],
                i["first_name"],
                i["last_name"],
                i["rating"],
                i["email"],
                fullname,
                rating_short,
            )
        )
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
        asyncio.run(
            stow_visit_roster(
                i["cid"],
                i["first_name"],
                i["last_name"],
                i["rating"],
                i["email"],
                fullname,
                rating_short,
            )
        )
        CIDSTOR.append(i["cid"])

    trim_roster()


def conv_rating(rating):
    """Converts the rating to match the rating system

    Args:
        rating (int): the input integer rating

    Returns:
        Str: the output rating string
    """
    if rating == 1:
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
        # sys.exit()


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
            bye.execute("DELETE FROM session_logs WHERE roster_member_id=?", (db_cid,))
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

    print("Fetching permissions for roster...")
    cur.execute("SELECT id, permissions FROM users WHERE id IN (?)", (cid,))
    permissions_map = {row[0]: row[1] for row in cur.fetchall()}
    print("Permissions fetched for roster.")

    if cid in permissions_map:
        permissions = permissions_map[cid]
        print(f"Permissions for {cid}: {permissions}")
        if permissions > 0:
            try:
                sto.execute(
                    "UPDATE users SET email=?, lname = ?, rating_id = ?, rating_short= ?, visitor = ? WHERE id = ?",
                    (email, lname, rating_id, rating_short, "0", cid),
                )
                sto.execute(
                    "UPDATE roster SET full_name = ?, visit = ?  WHERE user_id = ?",
                    (fullname, "0", cid),
                )
                sto.execute("SELECT status FROM roster WHERE cid = ?", (cid,))
                status = sto.fetchone()
                if status is None or status[0] == "visit":
                    sto.execute(
                        "UPDATE roster SET status = 'home' WHERE cid = ?", (cid,)
                    )
            except mariadb.Error as db_error:
                print("Iterative Error:", db_error)
                send_webhook(f"Iterative Error {db_error}")
                sys.exit(1)
        else:
            try:
                sto.execute(
                    "UPDATE roster SET full_name = ?, visit = ?  WHERE user_id = ?",
                    (fullname, "0", cid),
                )
                sto.execute(
                    "UPDATE users SET email=?, lname = ?, rating_id = ?, rating_short= ?, permissions = ?, visitor = ? WHERE id = ?",
                    (email, lname, rating_id, rating_short, "1", "0", cid),
                )
            except mariadb.Error as db_error:
                print("Iterative Error:", db_error)
                send_webhook(f"Iterative Error {db_error}")
                sys.exit(1)
    else:
        print("Not in DB Moving on...")

    try:
        print("Updating users role...")
        cur.execute("SELECT permissions FROM users WHERE id=?", (cid,))
        perm = cur.fetchone()
        if perm is not None:
            print(f"permissions for {cid}={perm}")
            if perm[0] <= 1:
                print(f"{cid}: Guest or Controller, moving on")
            elif perm[0] == 2:
                print(f"{cid}: Mentor")
                m = "mentor"
                cur.execute(
                    "UPDATE roster SET staff = ? WHERE cid = ?", (m, cid)
                )
            elif perm[0] == 3:
                print(f"{cid}: Instructor")
                ins = "ins"
                cur.execute(
                    "UPDATE roster SET staff = ? WHERE cid = ?", (ins, cid)
                )
            elif perm[0] == 4:
                print(f"{cid}: Staff")
                s = "staff"
                cur.execute(
                    "UPDATE roster SET staff = ? WHERE cid = ?", (s, cid)
                )
            elif perm[0] == 5:
                print(f"{cid}: Executive")
                e = "exec"
                cur.execute(
                    "UPDATE roster SET staff = ? WHERE cid = ?", (e, cid)
                )
            print("complete!")
        else:
            print("Not in DB Moving on...")
    except mariadb.Error as db_error:
        print(" Iterative Error: ", db_error)
        send_webhook(f"Iterative Error {db_error}")
        sys.exit(1)

    try:
        cur.execute(
            "INSERT INTO users (id, email, fname, lname, rating_id, Rating_short, permissions, display_fname) VALUES (?,?,?,?,?,?,?,?)",
            (cid, email, fname, lname, rating_id, rating_short, "1", fname),
        )
    except mariadb.Error as db_error:
        print("Error: ", db_error)
    try:
        print("Now adding to Roster")
        cur.execute(
            "INSERT INTO roster (cid, user_id, full_name, status, active, visit) VALUES (?,?,?,?,?,?)",
            (cid, cid, fullname, "home", "1", "0"),
        )
    except mariadb.Error as db_error:
        print("Error: ", db_error)
    print("complete!")


async def stow_visit_roster(cid, fname, lname, rating_id, email, fullname, rating_short):
    """
    stores new users in the visit roster
    """
    print("Stowing visitors in DB...")
    cur = connectSQL.cursor()
    sto = connectSQL.cursor()

    print("Fetching permissions for visit roster...")
    cur.execute("SELECT id, permissions FROM users WHERE id IN (?)", (cid,))
    permissions_map = {row[0]: row[1] for row in cur.fetchall()}
    print("Permissions fetched for visit roster.")

    if cid in permissions_map:
        permissions = permissions_map[cid]
        print(f"Permissions for {cid}: {permissions}")
        if permissions > 0:
            try:
                sto.execute(
                    "UPDATE users SET email=?, lname = ?, rating_id = ?, rating_short= ?, visitor = ? WHERE id = ?",
                    (email, lname, rating_id, rating_short, "1", cid),
                )
                sto.execute(
                    "UPDATE roster SET full_name = ?, visit = ?, status = ? WHERE user_id = ?",
                    (fullname, "1", "visit", cid),
                )
            except mariadb.Error as db_error:
                print("Iterative Error:", db_error)
                send_webhook(f"Iterative Error {db_error}")
                sys.exit(1)
        else:
            try:
                sto.execute(
                    "UPDATE roster SET full_name = ?, visit = ?, status = ? WHERE user_id = ?",
                    (fullname, "1", "visit", cid),
                )
                sto.execute(
                    "UPDATE users SET email=?, lname = ?, rating_id = ?, rating_short= ?, permissions = ?, visitor = ? WHERE id = ?",
                    (email, lname, rating_id, rating_short, "1", "1", cid),
                )
            except mariadb.Error as db_error:
                print("Iterative Error:", db_error)
                send_webhook(f"Iterative Error {db_error}")
                sys.exit(1)
    else:
        print("Not in DB Moving on...")

    try:
        cur.execute(
            "INSERT INTO users (id, email, fname, lname, rating_id, Rating_short, permissions, display_fname, visitor) VALUES (?,?,?,?,?,?,?,?,?)",
            (cid, email, fname, lname, rating_id, rating_short, "1", fname, "1"),
        )
    except mariadb.Error as db_error:
        print("Error: ", db_error)
    try:
        print("Now adding to Visitor Roster")
        cur.execute(
            "INSERT INTO roster (cid, user_id, full_name, status, active, visit) VALUES (?,?,?,?,?,?)",
            (cid, cid, fullname, "visit", "1", "1"),
        )
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
    elif (
        ISMODE == "roster"
        or ISMODE == "rosters"
        or ISMODE == "users"
        or ISMODE == "user"
    ):
        fetch_roster()
        fetch_visit_roster()
    elif ISMODE == "activityreset":
        reset_activity()
    else:
        print("You need to state either Events or Roster")
except Exception as e:
    print("Something went wrong: ", e)
    error_message = str(e)
    send_webhook(error_message)
    sys.exit()


connectSQL.close()
