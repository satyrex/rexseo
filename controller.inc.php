<?php

/**
 * REXseo
 * Based on the URL-Rewrite Addon
 * @author dh[at]gn2-netwerk[dot]de Dave Holloway
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author code[at]rexdev[dot]de jeandeluxe
 * @package redaxo4.2
 * @version 1.3
 * @version svn:$Id$
 */

if (rex_request('rexseo_func')!="") {
  $path = $REX['INCLUDE_PATH'].'/addons/'.$myself;

  switch (rex_request('rexseo_func')) {
    case "googlesitemap":

      require_once $REX['INCLUDE_PATH'].'/addons/rexseo/classes/class.rexseo_sitemap.inc.php';
      $map = new rexseo_sitemap;

      switch(rex_request('mode'))
      {
        case'json':
          $map->setMode('json');
          $map->send();
        break;
        default:
          $map->send();
      }

      die();
    break;

    case "robots":
      require_once $REX['INCLUDE_PATH'].'/addons/rexseo/classes/class.rexseo_robots.inc.php';

      $robots = new rexseo_robots;
      if (isset ($REX['ADDON']['rexseo']['settings']['robots']) && $REX['ADDON']['rexseo']['settings']['robots'] != '')
        $robots->setContent($REX['ADDON']['rexseo']['settings']['robots']);
      $robots->addSitemapLink();
      $robots->send();

      die();
    break;

    case "backend.css":
      rex_send_file($path.'/files/backend.css','text/css');
      die();
    break;

    case "jsopenwin.gif":
      rex_send_file($path.'/files/jsopenwin.gif','image/gif');
      die();
    break;

    case "jquery.highlight-3.yui.js":
      rex_send_file($path.'/files/jquery.highlight-3.yui.js','text/javascript');
      die();
    break;

    case "jquery.autogrow-textarea.js":
      rex_send_file($path.'/files/jquery.autogrow-textarea.js','text/javascript');
      die();
    break;

    case "jquery.scrollTo-1.4.2-min.js":
      rex_send_file($path.'/files/jquery.scrollTo-1.4.2-min.js','text/javascript');
      die();
    break;

    default:
    break;
  }
}
?>