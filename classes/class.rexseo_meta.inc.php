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


class rexseo_meta {

  private $article_id;
  private $clang;
  private $start_article_id;
  private $def_keys;
  private $def_desc;
  private $title_schema;
  private $base_url;
  private $install_subdir;
  private $params_starter;
  private $servername;
  private $http_host;
  private $protocol;
  private $rex_is_iso;
  private $encoder;

  function rexseo_meta()
  {
    global $REX;
    $this->article_id       = $REX['ARTICLE_ID'];
    $this->clang            = $REX['CUR_CLANG'];
    $this->start_article_id = $REX['START_ARTICLE_ID'];
    $this->title_schema     = $REX['ADDON']['rexseo']['settings']['title_schema'];
    $this->install_subdir   = $REX['ADDON']['rexseo']['settings']['install_subdir'];
    $this->params_starter   = $REX['ADDON']['rexseo']['settings']['params_starter'];
    $this->servername       = $REX['SERVERNAME'];
    $this->http_host        = $_SERVER['HTTP_HOST'];
    $this->def_keys         = $REX['ADDON']['rexseo']['settings']['def_keys'];
    $this->def_desc         = $REX['ADDON']['rexseo']['settings']['def_desc'];
    $this->protocol         = isset($REX['PROTOCOL']) ? $REX['PROTOCOL'] : self::get_protocol();
    $this->base_url         = $this->protocol.$this->http_host.'/'.$this->install_subdir;
    $this->rex_is_iso       = strpos($REX['LANG'], '_utf8')===false ? true : false;
    $this->encoder          = 'htmlspecialchars';
  }


  function get_title($article_id = null,$title_schema = null)
  {
    $article_id   = !$article_id   ? $this->article_id   : (int) $article_id;
    $title_schema = !$title_schema ? $this->title_schema : $title_schema;
    $curart = OOArticle::getArticleById($article_id);
    $art_rexseo_title = $curart->getValue('art_rexseo_title');

    // GET PARRENT CATS
    $parents = $curart->getParentTree();
    if ($curart->getValue('name') != $curart->getValue('catname'))
    {
      array_push($parents, $curart);
    }

    if (empty($parents))
    {
      $parents[0]=$curart;
    }
    else
    {
      $parents = array_reverse($parents);
    }

    // BREADCRUMB TITLE
    $B = '';
    foreach ($parents as $parent)
    {
      if (OOArticle::isValid($parent))
      {
        $B .= ' - '.$parent->getValue('name');
      }
      elseif (OOCategory::isValid($parent))
      {
        $B .= ' - '.$parent->getValue('catname');
      }
    }
    $B = trim($B," -");

    // CATEGORY TITLE
    $C = $curart->getValue('catname');

    // ARTICLE TITLE
    $N = $curart->getValue('name');

    // SERVERNAME
    $S = $this->servername!='' ? $this->servername : $this->http_host;

    // CUSTOM REXSEO TITLE OVERRIDES ANY OTHER TITLE
    if($art_rexseo_title!='')
    {
      $title_schema = $art_rexseo_title;
    }

    // APPLY SCHEMA
    $title = str_replace(array('%B','%N','%S','%C'),array($B,$N,$S,$C),$title_schema);

    return self::encode_string($title);
  }


  function get_keywords($article_id=null)
  {
    $article_id = !$article_id ? $this->article_id : (int) $article_id;

    $keys = self::getMetaField($article_id,"art_keywords",$this->def_keys[$this->clang]);
    $keys = self::sanitize_keywords($keys);
    $keys = self::encode_string($keys);
    return $keys;
  }


  function get_description($article_id=null)
  {
    $article_id = !$article_id ? $this->article_id : (int) $article_id;

    $desc = self::getMetaField($article_id,"art_description",$this->def_desc[$this->clang]);
    $desc = str_replace(array("\r","\n"),' ',$desc);
    $desc = trim($desc);

    return self::encode_string($desc);
  }


  function get_canonical($article_id=null)
  {
    if(isset($_SERVER['REQUEST_URI']) && $article_id==null)
    {
      $canonical = preg_replace('/[?|'.$this->params_starter.'].*/','',$_SERVER['REQUEST_URI']);
      $article_id = $this->article_id;
    }
    else
    {
      $article_id = !$article_id ? $this->article_id : (int) $article_id;
      $canonical = rex_getURL($article_id,$this->clang);
    }
    $canonical = self::getMetaField($article_id,'art_rexseo_canonicalurl',$canonical);

    return $this->protocol.$this->http_host.'/'.ltrim($canonical,'/');
  }

  function get_base()
  {
    return $this->base_url;
  }


  private function get_protocol()
  {
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
  }


  private function encode_string($str)
  {
    switch($this->encoder)
    {
      case'htmlentities':
          return $this->rex_is_iso ? htmlentities($str,ENT_QUOTES) : htmlentities($str,ENT_QUOTES,'UTF-8');
      break;
      case'htmlspecialchars':
          return $this->rex_is_iso ? htmlspecialchars($str,ENT_QUOTES) : htmlspecialchars($str,ENT_QUOTES,'UTF-8');
      break;
    }
  }


  public function set_encode($type)
  {
    switch($type)
    {
      case'htmlentities':
        $this->encoder = 'htmlentities';
      break;

      default:
        $this->encoder = 'htmlspecialchars';
    }
  }


  private function sanitize_keywords($keys)
  {
    $keys = str_replace(array("\r","\n"),' ',$keys);
    $keys = explode(',',$keys);
    foreach ($keys as $k=>$v)
    {
      $keys[$k] = trim($v);
      if($keys[$k]=='')
      {
        unset($keys[$k]);
      }
    }
    $keys = implode(',',$keys);
    return $keys;
  }


  function getMetaField($article_id,$metafield="file",$defval="",$loop="")
  {
    $meta = OOArticle::getArticleById($article_id);
    $value = '';

    if (($meta->getValue($metafield))!="")
    {   $value=$meta->getValue($metafield);
    }
    else
    {  if ($loop=="LOOP") {
        $cat = OOCategory::getCategoryById($article_id);

        if ($cat->getParent())
        {  $cat = $cat->getParent();

          $value=self::getMetaField($cat->getValue('id'),$metafield,$defval,$loop);
        }
      }
    }

    if ($value == '')
    {
      $value = $defval;
    }

    return $value;
  }

}
?>