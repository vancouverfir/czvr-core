import configparser
import os
import sys
import mariadb

#Modifies the roster to allow for GCAP roster changes

config = configparser.RawConfigParser()
assert os.path.exists("./pyapiconf.ini")
CONFIGFILEPATH = "./pyapiconf.ini"
config.read(CONFIGFILEPATH)

DBUser = config.get("ServerDB", "ID")
DBServer = config.get("ServerDB", "Address")
DBPort = config.get("ServerDB", "Port")
DBPass = config.get("ServerDB", "Password")
DBName = config.get("ServerDB", "DBName")

# Connect to your SQLite database
try:
    conn = mariadb.connect(
        user=DBUser,
        password=DBPass,
        host=DBServer,
        port=int(DBPort),
        database=DBName,
        autocommit=True,
    )
except mariadb.Error as e:
    print(f"Error connecting to MariaDB Platform: {e}")
    sys.exit(1)
cursor = conn.cursor()

col_query = """
ALTER TABLE roster
    ADD COLUMN twr_t2 INT NOT NULL DEFAULT 0 AFTER twr,
    ADD COLUMN app_t2 INT NOT NULL DEFAULT 0 AFTER app,
    MODIFY COLUMN del INT NOT NULL DEFAULT 0,
    MODIFY COLUMN gnd INT NOT NULL DEFAULT 0,
    MODIFY COLUMN twr INT NOT NULL DEFAULT 0,
    MODIFY COLUMN dep INT NOT NULL DEFAULT 0,
    MODIFY COLUMN app INT NOT NULL DEFAULT 0,
    MODIFY COLUMN ctr INT NOT NULL DEFAULT 0;
"""

gnd_del_query = """
     UPDATE roster
    SET del = CASE
        WHEN del = 6 THEN 3
        WHEN del = 5 THEN 2
        WHEN del = 4 THEN 1
        ELSE 0
    END,
    gnd = CASE
        WHEN gnd = 6 THEN 3
        WHEN gnd = 5 THEN 2
        WHEN gnd = 4 THEN 1
        ELSE 0
    END
"""

# Update the existing column and set the values for the new columns
twr_query = """
    UPDATE roster
    SET twr_t2 = CASE
        WHEN twr = 6 THEN 3
        WHEN twr = 7 THEN 1
        WHEN twr = 5 THEN 2
        WHEN twr = 4 THEN 1
        ELSE 0
    END,
    twr = CASE
        WHEN twr = 6 THEN 3
        WHEN twr = 7 THEN 2
        WHEN twr = 5 THEN 1
        WHEN twr = 4 THEN 1
        WHEN twr = 3 THEN 2
        WHEN twr = 2 THEN 1
        WHEN twr = 1 THEN 0
    END
"""

dep_app_query = """
    UPDATE roster
    SET dep = CASE
        WHEN dep = 4 THEN 3
        WHEN dep = 3 THEN 2
        WHEN dep = 2 THEN 1
        WHEN dep = 1 THEN 0
    END,
    app_t2 = CASE
        WHEN app = 6 THEN 3
        WHEN app = 7 THEN 1
        WHEN app = 5 THEN 2
        WHEN app = 4 THEN 1
        ELSE 0
    END,
    app = CASE
        WHEN app = 6 THEN 3
        WHEN app = 7 THEN 2
        WHEN app = 5 THEN 1
        WHEN app = 4 THEN 1
        WHEN app = 3 THEN 2
        WHEN app = 2 THEN 1
        WHEN app = 1 THEN 0
    END
"""

center_query = """
    UPDATE roster
    SET ctr = CASE
        WHEN ctr = 4 THEN 3
        WHEN ctr = 3 THEN 2
        WHEN ctr = 2 THEN 1
        WHEN ctr = 1 THEN 0
    END
"""

fss_query = """
    UPDATE roster
    SET fss = CASE
        WHEN fss = 4 THEN 3
        WHEN fss = 3 THEN 2
        WHEN fss = 2 THEN 1
        WHEN fss = 1 THEN 0
    END
"""

gnd_del_combine = """
    ALTER TABLE roster RENAME COLUMN del TO delgnd;
    ALTER TABLE roster RENAME COLUMN gnd TO delgnd_t2;
"""

gnd_del_set = """
    UPDATE roster
    SET delgnd_t2 = delgnd;
"""

# Execute the update query
# cursor.execute(col_query)
# cursor.execute(gnd_del_query)
# cursor.execute(twr_query)
# cursor.execute(dep_app_query)
# cursor.execute(center_query)
cursor.execute(gnd_del_combine)
cursor.execute(gnd_del_set)
# Commit the changes and close the connection
conn.commit()
conn.close()

