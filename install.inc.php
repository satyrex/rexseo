<?php
/**
 * RexSEO Addon
 *
 * @link http://gn2-code.de/projects/rexseo/
 * @link https://github.com/gn2netwerk/rexseo
 *
 * @author dh[at]gn2-netwerk[dot]de Dave Holloway
 * @author code[at]rexdev[dot]de jeandeluxe
 *
 * Based on url_rewrite Addon by
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4.2.x/4.3.x
 * @version 1.4
 * @version svn:$Id$
 */

// INSTALL SETTINGS
////////////////////////////////////////////////////////////////////////////////
$myself            = 'rexseo';
$myroot            = $REX['INCLUDE_PATH'].'/addons/'.$myself;

$minimum_REX       = '4.3.0';
$minimum_PHP       = 5;
$required_addons   = array('textile','metainfo','cronjob');
$disable_addons    = array('url_rewrite');
$htaccess_search   = array('x-mapp-php','php-cgi_wrapper');

$error             = array();

// CHECK REDAXO VERSION
////////////////////////////////////////////////////////////////////////////////
if(version_compare($REX['VERSION'].'.'.$REX['SUBVERSION'].'.'.$REX['MINORVERSION'], $minimum_REX, '<'))
{
  $error[] = 'Dieses Addon ben&ouml;tigt Redaxo Version '.$minimum_REX.' oder h&ouml;her.';
}


// CHECK PHP VERSION
////////////////////////////////////////////////////////////////////////////////
if(version_compare(PHP_VERSION, $minimum_PHP, '<'))
{
  $error[] = 'Dieses Addon ben&ouml;tigt mind. PHP '.$minimum_PHP.'!';
}


// CHECK REQUIRED ADDONS
////////////////////////////////////////////////////////////////////////////////
foreach($required_addons as $a)
{
  if (!OOAddon::isInstalled($a))
  {
    $error[] = 'Addon "'.$a.'" ist nicht installiert.  <span style="float:right;">[ <a href="index.php?page=addon&addonname='.$a.'&install=1">'.$a.' installieren</a> ]</span>';
  }
  else
  {
    if (!OOAddon::isAvailable($a))
    {
      $error[] = 'Addon "'.$a.'" ist nicht aktiviert.  <span style="float:right;">[ <a href="index.php?page=addon&addonname='.$a.'&activate=1">'.$a.' aktivieren</a> ]</span>';
    }
  }
}


// CHECK ADDONS TO DISABLE
////////////////////////////////////////////////////////////////////////////////
foreach($disable_addons as $a)
{
  if (OOAddon::isInstalled($a) || OOAddon::isAvailable($a))
  {
    $error[] = 'Addon "'.$a.'" muß erst deinstalliert werden.  <span style="float:right;">[ <a href="index.php?page=addon&addonname='.$a.'&uninstall=1">'.$a.' de-installieren</a> ]</span>';
  }
}


if(count($error)==0)
{
  // SETUP METAINFO
  //////////////////////////////////////////////////////////////////////////////
    // 4.3.x -> zusätzliches Feld "restrictions" (http://forum.redaxo.de/sutra80188.html#80188 -> erst ab metainfo r1871)
    //a62_add_field( $title,                        $name,                      $prior, $attributes, $type, $default, $params = null,                                                                                         $validate = null, $restrictions = '')
      a62_add_field( 'RexSEO Rewrite',              'art_rexseo_legend',        100,    '',         12,     '',       '',                                                                                                     '',               '');
      a62_add_field( 'Custom URL',                  'art_rexseo_url',           101,    '',          1,     '',       '',                                                                                                     '',               '');
      a62_add_field( 'Custom Canonical URL',        'art_rexseo_canonicalurl',  102,    '',          1,     '',       '',                                                                                                     '',               '');
      a62_add_field( 'Custom Page Title',           'art_rexseo_title',         103,    '',          1,     '',       '',                                                                                                     '',               '');
      a62_add_field( 'RexSEO Sitemap',              'art_rexseo_sitemap_legend',104,    '',         12,     '',       '',                                                                                                     '',               '');
      a62_add_field( 'Sitemap Priority',            'art_rexseo_priority',      105,    '',          3,     '',       ':auto|1.00:1.00|0.80:0.80|0.64:0.64|0.51:0.51|0.33:0.33|0.00:0.00',                                    '',               '');
      a62_add_field( 'Sitemap Changefreq',          'art_rexseo_changefreq',    105,    '',          3,     '',       ':auto|never:never|yearly:yearly|monthly:monthly|weekly:weekly|daily:daily|hourly:hourly|always:always','',               '');
      a62_add_field( 'Sitemap Output',              'art_rexseo_sitemap_out',   106,    '',          3,     '',       ':auto|show:show|hide:hide',                                                                            '',               '');



  // CHECK ROOT .HTACCESS FILE FOR CRITICAL SETTINGS
  //////////////////////////////////////////////////////////////////////////////
  $autoinstall  = true;
  if (file_exists($REX['FRONTEND_PATH'].'/.htaccess'))
  {
    $matches  = array();
    $htaccess = rex_get_file_contents($REX['FRONTEND_PATH'].'/.htaccess');

    foreach($htaccess_search as $needle)
    {
      if(strpos($htaccess,$needle)!==false)
      {
        $autoinstall = false;
        $matches[] = $needle;
      }
    }

    if(count($matches)>0)
    {
      $msg = 'RexSEO: Die original .htaccess Datei im Root Ordner enth&auml;lt potentiell kritische settings f&uuml;r den Serverbetrieb:<br>';
      foreach($matches as $m)
      {
        $msg .= '<em style="margin:4px 0 4px 10px;color:black;display:inline-block;">'.$m.'</em><br />';
      }
      $msg .= 'Die automatische Installation der .htaccess Dateien wurde deaktiviert,<br /> Details zur manuellen Installation siehe RexSEO Hilfe.';
      echo rex_warning($msg);
    }
  }


  // INSTALL/COPY .HTACCESS FILES
  //////////////////////////////////////////////////////////////////////////////
  if($autoinstall)
  {
    require_once $myroot.'/functions/function.rexseo_helpers.inc.php';
    $source = $REX['INCLUDE_PATH'].'/addons/'.$myself.'/install/files/';
    $target = $REX['HTDOCS_PATH'];
    $result = rexseo_recursive_copy($source, $target);
  }

  $REX['ADDON']['install'][$myself] = 1;
}
else

{
  $REX['ADDON']['installmsg'][$myself] = '<br />'.implode($error,'<br />');
  $REX['ADDON']['install'][$myself] = 0;
}

?>