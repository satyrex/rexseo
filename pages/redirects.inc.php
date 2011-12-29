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

// GET PARAMS
////////////////////////////////////////////////////////////////////////////////
$mypage    = rex_request('page'   , 'string');
$subpage   = rex_request('subpage', 'string');
$minorpage = rex_request('minorpage', 'string');
$func      = rex_request('func'   , 'string');
$id        = rex_request('id', 'int');


// SETTINGS
/////////////////////////////////////////////////////////////////////////////////
$table      = $REX['TABLE_PREFIX'].'rexseo_redirects';
$pagination = 30;


// BATCH SUBMIT
/////////////////////////////////////////////////////////////////////////////////
if($func=='batch-submit')
{
  $batch = rex_request('redirects','string','false'); 
  if($batch!='false')
  {
    $db = new rex_sql;
    $qry = 'INSERT INTO `rex_rexseo_redirects` (`id`, `createdate`, `updatedate`, `creator`, `status`, `from_url`, `to_article_id`, `to_clang`, `http_status`) VALUES';
    $batch = rexseo_301_2_array($batch);
    $date = time();
    foreach($batch as $k=>$v)
    {
      $qry .= PHP_EOL.'(\'\', \''.$date.'\', \''.$date.'\', \''.$REX['USER']->getValue('login').'\', 1, \''.$k.'\', '.$v['article_id'].', '.$v['clang'].', 301),';
    }
    $qry = rtrim($qry,',').';';

    if($db->setQuery($qry))
      echo rex_info('Weiterleitungen wurden in Tabelle gespeichert.');
  }
}


