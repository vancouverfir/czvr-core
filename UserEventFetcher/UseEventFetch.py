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
        print("Webhook error sent!")
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
    """Using the vatsim API to fetch Events"""

    print("Fetching Events!")
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
    Trimming the DB of any old Events! Date/Time format is YYYY-MM-DD HH:MM:SS
    """
    print("Trimming Events")

    rm_deleted_events(data)

    for i in data["data"]:
        print("End time ", i["end"])
        # End time ex. is YYYY-MM-DD HH:MM:SS
        # Example: 2021-01-01 01:11:11

        event = datetime.strptime(str(i["end"])[:16], "%Y-%m-%d %H:%M")

        # get current time in YYYY-MM-DD format
        present = datetime.utcnow()
        present = present.strftime("%Y-%m-%d %H:%M")
        present = datetime.strptime(present, "%Y-%m-%d %H:%M")

        slug = slugify(
            str(i["start"]) + "-" + str(i["name"])
        )  # nicely formatting our datetime string

        if event.date() > present.date() or event.date() == present.date() and event.time() > present.time():
            print("Event is within period!")

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
            print("Event is outside of period, ignoring!")


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
    print("Stowing Events in DB!")
    cur = connectSQL.cursor()

    # Extract all event IDs from the incoming data
    event_ids = [event["id"] for event in data["data"]]

    try:
        # Batch fetch existing event IDs from the database
        print("Selecting event IDs to check for updates!")
        cur.execute("SELECT id FROM events WHERE id IN (%s)" % ','.join(['?'] * len(event_ids)), event_ids)
        existing_event_ids = set(row[0] for row in cur.fetchall())
        print("Existing event IDs fetched.")
    except mariadb.Error as db_error:
        print(f"Error fetching existing event IDs! {db_error}")
        send_webhook(f"Error fetching existing event IDs! {db_error}")
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
            print(f"Event {id} exists, updating!")
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
            print(f"Event {id} not found, inserting!")
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

    print("Fetching Users!")
    try:
        req = requests.get(APIUsers + simKey, timeout=5)
    except requests.exceptions.RequestException as request_exception:
        print("User Fetch Failed!", request_exception)
        exit()
    resp = req.json()
    for i in resp["data"]["controllers"]:
        print("CID =", i["cid"])
        fullname = i["first_name"] + " " + i["last_name"]
        facility_join = i.get("facility_join")
        rating_short = conv_rating(i["rating"])
        print("Users Full Name:", fullname)
        asyncio.run(
            stow_roster(
                i["cid"],
                i["first_name"],
                i["last_name"],
                i["rating"],
                i["email"],
                fullname,
                facility_join,
                rating_short,
            )
        )
        CIDSTOR.append(i["cid"])


def fetch_visit_roster():
    """
    Fetches the visitor roster
    """
    # Using the vatsium API to fetch the user roster

    print("Fetching Visitors!")
    try:
        req = requests.get(APIUsers + simKey, timeout=5)
    except requests.exceptions.RequestException as request_exception:
        print("Visitor Fetch Failed!", request_exception)
        exit()
    resp = req.json()
    for i in resp["data"]["visitors"]:
        print("CID =", i["cid"])
        fullname = i["first_name"] + " " + i["last_name"]
        facility_join = i.get("facility_join")
        rating_short = conv_rating(i["rating"])
        print("Users Full Name:", fullname)
        asyncio.run(
            stow_visit_roster(
                i["cid"],
                i["first_name"],
                i["last_name"],
                i["rating"],
                i["email"],
                fullname,
                facility_join,
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
        str: the output rating string
    """
    rating_map = {
        1: "OBS",
        2: "S1",
        3: "S2",
        4: "S3",
        5: "C1",
        6: "C2",
        7: "C3",
        8: "I1",
        9: "I2",
        10: "I3",
        11: "SUP",
        12: "ADM"
    }

    try:
        return rating_map[rating]
    except KeyError:
        raise ValueError(f"Not a valid rating {rating}")

