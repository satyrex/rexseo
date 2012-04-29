<?php
/**
 * RexSEO - URLRewriter Addon
 *
 * @link https://github.com/gn2netwerk/rexseo
 *
 * @author dh[at]gn2-netwerk[dot]de Dave Holloway
 * @author code[at]rexdev[dot]de jdlx
 *
 * Based on url_rewrite Addon by
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4.3.x
 * @version 1.4.283
 */

if (!function_exists('rexseo_recursive_copy'))
{
  function rexseo_recursive_copy($source, $target, $makedir=TRUE, &$result=array(), $counter=1, $folderPermission='', $filePermission='')
  {
    global $REX;
    $folderPermission = (empty($folderPermission)) ? $REX['DIRPERM'] : $folderPermission;
    $filePermission = (empty($filePermission)) ? $REX['FILEPERM'] : $filePermission;

    // SCAN SOURCE DIR WHILE IGNORING  CERTAIN FILES
    $ignore = array('.DS_Store','.svn','.','..');
    $dirscan = array_diff(scandir($source), $ignore);

    // WALK THROUGH RESULT RECURSIVELY
    foreach($dirscan as $item)
    {

      // DO DIR STUFF
      if (is_dir($source.$item)) /* ITEM IS A DIR */
      {
        if(!is_dir($target.$item) && $makedir=TRUE) /* DIR NONEXISTANT IN TARGET */
        {
          if(mkdir($target.$item)) /* CREATE DIR IN TARGET */
          {
            if(chmod($source.$item,$folderPermission))
            {
            }
            else
            {
              echo rex_warning('Rechte für "'.$target.$item.'" konnten nicht gesetzt werden!');
            }
          }
          else
          {
            echo rex_warning('Das Verzeichnis '.$source.$item.' konnte nicht angelegt werden!');
          }
        }

        // RECURSION
        rexseo_recursive_copy($source.$item.'/', $target.$item.'/', $makedir, $result, $counter);
      }

      // DO FILE STUFF
      elseif (is_file($source.$item)) /* ITEM IS A FILE */
      {
        if (rex_is_writable($target)) /* CHECK WRITE PERMISSION */
        {
          if(is_file($target.$item)) /* FILE EXISTS IN TARGET */
          {
            $slug = date("d.m.y_H.i.s_");
            if(!rename($target.$item,$target.$slug.$item))
            {
              echo rex_warning('Datei "'.$target.$item.'" konnte nicht umbenannt werden!');
            }
            else
            {
              if(!copy($source.$item,$target.$item))
              {
                $result[$counter]['path'] = $target;
                $result[$counter]['item'] = $item;
                $result[$counter]['copystate'] = 0;
                echo rex_warning('Datei "'.$target.$item.'" konnte nicht geschrieben werden!');
              }
              else
              {
                $result[$counter]['path'] = $target;
                $result[$counter]['item'] = $item;
                if(chmod($target.$item,$filePermission))
                {
                  $result[$counter]['copystate'] = 1;
                  echo rex_info('Datei "'.$target.$item.'" wurde erfolgreich angelegt.');
                }
                else
                {
                  $result[$counter]['copystate'] = 0;
                  echo rex_warning('Rechte für "'.$target.$item.'" konnten nicht gesetzt werden!');
                }
              }
            }
          }
          else
          {
            if(!copy($source.$item,$target.$item))
            {
              $result[$counter]['path'] = $target;
              $result[$counter]['item'] = $item;
              $result[$counter]['copystate'] = 0;
              echo rex_warning('Datei "'.$target.$item.'" konnte nicht geschrieben werden!');
            }
            else
            {
              $result[$counter]['path'] = $target;
              $result[$counter]['item'] = $item;
              if(chmod($target.$item,$filePermission))
              {
                $result[$counter]['copystate'] = 1;
                echo rex_info('Datei "'.$target.$item.'" wurde erfolgreich angelegt.');
              }
              else
              {
                $result[$counter]['copystate'] = 0;
                echo rex_warning('Rechte für "'.$target.$item.'" konnten nicht gesetzt werden!');
              }
            }
          }
        }
        else
        {
          echo rex_warning('Keine Schreibrechte für das Verzeichnis "'.$target.'" !');
        }
      }
      $counter++;
    }
    return $result;
  }
}



