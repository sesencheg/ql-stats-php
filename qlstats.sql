-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 26 2015 г., 14:07
-- Версия сервера: 5.1.61
-- Версия PHP: 5.3.3-1ubuntu9.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `qlstats`
--

-- --------------------------------------------------------

--
-- Структура таблицы `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `server` varchar(255) NOT NULL,
  `MATCH_GUID` varchar(255) NOT NULL,
  `MOD` varchar(255) NOT NULL,
  `OTHER_TEAM_ALIVE` varchar(255) NOT NULL,
  `OTHER_TEAM_DEAD` varchar(255) NOT NULL,
  `ROUND` varchar(255) NOT NULL,
  `SUICIDE` varchar(255) NOT NULL,
  `TEAMKILL` varchar(255) NOT NULL,
  `TEAM_ALIVE` varchar(255) NOT NULL,
  `TEAM_DEAD` varchar(255) NOT NULL,
  `TIME` varchar(255) NOT NULL,
  `WARMUP` varchar(255) NOT NULL,
  `KILLER_AIRBORNE` varchar(255) NOT NULL,
  `KILLER_AMMO` int(11) NOT NULL,
  `KILLER_ARMOR` int(11) NOT NULL,
  `KILLER_BOT` varchar(255) NOT NULL,
  `KILLER_BOT_SKILL` varchar(255) NOT NULL,
  `KILLER_HEALTH` int(11) NOT NULL,
  `KILLER_HOLDABLE` varchar(255) NOT NULL,
  `KILLER_NAME` varchar(255) NOT NULL,
  `KILLER_POWERUPS` varchar(255) NOT NULL,
  `KILLER_STEAM_ID` varchar(255) NOT NULL,
  `KILLER_SUBMERGED` varchar(255) NOT NULL,
  `KILLER_TEAM` int(11) NOT NULL,
  `KILLER_WEAPON` varchar(255) NOT NULL,
  `VICTIM_AIRBORNE` varchar(255) NOT NULL,
  `VICTIM_AMMO` int(11) NOT NULL,
  `VICTIM_ARMOR` int(11) NOT NULL,
  `VICTIM_BOT` varchar(255) NOT NULL,
  `VICTIM_BOT_SKILL` varchar(255) NOT NULL,
  `VICTIM_HEALTH` int(11) NOT NULL,
  `VICTIM_HOLDABLE` varchar(255) NOT NULL,
  `VICTIM_NAME` varchar(255) NOT NULL,
  `VICTIM_POWERUPS` varchar(255) NOT NULL,
  `VICTIM_STEAM_ID` varchar(255) NOT NULL,
  `VICTIM_STREAK` int(11) NOT NULL,
  `VICTIM_SUBMERGED` varchar(255) NOT NULL,
  `VICTIM_TEAM` int(11) NOT NULL,
  `VICTIM_WEAPON` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `matches`
--

CREATE TABLE IF NOT EXISTS `matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `server` varchar(255) NOT NULL,
  `CAPTURE_LIMIT` int(11) NOT NULL,
  `FACTORY` varchar(255) NOT NULL,
  `FACTORY_TITLE` varchar(255) NOT NULL,
  `FRAG_LIMIT` int(11) NOT NULL,
  `GAME_TYPE` varchar(255) NOT NULL,
  `INFECTED` int(11) NOT NULL,
  `INSTAGIB` int(11) NOT NULL,
  `MAP` varchar(255) NOT NULL,
  `MATCH_GUID` varchar(255) NOT NULL,
  `MERCY_LIMIT` int(11) NOT NULL,
  `PLAYERS` text NOT NULL,
  `QUADHOG` int(11) NOT NULL,
  `SCORE_LIMIT` int(11) NOT NULL,
  `SERVER_TITLE` mediumtext NOT NULL,
  `TIME_LIMIT` int(11) NOT NULL,
  `TRAINING` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `medals`
--

