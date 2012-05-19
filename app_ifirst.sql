SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `buzz` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `pubtime` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`,`pubtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

INSERT INTO `buzz` (`id`, `user_id`, `class_id`, `pubtime`, `content`) VALUES
(1, 1, 2, 1337134347, '不知道该说什么好呢！！！！'),
(2, 1, 2, 1337134442, '我要继续发布呀！！！从前有座山，山上有座庙。苗栗面有三个和尚！！'),
(3, 1, 2, 1337139466, '你好阿！！！'),
(4, 1, 2, 1337140154, '你好阿'),
(5, 1, 2, 1337140166, '我不好呢！！！！'),
(6, 1, 2, 1337140456, '新的发布了！！！'),
(7, 1, 2, 1337150642, '现在心情很糟呢！！'),
(8, 1, 0, 1337158162, '今天是个好日子'),
(9, 1, 0, 1337176356, '我来发布东西吧！！！'),
(10, 1, 0, 1337179682, '手机端测试一下！！！'),
(11, 1, 0, 1337181051, '哈哈，测试一下发布消息。。。。'),
(12, 5, 0, 1337184132, '我是测试账号！！！');

CREATE TABLE IF NOT EXISTS `class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `courses` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `class` (`id`, `name`, `courses`) VALUES
(0, '公共区', 'a:3:{i:0;a:2:{s:4:"name";s:6:"综合";s:6:"credit";i:3;}i:1;a:2:{s:4:"name";s:12:"高等数学";s:6:"credit";d:2.5;}i:2;a:2:{s:4:"name";s:12:"大学英语";s:6:"credit";d:2.5;}}'),
(2, '08计算机联合班', 'a:3:{i:0;a:2:{s:4:"name";s:6:"综合";s:6:"credit";i:3;}i:1;a:2:{s:4:"name";s:12:"高等数学";s:6:"credit";d:2.5;}i:2;a:2:{s:4:"name";s:12:"大学英语";s:6:"credit";d:2.5;}}'),
(3, '新建的群组', 'a:0:{}');

CREATE TABLE IF NOT EXISTS `score` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `class_id` int(10) NOT NULL,
  `gpa` float NOT NULL DEFAULT '0',
  `scores` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`class_id`,`gpa`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `score` (`id`, `user_id`, `class_id`, `gpa`, `scores`) VALUES
(1, 1, 0, 4.8, 'a:3:{i:0;a:3:{s:4:"name";s:6:"综合";s:6:"credit";i:3;s:4:"mark";s:2:"91";}i:1;a:3:{s:4:"name";s:12:"高等数学";s:6:"credit";d:2.5;s:4:"mark";s:2:"86";}i:2;a:3:{s:4:"name";s:12:"大学英语";s:6:"credit";d:2.5;s:4:"mark";s:2:"98";}}');

CREATE TABLE IF NOT EXISTS `token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `value` varchar(100) NOT NULL,
  `stamp` int(10) NOT NULL,
  `count` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`value`,`stamp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO `token` (`id`, `user_id`, `value`, `stamp`, `count`) VALUES
(1, 1, '3dc5962906452fcbca4e493c0159f167', 1337185226, 0),
(2, 2, 'b1ad9625edcfc5a25ddd71428b8b51b4', 1337150191, 0),
(3, 3, 'e7078f8469250a3bfb0e890d50a2b9c5', 1337158103, 0),
(4, 5, '8264a98aaeef371b5f619dc19bfea292', 1337179777, 0);

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1是学生,5是老师,10是管理员',
  `name` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `user` (`id`, `class_id`, `type`, `name`, `password`, `email`) VALUES
(1, 0, 1, '学生', '0c7540eb7e65b553ec1ba6b20de79608', 'xinkiang@gmail.com'),
(2, 0, 5, '张弛', '0c7540eb7e65b553ec1ba6b20de79608', 'fastten@qq.com'),
(3, 0, 10, '管理员', '0c7540eb7e65b553ec1ba6b20de79608', 'admin@admin.com'),
(4, 3, 5, '老师', '0c7540eb7e65b553ec1ba6b20de79608', 'teacher@admin.com'),
(5, 0, 1, '测试账号', '0c7540eb7e65b553ec1ba6b20de79608', 'register2012@qq.com');