/**
 * CONTENT PARSER FUNKTIONEN
 * @author rexdev.de
 * @package redaxo4.2
 * @version svn:$Id$
 */

// INCLUDE PARSER FUNCTION
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('rexseo_incparse'))
{
  function rexseo_incparse($root,$source,$parsemode,$return=false)
  {

    switch ($parsemode)
    {
      case 'textile':
      $source = $root.$source;
      $new_redirects = file_get_contents($source);
      $html = rexseo_textileparser($new_redirects,true);
      break;

      case 'txt':
      $source = $root.$source;
      $new_redirects = file_get_contents($source);
      $html =  '<pre class="plain">'.$new_redirects.'</pre>';
      break;

      case 'raw':
      $source = $root.$source;
      $new_redirects = file_get_contents($source);
      $html = $new_redirects;
      break;

      case 'php':
      $source = $root.$source;
      $html =  get_include_contents($source);
      break;



      case 'iframe':
      $html = '<iframe src="'.$source.'" width="99%" height="600px"></iframe>';
      break;

      case 'jsopenwin':
      $html = 'Externer link: <a href="'.$source.'">'.$source.'</a>
      <script language="JavaScript">
      <!--
      window.open(\''.$source.'\',\''.$source.'\');
      //-->
      </script>';
      break;

      case 'extlink':
      $html = 'Externer link: <a href="'.$source.'">'.$source.'</a>';
      break;
    }

    if($return)
    {
      return $html;
    }
    else
    {
      echo $html;
    }

  }
}

// TEXTILE PARSER FUNCTION
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('rexseo_textileparser'))
{
  function rexseo_textileparser($textile,$return=false)
  {
    if(OOAddon::isAvailable("textile"))
    {
      global $REX;

      if($textile!='')
      {
        $textile = htmlspecialchars_decode($textile);
        $textile = str_replace("<br />","",$textile);
        $textile = str_replace("&#039;","'",$textile);
        if (strpos($REX['LANG'],'utf'))
        {
          $html = rex_a79_textile($textile);
        }
        else
        {
          $html =  utf8_decode(rex_a79_textile($textile));
        }
        $html = preg_replace('|<span class="caps">([^<]+)</span>|','\1',$html);

        if($return)
        {
          return $html;
        }
        else
        {
          echo $html;
        }
      }

    }
    else
    {
      $html = rex_warning('WARNUNG: Das <a href="index.php?page=addon">Textile Addon</a> ist nicht aktiviert! Der Text wird ungeparst angezeigt..');
      $html .= '<pre>'.$textile.'</pre>';

      if($return)
      {
        return $html;
      }
      else
      {
        echo $html;
      }
    }
  }
}

// ECHO TEXTILE FORMATED STRING
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('echotextile'))
{
  function echotextile($msg) {
    global $REX;
    if(OOAddon::isAvailable("textile")) {
      if($msg!='') {
         $msg = str_replace("	","",$msg); // tabs entfernen
         if (strpos($REX['LANG'],'utf')) {
          echo rex_a79_textile($msg);
        } else {
          echo utf8_decode(rex_a79_textile($msg));
        }
      }
    } else {
      $fallback = rex_warning('WARNUNG: Das <a href="index.php?page=addon">Textile Addon</a> ist nicht aktiviert! Der Text wird ungeparst angezeigt..');
      $fallback .= '<pre>'.$msg.'</pre>';
      echo $fallback;
    }
  }
}