CREATE TABLE IF NOT EXISTS `medals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `MATCH_GUID` varchar(255) NOT NULL,
  `MEDAL` varchar(255) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `STEAM_ID` varchar(255) NOT NULL,
  `TIME` int(11) NOT NULL,
  `TOTAL` int(11) NOT NULL,
  `WARMUP` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `players`
--

CREATE TABLE IF NOT EXISTS `players` (
  `STEAM_ID` varchar(255) NOT NULL,
  `DUEL_ELO` int(11) NOT NULL DEFAULT '1200',
  `TDM_ELO` int(11) NOT NULL DEFAULT '1200',
  `CA_ELO` int(11) NOT NULL DEFAULT '1200',
  `FFA_ELO` int(11) NOT NULL DEFAULT '1200',
  UNIQUE KEY `STEAM_ID` (`STEAM_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `server` varchar(255) NOT NULL,
  `CAPTURE_LIMIT` int(11) NOT NULL,
  `EXIT_MSG` varchar(255) NOT NULL,
  `FACTORY` varchar(255) NOT NULL,
  `FACTORY_TITLE` varchar(255) NOT NULL,
  `FIRST_SCORER` varchar(255) NOT NULL,
  `FRAG_LIMIT` int(11) NOT NULL,
  `GAME_LENGTH` int(11) NOT NULL,
  `GAME_TYPE` varchar(255) NOT NULL,
  `INFECTED` int(11) NOT NULL,
  `INSTAGIB` int(11) NOT NULL,
  `LAST_LEAD_CHANGE_TIME` int(11) NOT NULL,
  `LAST_SCORER` varchar(255) NOT NULL,
  `LAST_TEAMSCORER` varchar(255) NOT NULL,
  `MAP` varchar(255) NOT NULL,
  `MATCH_GUID` varchar(255) NOT NULL,
  `MERCY_LIMIT` int(11) NOT NULL,
  `QUADHOG` int(11) NOT NULL,
  `RESTARTED` int(11) NOT NULL,
  `SCORE_LIMIT` int(11) NOT NULL,
  `SERVER_TITLE` mediumtext NOT NULL,
  `TIME_LIMIT` int(11) NOT NULL,
  `TRAINING` int(11) NOT NULL,
  `TSCORE0` int(11) NOT NULL,
  `TSCORE1` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `rounds`
--

CREATE TABLE IF NOT EXISTS `rounds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `server` varchar(255) NOT NULL,
  `ABORTED` varchar(255) NOT NULL,
  `CAPTURE_LIMIT` int(11) NOT NULL,
  `EXIT_MSG` varchar(255) NOT NULL,
  `MATCH_GUID` varchar(255) NOT NULL,
  `WARMUP` varchar(255) NOT NULL,
  `ROUND` int(11) NOT NULL,
  `TEAM_WON` varchar(255) NOT NULL,
  `TIME` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `stats`
--

CREATE TABLE IF NOT EXISTS `stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `server` varchar(255) NOT NULL,
  `ABORTED` varchar(255) NOT NULL,
  `BLUE_FLAG_PICKUPS` int(11) NOT NULL,
  `DAMAGE` mediumtext NOT NULL,
  `DEATHS` int(11) NOT NULL,
  `HOLY_SHITS` int(11) NOT NULL,
  `KILLS` int(11) NOT NULL,
  `LOSE` int(11) NOT NULL,
  `MATCH_GUID` varchar(255) NOT NULL,
  `MAX_STREAK` int(11) NOT NULL,
  `MEDALS` mediumtext NOT NULL,
  `MODEL` varchar(255) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `NEUTRAL_FLAG_PICKUPS` int(11) NOT NULL,
  `PICKUPS` mediumtext NOT NULL,
  `PLAY_TIME` int(11) NOT NULL,
  `QUIT` int(11) NOT NULL,
  `RANK` int(11) NOT NULL,
  `RED_FLAG_PICKUPS` int(11) NOT NULL,
  `SCORE` int(11) NOT NULL,
  `STEAM_ID` varchar(255) NOT NULL,
  `TEAM` int(11) NOT NULL,
  `TEAM_JOIN_TIME` int(11) NOT NULL,
  `TEAM_RANK` int(11) NOT NULL,
  `TIED_RANK` int(11) NOT NULL,
  `TIED_TEAM_RANK` int(11) NOT NULL,
  `WARMUP` varchar(255) NOT NULL,
  `WEAPONS` mediumtext NOT NULL,
  `WIN` int(11) NOT NULL,
  `old_elo` int(11) NOT NULL,
  `new_elo` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
