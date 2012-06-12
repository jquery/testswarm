-- --------------------------------------------------------

--
-- Table structure for table `users`
-- Insertions handled by the SignupAction class.
--

CREATE TABLE `users` (
  `id` int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

  -- User name used for display and login prodecure.
  `name` varchar(255) binary NOT NULL default '',

  -- Password hash salt (SHA1).
  `seed` binary(40) NOT NULL default '',

  -- Password hash (SHA1).
  `password` binary(40) NOT NULL default '',

  -- Authentication token to use in situations where sending passwords
  -- is not an option. Used for the "addjob" API.
  `auth` binary(40) NOT NULL default '',

  -- User account details last modified (YYYYMMDDHHMMSS timestamp)
  -- Right now this is only set during creation, no update statement
  -- exists yet in TestSwarm, but for consistency with other tables
  -- it has been created for future use.
  `updated` binary(14) NOT NULL,

  -- User account originally created (YYYYMMDDHHMMSS timestamp).
  `created` binary(14) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE UNIQUE INDEX idx_users_name ON users (name);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
-- Insertions and updates handled by the Client class.
--

CREATE TABLE `clients` (
  `id` int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

  -- Key to users.id field.
  `user_id` int unsigned NOT NULL,

  -- Key to useragents.ini section.
  `useragent_id` varchar(255) NOT NULL default '',

  -- Raw User-Agent string.
  `useragent` tinytext NOT NULL,

  -- Raw IP string as extractred by WebRequest::getIP
  `ip` varbinary(40) NOT NULL default '',

  -- YYYYMMDDHHMMSS timestamp.
  `updated` binary(14) NOT NULL,

  -- YYYYMMDDHHMMSS timestamp.
  `created` binary(14) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Used on the HomePage and SwarmstateAction.
CREATE INDEX idx_clients_useragent_updated ON clients (useragent_id, updated);

-- Used for listing in UserAction and ScoresAction.
-- Also used to verify BrowserInfo/Client match.
CREATE INDEX idx_clients_user_useragent_updated ON clients (user_id, useragent_id, updated);

-- Foreign key constrains
ALTER TABLE clients
	ADD CONSTRAINT fk_clients_user_id FOREIGN KEY (user_id) REFERENCES users (id);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
-- Insertions handled by the AddjobAction class.
--

CREATE TABLE `jobs` (
  `id` int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

  -- Key to users.id field.
  `user_id` int unsigned NOT NULL,

  -- Job name (can contain HTML)
  `name` varchar(255) binary NOT NULL default '',

  -- YYYYMMDDHHMMSS timestamp.
  `created` binary(14) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Used in UserAction.
CREATE INDEX idx_jobs_user ON jobs (user_id, created);

-- Foreign key constrains
ALTER TABLE jobs
	ADD CONSTRAINT fk_jobs_user_id FOREIGN KEY (user_id) REFERENCES users (id);

-- --------------------------------------------------------

--
-- Table structure for table `runs`
-- Insertions handled by the AddjobAction class.
--

CREATE TABLE `runs` (
  `id` int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

  -- Key to jobs.id field.
  `job_id` int unsigned NOT NULL,

  -- Run name
  `name` varchar(255) binary NOT NULL default '',

  -- Run url
  `url` tinytext NOT NULL,

  -- YYYYMMDDHHMMSS timestamp.
  `created` binary(14) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Used in JobAction.
CREATE INDEX idx_runs_jobid ON runs (job_id);

ALTER TABLE runs
	ADD CONSTRAINT fk_runs_job_id FOREIGN KEY (job_id) REFERENCES jobs (id);

-- --------------------------------------------------------

--
-- Table structure for table `run_useragent`
-- Insertions handled by the AddjobAction class. Updates by SaverunAction,
-- WipejobAction, and WiperunAction.
--

CREATE TABLE `run_useragent` (
  `id` int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

  -- Key to runs.id field.
  `run_id` int unsigned NOT NULL default 0,

  -- Key to useragents.ini section.
  `useragent_id` varchar(255) NOT NULL default '',

  -- Addjob runMax
  `max` int unsigned NOT NULL default 1,

  -- Number of times this run has run to completion for this user agent.
  -- In most cases this will end up being set to 1 once and then never
  -- touched again. If a client completes a run with one or more failed
  -- unit tests, another client will get the same run to phase out / reduce the
  -- risk of false negatives due to coindicing browser/connectivity issues,
  -- until `max` is reached.
  `completed` int unsigned NOT NULL default 0,

  -- Run status
  -- 0 = idle (awaiting (re-)run)
  -- 1 = busy (being run by a client)
  -- 2 = done (passed and/or reached max)
  `status` tinyint unsigned NOT NULL default 0,

  -- Key to runresults.id field.
  -- If NULL, it means this run has not been ran yet (or it was wiped / cleaned).
  `results_id` int unsigned default NULL,

  -- YYYYMMDDHHMMSS timestamp.
  `updated` binary(14) NOT NULL,

  -- YYYYMMDDHHMMSS timestamp.
  `created` binary(14) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE UNIQUE INDEX idx_run_useragent_run_useragent ON run_useragent (run_id, useragent_id);
CREATE INDEX idx_run_useragent_useragent ON run_useragent (useragent_id);

ALTER TABLE run_useragent
	ADD CONSTRAINT fk_run_useragent_run_id FOREIGN KEY (run_id) REFERENCES runs (id);

-- --------------------------------------------------------

--
-- Table structure for table `runresults`
-- Insertions handled by the GetrunAction class. Updates by SaverunAction.
-- Should never be removed from.

CREATE TABLE `runresults` (
  `id` int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

  -- Key to runs.id field.
  `run_id` int unsigned NOT NULL,

  -- Key to clients.id field.
  `client_id` int unsigned NOT NULL,

  -- Client run status
  -- 1 = busy
  -- 2 = finished
  -- 3 = timed-out (maximum execution time exceeded)
  -- 4 = timed-out (client lost, set from CleanupAction)
  `status` tinyint unsigned NOT NULL default 0,

  -- Total number of tests ran.
  `total` int unsigned NOT NULL default 0,

  -- Number of failed tests.
  `fail` int unsigned NOT NULL default 0,

  -- Number of errors.
  `error` int unsigned NOT NULL default 0,

  -- HTML snapshot of the test results page.
  `report_html` text NOT NULL default '',

  -- Hash of random-generated token. To use as authentication to be allowed to
  -- store runresults in this rpw. This protects SaverunAction from bad
  -- insertions (otherwise the only ID is the auto incrementing ID, which is
  -- easy to fake).
  `store_token` binary(40) NOT NULL default '',

  -- YYYYMMDDHHMMSS timestamp.
  `updated` binary(14) NOT NULL,

  -- YYYYMMDDHHMMSS timestamp.
  `created` binary(14) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Used in GetrunAction.
CREATE INDEX idx_runresults_run_client ON runresults (run_id, client_id);

ALTER TABLE runresults
	ADD CONSTRAINT fk_runresults_client_id FOREIGN KEY (client_id) REFERENCES clients (id);