// http://php.net/manual/de/function.include.php
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('get_include_contents'))
{
  function get_include_contents($filename) {
    if (is_file($filename)) {
      ob_start();
      include $filename;
      $new_redirectss = ob_get_contents();
      ob_end_clean();
      return $new_redirectss;
    }
    return false;
  }
}


// REDAXO INSTALL ORDNER ERMITTELN
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('rexseo_subdir'))
{
  function rexseo_subdir()
  {
    global $REX;
    $path_diff = $REX['REDAXO'] ? array('index.php','redaxo'):array('index.php');
    $install_subdir = array_diff_assoc(array_reverse(explode('/',trim($_SERVER['SCRIPT_NAME'],'/'))),$path_diff);
    $rexseo_subdir = count($install_subdir)>0 ? implode('/',array_reverse($install_subdir)).'/' :'';
    return $rexseo_subdir;
  }
}


// PARAMS CAST FUNCTIONS
////////////////////////////////////////////////////////////////////////////////
function rexseo_nl_2_array($str)
{
  $arr = array_filter(preg_split("/\n|\r\n|\r/", $str));
  return is_array($arr) ? $arr : array($arr);
}

function rexseo_array_2_nl($arr)
{
  return count($arr)>0 ? implode(PHP_EOL,$arr) : '';
}

function rexseo_301_2_array($str)
{
  $arr = array();
  $tmp = array_filter(preg_split("/\n|\r\n|\r/", $str));
  foreach($tmp as $k => $v)
  {
    $a = explode(' ',trim($v));
    $arr[trim(ltrim($a[0],'/'))] = array('article_id'=>intval($a[1]),'clang'=>intval($a[2]));
  }
  return $arr;
}

function rexseo_301_2_string($arr)
{
  $str = '';
  foreach($arr as $k => $v)
  {
    $str .= $k.' '.$v['article_id'].' '.$v['clang'].PHP_EOL;
  }
  return $str;
}

function rexseo_batch_cast($request,$conf)
{
  if(is_array($request) && is_array($conf))
  {
    foreach($conf as $key => $cast)
    {
      switch($cast)
      {
        case 'unset':
          unset($request[$key]);
          break;

        case '301_2_array':
          $request[$key] = rexseo_301_2_array($request[$key]);
          break;

        case 'nl_2_array':
          $request[$key] = rexseo_nl_2_array($request[$key]);
          break;

        default:
          $request[$key] = rex_request($key,$cast);
      }
    }
    return $request;
  }
  else
  {
    trigger_error('wrong input type, array expected', E_USER_ERROR);
  }
}


// FIX INTERNAL LAINKAS FOR TINY/TEXTILE
////////////////////////////////////////////////////////////////////////////////
function rexseo_fix_42x_links($params)
{
  global $REX;

  $subdir = $REX['ADDON']['rexseo']['settings']['install_subdir'];
  if($subdir=='')
  {
    $relpath     = '/redaxo/';
    $replacement = '/';
  }
  else
  {
    $relpath     = '/'.$subdir.'redaxo/';
    $replacement = '/'.$subdir;
  }

  // textile, tiny
  return str_replace(
    array('&quot;:'.$relpath, '"'.$relpath),
    array('&quot;:'.$replacement, '"'.$replacement),
    $params['subject']
  );
}


// INJECT 301 URLS INTO REXSEO PATHLIST
function rexseo_inject_301($params)
{
  global $REX;
  $redirects = $REX['ADDON']['rexseo']['settings']['301s'];

  if(count($redirects)>0)
  {
    foreach($redirects as $url => $v)
    {
      if(!isset($params['subject']['REXSEO_URLS'][$url]))
      {
        $params['subject']['REXSEO_URLS'][$url] = array('id'    =>$v['article_id'],
                                                        'clang' =>$v['clang'],
                                                        'status'=>301);
      }
    }
  }

  return $params['subject'];
}


