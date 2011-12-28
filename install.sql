CREATE TABLE IF NOT EXISTS `rex_rexseo_redirects` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `createdate` int(11) NOT NULL,
  `updatedate` int(11) NOT NULL,
  `expiredate` int(11) NOT NULL,
  `creator` varchar(32) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `from_url` text NOT NULL,
  `to_article_id` int(4) NOT NULL,
  `to_clang` tinyint(2) NOT NULL,
  `http_status` int(3) NOT NULL DEFAULT '301',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;