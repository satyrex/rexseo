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
   * CONSTRUCTOR
   */
  public function rexseo_robots()
  {
    global $REX;
    $this->host = $REX['SERVER'];
    $this->robots_txt = 'User-agent: *
    Disallow:';
  }


  /**
   * SET HOST
   *
   * @param $host  (string)  http://DOMAIN.TLD
   */
  public function setHost($host)
  {
    $this->host = rtrim($host,'/');
  }


  /**
   * SET CONTENT OF ROBOTS.TXT
   *
   * @return  (string) robots.txt
   */
  public function setContent($content)
  {
    $this->robots_txt = $content;
  }


  /**
   * INSERT SITEMAP LINK INTO ROBOTS.TXT
   *
   * @return  (string) robots.txt
   */
  public function addSitemapLink()
  {
    $this->robots_txt .= PHP_EOL.'Sitemap: '.$this->host.'sitemap.xml';
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


  /**
   * SEND ROBOTS.TXT
   */
  public function send()
  {
    $robots = self::get();

    header('Content-Type: text/plain; charset=UTF-8');
    header('Content-Length: '.strlen($robots));
    echo $robots;
    die();
  }

}

?>