// HTACCESS REDIRECTS UPDATE FUNCTION
////////////////////////////////////////////////////////////////////////////////
function rexseo_htaccess_update_redirects()
{
  global $REX;                                                                  #FB::group(__FUNCTION__);

  $table = $REX['TABLE_PREFIX'].'rexseo_redirects';
  $db = new rex_sql;
  $redirects = array();
  $now = time();
  #$qry = 'SELECT * FROM `'.$table.'` WHERE `status`=1 ORDER BY `createdate` DESC;';
  $qry = 'SELECT * FROM `'.$table.'` ORDER BY `createdate` DESC;';

  foreach($db->getDBArray($qry) as $r)
  {
    $target_url = rex_getUrl($r['to_article_id'],$r['to_clang']);               #FB::log($target_url,'$target_url');
    $from_url   = $r['from_url'];                                               #FB::log($from_url,'$from_url');

    if($from_url==$target_url) /*1:1 loop*/
    {
      rexseo_update_redirect($r['id'],2,'delete');                              #FB::info($r['id'],'$from_url==$target_url');
      continue;
    }
    elseif($from_url=='/' || $from_url=='' || $target_url=='/') /*root loop*/
    {
      rexseo_update_redirect($r['id'],2,'delete');                              #FB::info($r['id'],'$from_url=="/" || $from_url==""');
      continue;
    }
    elseif(isset($redirects[$from_url])) /*duplicate*/
    {
      rexseo_update_redirect($r['id'],3,'update');                              #FB::info($r['id'],'$redirects[$from_url]');
      continue;
    }
    elseif(isset($redirects[$target_url])) /*2nd level loop*/
    {
      rexseo_update_redirect($r['id'],4,'update');                              #FB::info($r['id'],'redirects[$target_url]');
      continue;
    }
    elseif($r['expiredate']<$now) /*expired*/
    {
      rexseo_update_redirect($r['id'],5,'update');                              #FB::info($r['id'],'redirects[$target_url]');
      continue;
    }
    elseif($r['status']==1)
    {
      $redirects[$from_url]=array('http_status'=>$r['http_status'],'target_url'=>$target_url);
    }
  }                                                                             #FB::log($redirects,'$redirects');

  $ht_path = $REX['HTDOCS_PATH'].'.htaccess';

  if(!file_exists($ht_path))
    {
      echo rex_warning('FEHLER: .htaccess wurde nicht unter folgendem Pfad gefunden:<br />
Pfad: "'.$ht_path.'"');
      return false;
    }

  if(count($redirects)>0)
  {
    $new_redirects = '### REXSEO REDIRECTS BLOCK'.PHP_EOL;
    foreach($redirects as $k=>$v)
    {
      $new_redirects .= 'Redirect '.$v['http_status'].' /'.$k.' '.$REX['SERVER'].$v['target_url'].PHP_EOL;
    }
    $new_redirects .= '### /REXSEO REDIRECTS BLOCK';
  }
  else
  {
    $new_redirects = '### REXSEO REDIRECTS BLOCK'.PHP_EOL.'### /REXSEO REDIRECTS BLOCK';
  }

  if($ht_content = rex_get_file_contents($ht_path))
  {
    if(preg_match("@(### REXSEO REDIRECTS BLOCK.*### /REXSEO REDIRECTS BLOCK)@s",$ht_content)!=1)
      echo rex_warning('ACHTUNG: redirects konnten nicht geschrieben werden!<br />
Bitte die .htaccess auf korrektes redirects delimiter Paar überprüfen.<br />
                       (Siehe original RexSEO htaccess: <em style="color:black;">./addons/rexseo/install/files/.htaccess</em> )');

    $new_ht = preg_replace("@(### REXSEO REDIRECTS BLOCK.*### /REXSEO REDIRECTS BLOCK)@s", $new_redirects, $ht_content);
    return rex_put_file_contents($ht_path, $new_ht);
  }                                                                             #FB::groupEnd();
  return false;
}


// UPDATE REDIRECT STATUS OR DELETE
////////////////////////////////////////////////////////////////////////////////
function rexseo_update_redirect($id,$status=2,$func='update')
{
  global $REX;                                                                  #FB::group(__FUNCTION__);

  $table = $REX['TABLE_PREFIX'].'rexseo_redirects';
  $db = new rex_sql;
  switch($func)
  {
  case 'delete':
    #$qry = 'DELETE FROM `'.$table.'` WHERE `id`='.$id.' AND `creator`=\'rexseo\';';
    $qry = 'DELETE FROM `'.$table.'` WHERE `id`='.$id.';';                      #FB::log($qry,'$qry');FB::groupEnd();
    return $db->setQuery($qry);
    break;

  default:
    $qry = 'UPDATE `'.$table.'` SET `status`='.$status.' WHERE `id`='.$id.';';  #FB::log($qry,'$qry');FB::groupEnd();
    return $db->setQuery($qry);
  }
}


// SETUP/REPAIR REXSEO'S METAINFOS
////////////////////////////////////////////////////////////////////////////////
function rexseo_setup_metainfo()
{
  global $REX;

  $install_metas = array(
    'art_rexseo_legend'         => array('RexSEO Rewrite',              'art_rexseo_legend',        100,    '',         12,     '',       '',                                                                                                     '',               ''),
    'art_rexseo_url'            => array('Custom URL',                  'art_rexseo_url',           101,    '',          1,     '',       '',                                                                                                     '',               ''),
    'art_rexseo_canonicalurl'   => array('Custom Canonical URL',        'art_rexseo_canonicalurl',  102,    '',          1,     '',       '',                                                                                                     '',               ''),
    'art_rexseo_title'          => array('Custom Page Title',           'art_rexseo_title',         103,    '',          1,     '',       '',                                                                                                     '',               ''),
    'art_rexseo_sitemap_legend' => array('RexSEO Sitemap',              'art_rexseo_sitemap_legend',104,    '',         12,     '',       '',                                                                                                     '',               ''),
    'art_rexseo_priority'       => array('Sitemap Priority',            'art_rexseo_priority',      105,    '',          3,     '',       ':auto|1.00:1.00|0.80:0.80|0.64:0.64|0.51:0.51|0.33:0.33|0.00:0.00',                                    '',               ''),
    'art_rexseo_changefreq'     => array('Sitemap Changefreq',          'art_rexseo_changefreq',    105,    '',          3,     '',       ':auto|never:never|yearly:yearly|monthly:monthly|weekly:weekly|daily:daily|hourly:hourly|always:always','',               ''),
    'art_rexseo_sitemap_out'    => array('Sitemap Output',              'art_rexseo_sitemap_out',   106,    '',          3,     '',       ':auto|show:show|hide:hide',                                                                            '',               ''),
    );

  $db = new rex_sql;
  foreach($db->getDbArray('SHOW COLUMNS FROM `rex_article` LIKE \'art_rexseo_%\';') as $column)
  {
    unset($install_metas[$column['Field']]);
  }

  foreach($install_metas as $k => $v)
  {
    $db->setQuery('SELECT `name` FROM `rex_62_params` WHERE `name`=\''.$k.'\';');

    if($db->getRows()>0)
    {
      // FIELD KNOWN TO METAINFO BUT MISSING IN ARTICLE..
      $db->setQuery('ALTER TABLE `rex_article` ADD `'.$k.'` TEXT NOT NULL;');
      if($REX['REDAXO'])
      {
        echo rex_info('Metainfo Feld '.$k.' wurde repariert.');
      }
    }
    else
    {
      a62_add_field($v[0], $v[1], $v[2], $v[3], $v[4], $v[5], $v[6], $v[7], $v[8]);
      if($REX['REDAXO'])
      {
        echo rex_info('Metainfo Feld '.$k.' wurde angelegt.');
      }
    }
  }

}