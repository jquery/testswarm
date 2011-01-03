-- SQLITE3 Table Definitions

-- NOTES:
-- * What up with dates?


BEGIN TRANSACTION;

CREATE TABLE users (
       id INTEGER PRIMARY KEY,
       name TEXT NOT NULL UNIQUE DEFAULT '',
       updated INTEGER NOT NULL,
       created TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
       seed REAL NOT NULL DEFAULT 0.0,
       password TEXT NOT NULL DEFAULT '',
       auth TEXT NOT NULL DEFAULT '',
       email TEXT NOT NULL DEFAULT '',
       request TEXT NOT NULL DEFAULT ''
);

CREATE TABLE useragents (
       id INTEGER PRIMARY KEY,
       name TEXT NOT NULL DEFAULT '',
       engine TEXT NOT NULL DEFAULT '',
       version TEXT NOT NULL DEFAULT '',
       active INTEGER NOT NULL DEFAULT 0,
       current INTEGER NOT NULL DEFAULT 0,
       popular INTEGER NOT NULL DEFAULT 0,
       gbs INTEGER NOT NULL DEFAULT 0,
       beta INTEGER NOT NULL DEFAULT 0,
       mobile INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE clients (
       id INTEGER PRIMARY KEY,
       user_id INTEGER REFERENCES users,
       useragent_id INTEGER REFERENCES useragents,
       os TEXT NOT NULL DEFAULT 'xp',
       useragent TEXT NOT NULL DEFAULT '',
       ip TEXT NOT NULL DEFAULT '',
       updated INTEGER NOT NULL,
       created TEXT NOT NULL DEFAULT '0000-00-00 00:00:00'
);

CREATE TABLE jobs (
       id INTEGER PRIMARY KEY,
       user_id INTEGER REFERENCES users,
       name TEXT NOT NULL DEFAULT '',
       status INTEGER NOT NULL DEFAULT 0,
       updated INTEGER NOT NULL,
       created TEXT NOT NULL DEFAULT '0000-00-00 00:00:00'
);

CREATE TABLE runs (
       id INTEGER PRIMARY KEY,
       job_id INTEGER REFERENCES jobs,
       name TEXT NOT NULL DEFAULT '',
       url TEXT NOT NULL,
       status INTEGER NOT NULL DEFAULT 0,
       updated INTEGER NOT NULL,
       created TEXT NOT NULL DEFAULT '0000-00-00 00:00:00'
);

CREATE TABLE run_client (
       run_id INTEGER REFERENCES runs,
       client_id INTEGER REFERENCES clients,
       status INTEGER NOT NULL DEFAULT 0,
       fail INTEGER NOT NULL DEFAULT 0,
       error INTEGER NOT NULL DEFAULT 0,
       total INTEGER NOT NULL DEFAULT 0,
       results TEXT NOT NULL DEFAULT '',
       updated INTEGER NOT NULL,
       created TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
       PRIMARY KEY (run_id, client_id)
);

CREATE TABLE run_useragent (
       run_id INTEGER REFERENCES runs,
       useragent_id INTEGER REFERENCES useragents,
       runs INTEGER NOT NULL DEFAULT 0,
       max INTEGER NOT NULL DEFAULT 1,
       completed INTEGER NOT NULL DEFAULT 0,
       status INTEGER NOT NULL DEFAULT 0,
       updated INTEGER NOT NULL,
       created TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
       PRIMARY KEY (run_id, useragent_id)
);

COMMIT;
