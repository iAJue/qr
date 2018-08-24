
CREATE TABLE `qr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qr` varchar(128) NOT NULL DEFAULT '',
  `name` varchar(10) NOT NULL DEFAULT '',
  `alipay` varchar(128) NOT NULL DEFAULT '',
  `qq` varchar(128) NOT NULL DEFAULT '',
  `wechat` varchar(128) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

