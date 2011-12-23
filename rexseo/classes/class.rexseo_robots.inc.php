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

class rexseo_robots
{
  private $host;
  private $robots_txt;

  /**
   * ASSEMBLE ROBOTS.TXT
   *
   * @return  (string) robots.txt
   */
  private function assemble_robots()
  {
    global $REX;

    if (isset ($REX['ADDON']['rexseo']['settings']['robots']) && 
               $REX['ADDON']['rexseo']['settings']['robots'] != '')
    {
      $this->robots_txt = $REX['ADDON']['rexseo']['settings']['robots'];
    }
    else
    {
      $this->robots_txt = 'User-agent: *
      Disallow:';
    }
    
    $this->robots_txt .= PHP_EOL.'Sitemap: '.$this->host.'sitemap.xml';
  }


  /**
   * CONSTRUCTOR
   */
  public function rexseo_robots()
  {
    global $REX;
    $this->host = $REX['SERVER'];

    self::assemble_robots();
  }


  /**
   * RETURN ROBOTS.TXT
   *
   * @return  (string) robots.txt
   */
  public function get()
  {
    return $this->robots_txt;
  }

}

?>