def trim_roster():
    """
    Cleans up the roster and returns a list of players
    """
    print("Cleaning up roster!")
    cur = connectSQL.cursor()
    bye = connectSQL.cursor()
    cur.execute("SELECT user_id FROM roster")
    for i in cur:
        str_cid = str(i)[1:-2]
        db_cid = int(str_cid)
        if db_cid not in CIDSTOR:
            print("invalid CID:", db_cid)
            bye.execute("DELETE FROM session_logs WHERE roster_member_id=?", (db_cid,))
            bye.execute("DELETE FROM roster WHERE user_id=?", (db_cid,))
            bye.execute(
                "UPDATE users SET permissions = ? WHERE id = ?", ("0", db_cid))
            print("Invalid CID Removed from DB... BUH BYE")

async def stow_roster(cid, fname, lname, rating_id, email, fullname, facility_join, rating_short):
    """
    Stores new users in the roster or updates existing ones
    """
    print(f"Stowing user {cid} in DB...")

    try:
        cur = connectSQL.cursor()

        cur.execute("SELECT permissions FROM users WHERE id = ?", (cid,))
        row = cur.fetchone()
        permissions = row[0] if row else None

        if permissions is not None:
            print(f"User {cid} exists with permissions: {permissions}")
            cur.execute("""
                UPDATE users 
                SET email=?, lname=?, rating_id=?, rating_short=?, visitor='0'
                WHERE id=?
            """, (email, lname, rating_id, rating_short, cid))

            cur.execute("""
                UPDATE roster
                SET full_name=?, visit='0'
                WHERE user_id=?
            """, (fullname, cid))

            cur.execute("UPDATE roster SET status='home' WHERE cid=? AND (status='visit' OR status IS NULL)", (cid,))
            if permissions == 0:
                cur.execute("UPDATE users SET permissions='1' WHERE id=?", (cid,))

        else:
            print(f"User {cid} not in DB. Inserting new user!")
            cur.execute("""
                INSERT INTO users (id, email, fname, lname, rating_id, rating_short, permissions, display_fname)
                VALUES (?, ?, ?, ?, ?, ?, '1', ?)
            """, (cid, email, fname, lname, rating_id, rating_short, fname))

            cur.execute("""
                INSERT INTO roster (cid, user_id, full_name, status, active, visit)
                VALUES (?, ?, ?, 'home', '1', '0')
            """, (cid, cid, fullname))

        if permissions:
            role_map = {2: 'mentor', 3: 'ins', 4: 'staff', 5: 'exec'}
            staff_role = role_map.get(permissions)
            if staff_role:
                print(f"Assigning staff role '{staff_role}' to {cid}")
                cur.execute("UPDATE roster SET staff=? WHERE cid=?", (staff_role, cid))

        cur.execute("""
            SELECT delgnd, delgnd_t2, twr, twr_t2, dep, app, app_t2, ctr, fss
            FROM roster WHERE cid=?
        """, (cid,))
        certs = cur.fetchone()

        if certs and all(c == 0 for c in certs):
            cur.execute("SELECT COUNT(*) FROM students WHERE user_id=?", (cid,))
            is_student = cur.fetchone()[0]

            if is_student == 0:
                print(f"Adding user {cid} to students table!")
                cur.execute("""
                    INSERT INTO students (user_id, times, position, status, instructor_id, renewal_token, renewed_at, renewal_expires_at, last_status_change, created_at, updated_at)
                    VALUES (?, NULL, 1, ?, NULL, NULL, UTC_TIMESTAMP, NULL, UTC_TIMESTAMP, ?, UTC_TIMESTAMP)
                """, (cid, 0, facility_join))
                student_id = cur.lastrowid

                cur.execute("""
                    INSERT INTO student_interactive_labels (student_label_id, student_id, created_at, updated_at)
                    VALUES
                        (8, ?, UTC_TIMESTAMP, UTC_TIMESTAMP),
                        (1, ?, UTC_TIMESTAMP, UTC_TIMESTAMP)
                """, (student_id, student_id,))

                cur.execute("""
                    INSERT INTO student_notes (student_id, author_id, title, content, created_at, updated_at)
                    VALUES (?, 1, 'Created', CONCAT('Student created automatically by System at ', NOW()), NOW(), NOW())
                """, (student_id,))
                print(f"Created user {cid} !")

    except Exception as e:
        print(f"Error processing user {cid}: {e}")
        send_webhook(f"Error for user {cid}: {e}")
        sys.exit(1)
    finally:
        print(f"Completed User {cid}!")


