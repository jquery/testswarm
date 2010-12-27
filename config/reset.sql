-- Resets the entire database
TRUNCATE run_client;
TRUNCATE clients;
TRUNCATE run_useragent;
TRUNCATE runs;
TRUNCATE jobs;
TRUNCATE users; -- Optional

-- Resets just the results from clients
UPDATE jobs SET `status`=0 WHERE 1=1;
UPDATE run_useragent SET `runs`=0, `completed`=0, `status`=0 WHERE 1=1;
UPDATE runs SET `status`=0 WHERE 1=1;
DELETE FROM run_client WHERE 1=1;
