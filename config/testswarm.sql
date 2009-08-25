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
  `os` varchar(10) NOT NULL default 'xp',
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

INSERT INTO `useragents` (`id`, `name`, `engine`, `version`, `os`, `active`, `current`, `popular`, `gbs`, `beta`, `mobile`) VALUES
(1, 'Firefox 3.0', 'gecko', '^1.9.0', 'xp', 1, 0, 1, 1, 0, 0),
(2, 'Firefox 3.5', 'gecko', '^1.9.1[0-9.]*$', 'xp', 1, 1, 1, 0, 0, 0),
(3, 'Safari 3.2', 'webkit', '^525', 'xp', 1, 0, 0, 0, 0, 0),
(4, 'Safari 4.0', 'webkit', '^53', 'xp', 1, 1, 0, 0, 0, 0),
(5, 'Internet Explorer 6', 'msie', '^6.', 'xp', 1, 1, 1, 1, 0, 0),
(6, 'Internet Explorer 7', 'msie', '^7.', 'xp', 1, 0, 1, 1, 0, 0),
(7, 'Internet Explorer 8', 'msie', '^8.', 'xp', 1, 1, 1, 1, 0, 0),
(8, 'Opera  9.6', 'presto', '^2.1', 'xp', 1, 1, 1, 1, 0, 0),
(9, 'Chrome 1.0', 'chrome', '^525', 'xp', 1, 0, 0, 0, 0, 0),
(10, 'Chrome 2.0', 'chrome', '^530', 'xp', 1, 1, 1, 0, 0, 0),
(11, 'Firefox 2.0', 'gecko', '^1.8.1', 'xp', 1, 0, 1, 1, 0, 0),
(12, 'Opera 10', 'presto', '^2.2.15$', 'xp', 1, 0, 0, 0, 1, 0),
(13, 'Firefox 3.0', 'gecko', '^1.9.0', 'osx10.4', 1, 0, 1, 0, 0, 0),
(14, 'Firefox 3.5', 'gecko', '^1.9.1[0-9.]*$', 'osx10.4', 1, 1, 1, 0, 0, 0),
(15, 'Safari 3.2', 'webkit', '^525', 'osx10.4', 1, 0, 1, 1, 0, 0),
(16, 'Safari 4.0', 'webkit', '^53', 'osx10.4', 1, 1, 1, 0, 0, 0),
(17, 'Opera  9.6', 'presto', '^2.1', 'osx', 1, 1, 1, 1, 0, 0),
(18, 'Opera 10', 'presto', '^2.2.15$', 'osx', 1, 0, 0, 0, 1, 0),
(19, 'Firefox 2.0', 'gecko', '^1.8.1', 'osx', 1, 0, 0, 0, 0, 0),
(20, 'Firefox 3.0', 'gecko', '^1.9.0', 'vista', 1, 0, 1, 1, 0, 0),
(21, 'Firefox 3.5', 'gecko', '^1.9.1[0-9.]*$', 'vista', 1, 1, 1, 0, 0, 0),
(22, 'Safari 3.2', 'webkit', '^525', 'vista', 1, 0, 0, 0, 0, 0),
(23, 'Safari 4.0', 'webkit', '^53', 'vista', 1, 1, 0, 0, 0, 0),
(25, 'Internet Explorer 7', 'msie', '^7.', 'vista', 1, 0, 1, 1, 0, 0),
(26, 'Internet Explorer 8', 'msie', '^8.', 'vista', 1, 1, 1, 1, 0, 0),
(27, 'Opera  9.6', 'presto', '^2.1', 'vista', 1, 1, 1, 0, 0, 0),
(28, 'Chrome 1.0', 'chrome', '^525', 'vista', 1, 0, 0, 0, 0, 0),
(29, 'Chrome 2.0', 'chrome', '^530', 'vista', 1, 1, 1, 0, 0, 0),
(31, 'Opera 10', 'presto', '^2.2.15$', 'vista', 1, 0, 0, 0, 1, 0),
(32, 'Firefox 3.0', 'gecko', '^1.9.0', 'osx10.5', 1, 0, 1, 1, 0, 0),
(33, 'Firefox 3.5', 'gecko', '^1.9.1[0-9.]*$', 'osx10.5', 1, 1, 1, 0, 0, 0),
(34, 'Safari 3.2', 'webkit', '^525', 'osx10.5', 1, 0, 1, 1, 0, 0),
(35, 'Safari 4.0', 'webkit', '^53', 'osx10.5', 1, 1, 1, 0, 0, 0),
(39, 'Firefox 2.0', 'gecko', '^1.8.1', 'linux', 0, 0, 0, 0, 0, 0),
(40, 'Firefox 3.0', 'gecko', '^1.9.0', 'linux', 1, 0, 1, 0, 0, 0),
(41, 'Firefox 3.5', 'gecko', '^1.9.1[0-9.]*$', 'linux', 1, 1, 1, 0, 0, 0),
(42, 'Konqueror 4.2', 'konqueror', '^4.2', 'linux', 1, 1, 0, 0, 0, 0),
(43, 'Safari 3.1', 'webkit', '^525.19', 'osx10.4', 1, 0, 0, 0, 0, 0),
(44, 'Safari 3.1', 'webkit', '^525.19', 'osx10.5', 1, 0, 0, 0, 0, 0),
(45, 'Internet Explorer 6', 'msie', '^6.', '2000', 1, 0, 1, 1, 0, 0),
(46, 'Firefox 2.0', 'gecko', '^1.8.1', 'osx10.4', 0, 0, 0, 0, 0, 0),
(47, 'Firefox 2.0', 'gecko', '^1.8.1', 'osx10.5', 1, 0, 0, 1, 0, 0);

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

