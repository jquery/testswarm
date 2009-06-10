-- 
-- Table structure for table `clients`
-- 

CREATE TABLE `clients` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `useragent_id` int(11) NOT NULL default '0',
  `useragent` tinytext NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `run_client`
-- 

CREATE TABLE `run_client` (
  `run_id` int(11) NOT NULL default '0',
  `client_id` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `fail` int(11) NOT NULL default '0',
  `total` int(11) NOT NULL default '0',
  `results` text NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`run_id`,`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `run_useragent`
-- 

CREATE TABLE `run_useragent` (
  `run_id` int(11) NOT NULL default '0',
  `useragent_id` int(11) NOT NULL default '0',
  `runs` int(11) NOT NULL default '0',
  `min` int(11) NOT NULL default '1',
  `max` int(11) NOT NULL default '1',
  `completed` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`run_id`,`useragent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `useragents`
-- 

CREATE TABLE `useragents` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `engine` varchar(255) NOT NULL default '',
  `version` varchar(255) NOT NULL default '',
  `os` varchar(10) NOT NULL default 'xp',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `useragents`
-- 

INSERT INTO `useragents` (`id`, `name`, `engine`, `version`, `os`) VALUES (1, 'Firefox 3.0', 'gecko', '^1.9.0', 'xp'),
(2, 'Firefox 3.5b99', 'gecko', '^1.9.1b99$', 'xp'),
(3, 'Safari 3.2', 'webkit', '^525', 'xp'),
(4, 'Safari 4.0', 'webkit', '^530', 'xp'),
(5, 'Internet Explorer 6', 'msie', '^6.', 'xp'),
(6, 'Internet Explorer 7', 'msie', '^7.', 'xp'),
(7, 'Internet Explorer 8', 'msie', '^8.', 'xp'),
(8, 'Opera  9.6', 'presto', '^2.1', 'xp'),
(9, 'Chrome 1.0', 'chrome', '^525', 'xp'),
(10, 'Chrome 2.0', 'chrome', '^530', 'xp'),
(11, 'Firefox 2.0', 'gecko', '^1.8.1', 'xp'),
(12, 'Opera 10b1', 'presto', '^2.2.15$', 'xp'),
(13, 'Firefox 3.0', 'gecko', '^1.9.0', 'osx10.4'),
(14, 'Firefox 3.5b99', 'gecko', '^1.9.1b99$', 'osx10.4'),
(15, 'Safari 3.2', 'webkit', '^525', 'osx10.4'),
(16, 'Safari 4.0', 'webkit', '^530', 'osx10.4'),
(17, 'Opera  9.6', 'presto', '^2.1', 'osx'),
(18, 'Opera 10b1', 'presto', '^2.2.15$', 'osx'),
(19, 'Firefox 2.0', 'gecko', '^1.8.1', 'osx'),
(20, 'Firefox 3.0', 'gecko', '^1.9.0', 'vista'),
(21, 'Firefox 3.5b99', 'gecko', '^1.9.1b99$', 'vista'),
(22, 'Safari 3.2', 'webkit', '^525', 'vista'),
(23, 'Safari 4.0', 'webkit', '^530', 'vista'),
(25, 'Internet Explorer 7', 'msie', '^7.', 'vista'),
(26, 'Internet Explorer 8', 'msie', '^8.', 'vista'),
(27, 'Opera  9.6', 'presto', '^2.1', 'vista'),
(28, 'Chrome 1.0', 'chrome', '^525', 'vista'),
(29, 'Chrome 2.0', 'chrome', '^530', 'vista'),
(30, 'Firefox 2.0', 'gecko', '^1.8.1', 'vista'),
(31, 'Opera 10b1', 'presto', '^2.2.15$', 'vista'),
(32, 'Firefox 3.0', 'gecko', '^3.0', 'osx10.5'),
(33, 'Firefox 3.5b99', 'gecko', '^1.9.1b99$', 'osx10.5'),
(34, 'Safari 3.2', 'webkit', '^525', 'osx10.5'),
(35, 'Safari 4.0', 'webkit', '^530', 'osx10.5'),
(39, 'Firefox 2.0', 'gecko', '^1.8.1', 'linux'),
(40, 'Firefox 3.0', 'gecko', '^3.0', 'linux'),
(41, 'Firefox 3.5b99', 'gecko', '^1.9.1b99$', 'linux'),
(42, 'Konqueror 4.2', 'konqueror', '^4.2', 'linux');

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
