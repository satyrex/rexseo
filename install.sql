-- /**
--  * RexSEO Addon
--  *
--  * @link http://gn2-code.de/projects/rexseo/
--  * @link https://github.com/gn2netwerk/rexseo
--  *
--  * @author dh[at]gn2-netwerk[dot]de Dave Holloway
--  * @author code[at]rexdev[dot]de jeandeluxe
--  *
--  * Based on url_rewrite Addon by
--  * @author markus.staab[at]redaxo[dot]de Markus Staab
--  *
--  * @package redaxo4.2.x/4.3.x
--  * @version 1.4
--  * @version svn:$Id$
--  */

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%rexseo_redirects` (
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

INSERT INTO `%TABLE_PREFIX%630_cronjobs` (`id`, `name`, `type`, `parameters`, `interval`, `nexttime`, `environment`, `status`, `createdate`, `createuser`, `updatedate`, `updateuser`) VALUES
('', 'RexSEO Redirect Expire', 'rex_cronjob_phpcode', 'a:1:{s:24:"rex_cronjob_phpcode_code";s:35:"rexseo_htaccess_update_redirects();";}', '|1|d|', 0, '|0|1|', 1, CURDATE(), 'rexseo', CURDATE(), 'rexseo');