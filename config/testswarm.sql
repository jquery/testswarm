-- 
-- Table structure for table `clients`
-- 

CREATE TABLE `clients` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `useragent_id` int(11) NOT NULL default '0',
  `os` varchar(10) NOT NULL default 'xp',
  `useragent` tinytext NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `jobs`
-- 

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `status` tinyint(4) NOT NULL default '0',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `run_client`
-- 

CREATE TABLE `run_client` (
  `run_id` int(11) NOT NULL default '0',
  `client_id` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `fail` int(11) NOT NULL default '0',
  `error` int(11) NOT NULL default '0',
  `total` int(11) NOT NULL default '0',
  `results` text NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`run_id`,`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `run_useragent`
-- 

CREATE TABLE `run_useragent` (
  `run_id` int(11) NOT NULL default '0',
  `useragent_id` int(11) NOT NULL default '0',
  `runs` int(11) NOT NULL default '0',
  `max` int(11) NOT NULL default '1',
  `completed` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`run_id`,`useragent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `runs`
-- 

CREATE TABLE `runs` (
  `id` int(11) NOT NULL auto_increment,
  `job_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `url` tinytext NOT NULL,
  `status` tinyint(4) NOT NULL default '0',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `useragents`
-- 

CREATE TABLE `useragents` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `engine` varchar(255) NOT NULL default '',
  `version` varchar(255) NOT NULL default '',
  `active` tinyint(4) NOT NULL default '0',
  `current` tinyint(4) NOT NULL default '0',
  `popular` tinyint(4) NOT NULL default '0',
  `gbs` tinyint(4) NOT NULL default '0',
  `beta` tinyint(4) NOT NULL default '0',
  `mobile` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `seed` double NOT NULL default '0',
  `password` varchar(40) NOT NULL default '',
  `auth` varchar(40) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `request` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Indexes and Foreign Keys
alter table clients
	add index idx_clients_user_id (user_id),
	add constraint fk_clients_user_id foreign key (user_id) references users (id);
alter table clients
	add index idx_clients_useragent_id (useragent_id),
	add constraint fk_clients_useragent_id foreign key (useragent_id) references useragents (id);
alter table jobs
	add index idx_jobs_user_id (user_id),
	add constraint fk_jobs_user_id foreign key (user_id) references users (id);
alter table run_client
	add index idx_run_client_run_id (run_id),
	add constraint fk_run_client_run_id foreign key (run_id) references runs (id);
alter table run_client
	add index idx_run_client_client_id (client_id),
	add constraint fk_run_client_client_id foreign key (client_id) references clients (id);
alter table run_useragent
	add index idx_run_useragent_run_id (run_id),
	add constraint fk_run_useragent_run_id foreign key (run_id) references runs (id);
alter table run_useragent
	add index idx_run_useragent_useragent_id (useragent_id),
	add constraint fk_run_useragent_useragent_id foreign key (useragent_id) references useragents (id);
alter table runs
	add index idx_runs_job_id (job_id),
	add constraint fk_runs_job_id foreign key (job_id) references jobs (id);
