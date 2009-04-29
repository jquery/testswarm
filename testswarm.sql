-- phpMyAdmin SQL Dump
-- version 2.11.9.3
-- http://www.phpmyadmin.net
--
-- Host: db.testswarm.com
-- Generation Time: Apr 29, 2009 at 10:14 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `testswarm`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `useragent_id` int(11) NOT NULL,
  `useragent` tinytext NOT NULL,
  `ip` varchar(15) NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `clients`
--


-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL default '0',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `jobs`
--


-- --------------------------------------------------------

--
-- Table structure for table `runs`
--

CREATE TABLE IF NOT EXISTS `runs` (
  `id` int(11) NOT NULL auto_increment,
  `job_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` tinytext NOT NULL,
  `status` tinyint(4) NOT NULL default '0',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `runs`
--


-- --------------------------------------------------------

--
-- Table structure for table `run_client`
--

CREATE TABLE IF NOT EXISTS `run_client` (
  `run_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL default '0',
  `fail` int(11) NOT NULL default '0',
  `total` int(11) NOT NULL default '0',
  `results` text NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`run_id`,`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `run_client`
--


-- --------------------------------------------------------

--
-- Table structure for table `run_useragent`
--

CREATE TABLE IF NOT EXISTS `run_useragent` (
  `run_id` int(11) NOT NULL,
  `useragent_id` int(11) NOT NULL,
  `runs` int(11) NOT NULL default '0',
  `min` int(11) NOT NULL default '1',
  `max` int(11) NOT NULL default '1',
  `completed` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`run_id`,`useragent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `run_useragent`
--


-- --------------------------------------------------------

--
-- Table structure for table `useragents`
--

CREATE TABLE IF NOT EXISTS `useragents` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `engine` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `useragents`
--

INSERT INTO `useragents` (`id`, `name`, `engine`, `version`) VALUES
(1, 'Firefox 3.0', 'gecko', '1.9.0'),
(2, 'Firefox 3.5', 'gecko', '1.9.2'),
(3, 'Safari 3.2', 'webkit', '525'),
(4, 'Safari 4', 'webkit', '530'),
(5, 'Internet Explorer 6', 'msie', '6'),
(6, 'Internet Explorer 7', 'msie', '7'),
(7, 'Internet Explorer 8', 'msie', '8'),
(8, 'Opera 9.6', 'opera', '9.6'),
(9, 'Chrome 1.0', 'chrome', '525'),
(10, 'Chrome 2.0', 'chrome', '530'),
(11, 'Firefox 2.0', 'gecko', '1.8.1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `users`
--


