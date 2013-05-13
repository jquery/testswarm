-- --------------------------------------------------------

--
-- Table structure for table `projects`
-- Insertions handled by the manageProject.php script.
--

CREATE TABLE `projects` (
  `id` varchar(255) binary NOT NULL PRIMARY KEY,

  -- Human readable display title for the front-end.
  `display_title` varchar(255) binary NOT NULL,

  -- URL pointing to a page with more information about this project
  -- (Optional field, can be empty).
  `site_url` blob NOT NULL default '',

  -- Salted hash of password (see LoginAction::comparePasswords).
  `password` tinyblob NOT NULL,

  -- SHA1 hash of authentication token.
  -- Refresh handled by the refreshProjectToken.php script.
  `auth_token` tinyblob NOT NULL,

  -- Project update timestamp (YYYYMMDDHHMMSS timestamp).
  `updated` binary(14) NOT NULL,

  -- Project creation timestamp (YYYYMMDDHHMMSS timestamp).
  `created` binary(14) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
-- Insertions and updates handled by the Client class.
--

CREATE TABLE `clients` (
  `id` int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

  -- Freeform client name.
  `name` varchar(255) binary NOT NULL,

  -- Key to config.userAgents property.
  `useragent_id` varchar(255) NOT NULL,

  -- Raw User-Agent string.
  `useragent` tinytext NOT NULL,

  -- Raw IP string as extractred by WebRequest::getIP
  `ip` varbinary(40) NOT NULL default '',

  -- YYYYMMDDHHMMSS timestamp.
  `updated` binary(14) NOT NULL,

  -- YYYYMMDDHHMMSS timestamp.
  `created` binary(14) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Usage: HomePage, SwarmstateAction.
CREATE INDEX idx_clients_useragent_updated ON clients (useragent_id, updated);

-- Usage: CleanupAction.
CREATE INDEX idx_clients_updated ON clients (updated);

-- Usage: ClientAction, ScoresAction, BrowserInfo and Client.
CREATE INDEX idx_clients_name_ua_created ON clients (name, useragent_id, created);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
-- Insertions handled by the AddjobAction class.
--

CREATE TABLE `jobs` (
  `id` int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

  -- Job name (can contain HTML).
  `name` varchar(255) binary NOT NULL default '',

  -- Key to projects.id field.
  `project_id` varchar(255) binary NOT NULL,

  -- YYYYMMDDHHMMSS timestamp.
  `created` binary(14) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Usage: ProjectAction.
CREATE INDEX idx_jobs_project_created ON jobs (project_id, created);

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

-- Usage: JobAction.
CREATE INDEX idx_runs_jobid ON runs (job_id);

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

  -- Key to config.userAgents property.
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

-- Usage: GetrunAction.
CREATE INDEX idx_run_useragent_useragent_status_run ON run_useragent (useragent_id, status, run_id);

-- Usage: CleanupAction.
CREATE INDEX ids_runs_results ON run_useragent (results_id);

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

  -- HTML snapshot of the test results page - gzipped.
  `report_html` blob NULL,

  -- Hash of random-generated token. To use as authentication to be allowed to
  -- store runresults in this row. This protects SaverunAction from bad
  -- insertions (otherwise the only ID is the auto incrementing ID, which is
  -- easy to fake).
  `store_token` binary(40) NOT NULL,

  -- YYYYMMDDHHMMSS timestamp.
  `updated` binary(14) NOT NULL,

  -- YYYYMMDDHHMMSS timestamp.
  `created` binary(14) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Usage: GetrunAction.
CREATE INDEX idx_runresults_run_client ON runresults (run_id, client_id);

-- Usage: CleanupAction.
CREATE INDEX idx_runresults_status_client ON runresults (status, client_id);

-- Usage: ScoresAction.
CREATE INDEX idx_runresults_client_total ON runresults (client_id, total);
