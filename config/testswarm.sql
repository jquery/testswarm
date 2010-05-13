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

-- 
-- Dumping data for table `useragents`
-- 
INSERT INTO `useragents` (`name`, `engine`, `version`, `active`, `current`, `popular`, `gbs`, `beta`, `mobile`) VALUES
('Firefox 2.0', 'gecko', '^1.8.1', 1, 0, 1, 0, 0, 0),
('Firefox 3.0', 'gecko', '^1.9.0', 1, 0, 1, 1, 0, 0),
('Firefox 3.5', 'gecko', '^1.9.1[0-9.]*$', 1, 0, 1, 1, 0, 0),
('Firefox 3.6', 'gecko', '^1.9.2[0-9.]*$', 1, 1, 1, 1, 0, 0),
('Safari 3.1', 'webkit', '^525.19', 1, 0, 1, 0, 0, 0),
('Safari 3.2', 'webkit', '^525.2', 1, 0, 1, 0, 0, 0),
('Safari 4.0', 'webkit', '^53', 1, 1, 1, 1, 0, 0),
('webOS Browser 1.4', 'webos', '^1.4', 1, 1, 0, 0, 0, 1),
('Mobile Safari 2.2.1', 'mobilewebkit', '^525', 1, 0, 0, 0, 0, 1),
('Mobile Safari 3.1.3', 'mobilewebkit', '^528', 1, 1, 0, 0, 0, 1),
('Mobile Safari 3.2', 'mobilewebkit', '^531', 1, 0, 0, 0, 1, 1),
('Android 1.5/1.6', 'android', '^528.5', 1, 0, 0, 0, 0, 1),
('Android 2.1', 'android', '^530.17', 1, 1, 0, 0, 0, 1),
('S60 3.2', 's60', '^3.2$', 1, 0, 0, 0, 0, 1),
('S60 5.0', 's60', '^5.0$', 1, 1, 0, 0, 0, 1),
('Opera Mobile 10.0', 'operamobile', '^2.4.18$', 1, 1, 0, 0, 0, 1),
('Fennec 1.1b1', 'fennec', '^1.1b1', 1, 0, 0, 0, 1, 1),
('Windows Mobile 6.5', 'winmo', '^6.', 1, 0, 0, 0, 0, 1),
('Windows Mobile 7', 'winmo', '^7.', 1, 1, 0, 0, 0, 1),
('Blackberry 4.6', 'blackberry', '^4.6', 1, 0, 0, 0, 0, 1),
('Blackberry 4.7', 'blackberry', '^4.7', 1, 0, 0, 0, 0, 1),
('Blackberry 5', 'blackberry', '^5.0', 1, 1, 0, 0, 0, 1),
('Internet Explorer 6', 'msie', '^6.', 1, 0, 1, 1, 0, 0),
('Internet Explorer 7', 'msie', '^7.', 1, 0, 1, 1, 0, 0),
('Internet Explorer 8', 'msie', '^8.', 1, 1, 1, 1, 0, 0),
('Opera 9.6', 'presto', '^2.1', 1, 0, 1, 0, 0, 0),
('Opera 10.20', 'presto', '^2.2.15$', 1, 0, 1, 0, 0, 0),
('Opera 10.50', 'presto', '^2.5.22$', 1, 1, 1, 0, 0, 0),
('Chrome 4.0', 'chrome', '^532', 1, 1, 1, 1, 0, 0);

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
