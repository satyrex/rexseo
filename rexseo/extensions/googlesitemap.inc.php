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


require_once $REX['INCLUDE_PATH'].'/addons/rexseo/classes/class.rexseo_sitemap.inc.php';

$map = new rexseo_sitemap;
$map = $map->get();

header('Content-Type: application/xml');
header('Content-Length: '.strlen($map));
echo $map;
