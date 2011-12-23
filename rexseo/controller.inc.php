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
      $map = $map->get();

      header('Content-Type: application/xml');
      header('Content-Length: '.strlen($map));
      echo $map;

      die();
    break;

    case "robots":
      require_once $REX['INCLUDE_PATH'].'/addons/rexseo/classes/class.rexseo_robots.inc.php';

      $robots = new rexseo_robots;
      $robots = $robots->get();

      header('Content-Type: text/plain; charset=UTF-8');
      header('Content-Length: '.strlen($robots));
      echo $robots;
      die();
    break;

    case "backend.css":
      header('Content-Type:text/css');
      echo file_get_contents($path.'/files/backend.css');
      die();
    break;

    case "jsopenwin.gif":
      header('Content-Type:image/gif');
      echo file_get_contents($path.'/files/jsopenwin.gif');
      die();
    break;

    case "jquery.highlight-3.yui.js":
      header('Content-Type:text/javascript');
      echo file_get_contents($path.'/files/jquery.highlight-3.yui.js');
      die();
    break;

    case "jquery.autogrow-textarea.js":
      header('Content-Type:text/javascript');
      echo file_get_contents($path.'/files/jquery.autogrow-textarea.js');
      die();
    break;

    case "jquery.scrollTo-1.4.2-min.js":
      header('Content-Type:text/javascript');
      echo file_get_contents($path.'/files/jquery.scrollTo-1.4.2-min.js');
      die();
    break;

    default:
    break;
  }
}
?>