// LIST & FORM
/////////////////////////////////////////////////////////////////////////////////
if($func == '' || $func=='batch-submit')
{
  /* LISTE ------------------------------------------------------------------ */
   echo '<div class="rex-addon-output">
   <h2 class="rex-hl2">Redirects <span style="color:silver;font-size:12px;">(DB Tabelle: '.$table.')</span></h2>';

  $query = 'SELECT `id`, `from_url`, `status`, `to_article_id`, `to_clang`, `http_status`, `createdate`, `updatedate`, `creator` FROM '.$table.' ORDER BY `createdate` DESC';
  $list = new rex_list($query,$pagination,'data');
  $list->debug = false;


  $imgHeader = '<a href="'. $list->getUrl(array('func' => 'add')) .'"><img src="media/metainfo_plus.gif" alt="add" title="add" /></a>';
  $list->addColumn($imgHeader,'<img src="media/metainfo.gif" alt="field" title="field" />',0,array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($imgHeader,array('func' => 'edit', 'id' => '###id###'));


  $list->removeColumn     ('id'            );
  $list->setColumnSortable('from_url'      );
  $list->setColumnSortable('status'        );

  $list->addColumn        ('target','',4);
  //$list->setColumnSortable('target'        );

  $list->setColumnSortable('to_article_id' );
  $list->setColumnSortable('to_clang'      );
  $list->setColumnSortable('http_status'   );
  $list->removeColumn     ('createdate'    );
  $list->removeColumn     ('updatedate'    );
  $list->removeColumn     ('expiredate'    );
  $list->removeColumn     ('creator'       );


  $list->setColumnLabel('id'            ,'ID');
  $list->setColumnLabel('from_url'      ,'alte URL');
  $list->setColumnLabel('status'        ,'Status');
  $list->setColumnLabel('target'        ,'Ziel-Artikel');
  $list->setColumnLabel('to_article_id' ,'ID');
  $list->setColumnLabel('to_clang'      ,'CLANG');
  $list->setColumnLabel('http_status'   ,'HTTP Status');
  $list->setColumnLabel('creator'       ,'Erzeuger');

  function list_status()
  {
    global $list;
    $str = $list->getValue('status')==1 ? '<span style="color:#107C2C;">aktiv ('.$list->getValue('http_status').')</span>' : '<span style="color:#EA1144;">inaktiv</span>';
    return $str;
  }
  $list->setColumnFormat('status'  ,'custom', 'list_status');

  function list_target()
  {
    global $list;
    return urldecode(rex_getUrl($list->getValue('to_article_id'),$list->getValue('to_clang')));
  }
  $list->setColumnFormat('target'  ,'custom', 'list_target');

  $list->setColumnParams('id'            ,array('func' => 'edit', 'id' => '###id###'));
  $list->setColumnParams('from_url'      ,array('func' => 'edit', 'id' => '###id###'));
  $list->setColumnParams('status'        ,array('func' => 'edit', 'id' => '###id###'));
  $list->setColumnParams('target'        ,array('func' => 'edit', 'id' => '###id###'));
  $list->setColumnParams('to_article_id' ,array('func' => 'edit', 'id' => '###id###'));
  $list->setColumnParams('to_clang'      ,array('func' => 'edit', 'id' => '###id###'));
  $list->setColumnParams('http_status'   ,array('func' => 'edit', 'id' => '###id###'));
  $list->show();

  echo '</div>';
  
  // FORM
////////////////////////////////////////////////////////////////////////////////
echo '

<div class="rex-addon-output">
  <div class="rex-form">

  <form action="index.php" method="post">
    <input type="hidden" name="page"                   value="rexseo" />
    <input type="hidden" name="subpage"                value="redirects" />
    <input type="hidden" name="func"                   value="batch-submit" />

      <fieldset class="rex-form-col-1">
        <legend style="font-size: 1.333em;color: #336699;">Batch Submit</legend>
        <div class="rex-form-wrapper">

          <div class="rex-form-row">
            <p class="rex-form-col-a rex-form-select">
              <label for="robots" class="helptopic">Weiterleitungen:<br /> <br /><em style="color:gray;font-size:10px;">url article_id clang<br />z.B. foo/bar.html 4 0</em></label>
              <textarea id="rexseo_redirects" name="redirects"></textarea>
            </p>
          </div><!-- /rex-form-row -->

        </div><!-- /rex-form-wrapper -->

        <div class="rex-form-wrapper">

          <div class="rex-form-row rex-form-element-v2">
            <p class="rex-form-submit">
              <input class="rex-form-submit" type="submit" id="sendit" name="sendit" value="Weiterleitungen einfügen" />
            </p>
          </div><!-- /rex-form-row -->

        </div><!-- /rex-form-wrapper -->
      </fieldset>

  </form>
  </div><!-- /rex-addon-output -->
</div><!-- /rex-form -->
';
  
  
  
}

elseif($func == 'edit' || $func == 'add')
{
  /* ADD/EDIT FORMULAR ------------------------------------------------------ */

  echo '<div class="rex-addon-output">';

  // Pberschrift je nach Funktion ADD/EDIT
  if($func == 'edit')
  {
    echo '<h2 class="rex-hl2">Redirect bearbeiten <span style="color:silver;font-size:12px;">(ID: '.$id.')</span></h2>';
  }
  else
  {
    echo '<h2 class="rex-hl2">Neuen Datensatz anlegen</h2>';
  }


  $form = new rex_form($table,'Redirect','id='.$id,'post',false);


  $field =& $form->addSelectField('status');
  $field->setLabel('Status');
  $select =& $field->getSelect();
  $select->setSize(1);
  $select->addOption('aktiv',1);
  $select->addOption('inaktiv',0);

  $field = &$form->addTextField('from_url');
  $field->setLabel('alte URL');

  $field = &$form->addLinkmapField('to_article_id');
  $field->setLabel('umleiten nach');


  $field =& $form->addSelectField('http_status');
  $field->setLabel('HTTP Status');
  $select =& $field->getSelect();
  $select->setSize(1);
  $select->addOption('301',301);
  $select->addOption('302',302);

  $field = &$form->addTextField('to_clang',null,array('style'=>'display:none;'));
  $stored_clang = $form->getElement('Redirect','to_clang')->value;

  $form->addFieldset('Infos');

  $field = &$form->addReadOnlyField('createdate',null,array('class'=>'rex-form-read unix-date'));
  $field->setLabel('Erstellt');

  $field = &$form->addReadOnlyField('updatedate',null,array('class'=>'rex-form-read unix-date'));
  $field->setLabel('Geändert');

  $field = &$form->addTextField('expiredate',null,array('class'=>'rex-form-text unix-date-picker'));
  $field->setLabel('Verfallsdatum');

  $field = &$form->addReadOnlyField('creator');
  $field->setLabel('Ersteller');
   

  if($func == 'edit')
  {
    $form->addParam('id', $id);
    $form->addParam('updatedate', time());
  }

  if($func == 'add')
  {
    $form->addParam('updatedate', time());
    $form->addParam('createdate', time());
  }

  $form->show();

  echo '</div>

<div id="clang-hack" clang="" article_id="" style="/*display:none*/">
<p id="clang-link-buttons" class="rex-widget-icons rex-widget-1col">
  <span class="rex-widget-column rex-widget-column-first">
  <span style="float:left;margin-top:3px;">clang:</span>
  ';

foreach($REX['CLANG'] as $id=>$name)
{
  $add_css = $stored_clang==$id ? ' current':'';
  echo '  <a tabindex="35" title="Link auswählen" clang="'.$id.'" onclick="openLinkMap(\'LINK_1\', \'&amp;clang='.$id.'&amp;category_id=0\');return false;" class="rex-icon-file-open open-clang-linkmap'.$add_css.'" href="#">'.$id.'</a>
';
}

echo '
  <a tabindex="36" title="Ausgewählten Link löschen" onclick="deleteREXLink(1);return false;" class="rex-icon-file-delete" href="#"></a>
  </span>
  </p>
</div>

<script type="text/javascript">
<!--
jQuery(function($) {


  jQuery(document).ready(function() {
    $(\'p.rex-widget-icons\').replaceWith($(\'p#clang-link-buttons\'));

    $(\'#clang-hack\').attr(\'article_id\',$(\'#LINK_1\').val());
    $(\'#clang-hack\').attr(\'clang\',$(\'#rex_rexseo_redirects_Redirect_to_clang\').val());

    $(document).focus(function(){
      if($(\'#LINK_1\').val() != $(\'#clang-hack\').attr(\'article_id\')){
        $(\'#rex_rexseo_redirects_Redirect_to_clang\').val($(\'#clang-hack\').attr(\'clang\'));
      }
    });

    $("span.rex-form-read.unix-date").each(function() {
      d = new Date($(this).html() * 1000);
      $(this).html(d.getDate()+"."+d.getMonth()+"."+d.getFullYear()+" - "+d.getHours()+":"+d.getMinutes()+"h");
    });

    $("#rex_rexseo_redirects_Infos_expiredate").css("display","none");
    v = $("#rex_rexseo_redirects_Infos_expiredate").val();
    d = new Date(v * 1000);
    $(".unix-date-picker").append(\'<input type="text" style="width:100px" id="formated-date" value="\'+d.getDate()+"."+d.getMonth()+"."+d.getFullYear()+\'" /> (DD.MM.YYYY)\');

  }); //jQuery(document).ready(function()


  $(\'.open-clang-linkmap\').click(function(){
    $(\'#clang-hack\').attr(\'clang\',$(this).attr(\'clang\'));
    });


  $(\'input#formated-date\').change(function(){
    u = $(this).val().split(".");
    if(u[0]<10) u[0]="0"+u[0];
    if(u[1]<10) u[1]="0"+u[1];console.log(u);
    d = new Date(u[2],u[1],u[0]);
    $("#rex_rexseo_redirects_Infos_expiredate").val(d.getTime()/1000);
    });


}); // jQuery(function($)
//-->
</script>
';
}
?>