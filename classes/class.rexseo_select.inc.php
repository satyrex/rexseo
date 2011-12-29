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


class rexseo_select extends rex_select {
	
	
	/*
	 * addOption(); falls latin/iso verwendet wird, werden UTF8-zeichen dekodiert.
	 * 
	 */
	function addOption($key='',$value='') {
		global $REX;
		
				
		if(!strpos($REX['LANG'],'utf'))	{
			$key = utf8_decode($key);
			$value = utf8_decode($value);
		}
		return parent::addOption($key,$value);
	}
	
}

?>