async def stow_visit_roster(cid, fname, lname, rating_id, email, fullname, facility_join, rating_short):
    """
    Stores new visiting users or updates existing ones as visitors
    """
    print(f"Processing visiting user {cid}...")
    try:
        cur = connectSQL.cursor()

        cur.execute("SELECT permissions FROM users WHERE id=?", (cid,))
        row = cur.fetchone()
        permissions = row[0] if row else None

        if permissions is not None:
            print(f"User {cid} exists with permissions: {permissions}")
            cur.execute("""
                UPDATE users 
                SET email=?, lname=?, rating_id=?, rating_short=?, visitor='1'
                WHERE id=?
            """, (email, lname, rating_id, rating_short, cid))

            cur.execute("""
                UPDATE roster 
                SET full_name=?, visit='1', status='visit'
                WHERE user_id=?
            """, (fullname, cid))

            if permissions == 0:
                cur.execute("UPDATE users SET permissions='1' WHERE id=?", (cid,))

        else:
            print(f"User {cid} not in DB. Inserting new visitor.")
            cur.execute("""
                INSERT INTO users (id, email, fname, lname, rating_id, rating_short, permissions, display_fname, visitor)
                VALUES (?, ?, ?, ?, ?, ?, '1', ?, '1')
            """, (cid, email, fname, lname, rating_id, rating_short, fname))

            cur.execute("""
                INSERT INTO roster (cid, user_id, full_name, status, active, visit)
                VALUES (?, ?, ?, 'visit', '1', '1')
            """, (cid, cid, fullname))

        cur.execute("""
            SELECT delgnd, delgnd_t2, twr, twr_t2, dep, app, app_t2, ctr, fss
            FROM roster WHERE cid=?
        """, (cid,))
        certs = cur.fetchone()

        if certs and all(c == 0 for c in certs):
            cur.execute("SELECT COUNT(*) FROM students WHERE user_id=?", (cid,))
            is_student = cur.fetchone()[0]

            if is_student == 0:
                print(f"Adding user {cid} to visitor students table!")
                cur.execute("""
                    INSERT INTO students (user_id, times, position, status, instructor_id, renewal_token, renewed_at, renewal_expires_at, last_status_change, created_at, updated_at)
                    VALUES (?, NULL, 1, ?, NULL, NULL, UTC_TIMESTAMP, NULL, UTC_TIMESTAMP, ?, UTC_TIMESTAMP)
                """, (cid, 3, facility_join))
                student_id = cur.lastrowid

                cur.execute("""
                    INSERT INTO student_interactive_labels (student_label_id, student_id, created_at, updated_at)
                    VALUES
                        (9, ?, UTC_TIMESTAMP, UTC_TIMESTAMP),
                        (1, ?, UTC_TIMESTAMP, UTC_TIMESTAMP)
                """, (student_id, student_id,))

                cur.execute("SELECT division_code FROM users WHERE id=?", (cid,))
                division_row = cur.fetchone()
                division_code = division_row[0] if division_row else None
                vat_label = 17 if division_code == 'CAN' else 16
                cur.execute("""
                    INSERT INTO student_interactive_labels (student_label_id, student_id, created_at, updated_at)
                    VALUES (?, ?, UTC_TIMESTAMP, UTC_TIMESTAMP)
                """, (vat_label, student_id))

                cur.execute("""
                    INSERT INTO student_notes (student_id, author_id, title, content, created_at, updated_at)
                    VALUES (?, 1, 'Created', CONCAT('Student created automatically by System at ', NOW()), NOW(), NOW())
                """, (student_id,))

    except Exception as e:
        print(f"Error processing visiting user {cid}: {e}")
        send_webhook(f"Visitor error for user {cid}: {e}")
        sys.exit(1)
    finally:
        print(f"Finished processing visiting user {cid}!")

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
