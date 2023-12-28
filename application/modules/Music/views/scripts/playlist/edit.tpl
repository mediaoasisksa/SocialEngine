<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: edit.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */
$songs = $this->playlist->getSongs();
?>
<?php
  $headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile($this->layout()->staticBaseUrl.'application/modules/Core/externals/scripts/create_edit_category.js');
?>
<script type="text/javascript">
  var modulename = 'music';
  en4.core.runonce.add(function() {
    <?php if(isset($this->category_id) && $this->category_id != 0) { ?>
      showSubCategory('<?php echo $this->category_id; ?>','<?php echo $this->subcat_id; ?>');
    <?php } else { ?>
      if(document.getElementById('subcat_id-wrapper'))
        document.getElementById('subcat_id-wrapper').style.display = "none";
    <?php } ?>

    <?php if(isset($this->subsubcat_id)) { ?>
      <?php if(isset($this->subcat_id) && $this->subcat_id != 0) { ?>
        showSubSubCategory('<?php echo $this->subcat_id; ?>' ,'<?php echo $this->subsubcat_id; ?>');
      <?php } else { ?>
        if(document.getElementById('subsubcat_id-wrapper'))
          document.getElementById('subsubcat_id-wrapper').style.display = "none";
      <?php } ?>
    <?php } else { ?>
      if(document.getElementById('subsubcat_id-wrapper'))
        document.getElementById('subsubcat_id-wrapper').style.display = "none";
    <?php } ?>
  });
</script>
<div class="layout_middle">
  <div class="generic_layout_container">
     <div class="headline">
      <h2>
       <?php echo $this->translate('Music');?>
      </h2>
      <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
      </div>
    </div>
  </div>
</div>
<div class="layout_middle">
  <div class="generic_layout_container">
<?php echo $this->form->render($this) ?>

<div style="display:none;">
  <?php if (!empty($songs)): ?>
    <ul id="music_songlist">
      <?php foreach ($songs as $song): ?>
      <li id="song_item_<?php echo $song->song_id ?>" class="file file-success">
        <a href="javascript:void(0)" class="file-remove" data-file_id="<?php echo $song->song_id ?>">
          <b><?php echo $this->translate('Remove') ?></b>
        </a>
        <span class="file-name">
          <?php echo $song->getTitle() ?>
        </span>
        (<a href="javascript:void(0)" class="song_action_rename file-rename"><?php echo $this->translate('rename') ?></a>)
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
</div>
</div>
<script type="text/javascript">
//<![CDATA[
  en4.core.runonce.add(function() {
    try {
    new Uploader('#upload_file', {
      uploadLinkClass : 'buttonlink icon_music_new',
      uploadLinkTitle : '<?php echo $this->translate("Add Music");?>',
      uploadLinkDesc : '<?php echo $this->translate("_MUSIC_UPLOAD_DESCRIPTION");?>'
    });
    //$('save-wrapper').inject($('art-wrapper'), 'after');

    // IMPORT SONGS INTO FORM
    if (scriptJquery('#music_songlist li.file').length) {
      scriptJquery('#music_songlist li.file').appendTo(scriptJquery('#uploaded-file-list'));
      scriptJquery('#uploaded-file-list li span.file-name').css('cursor', 'move');
      scriptJquery('#uploaded-file-list').css('display', 'block');
      scriptJquery('#remove_all_files').css('display', 'inline');
    }

    // SORTABLE PLAYLIST
    scriptJquery('#uploaded-file-list').sortable({
        helper: "clone",
        handle : 'span',
        stop: function( event, ui ) {
          scriptJquery.ajax({
            url: '<?php echo $this->url(array('controller'=>'playlist','action'=>'sort'), 'music_extended') ?>',
            noCache: true,
            dataType: 'json',
            method : 'post',
            data: {
              'format': 'json',
              'playlist_id': <?php echo $this->playlist->playlist_id ?>,
              'order': scriptJquery('#uploaded-file-list li').map((i,ele)=> scriptJquery(ele).attr("id")).toArray().join()
            }
          });
        }
    });

    // RENAME SONG
    scriptJquery('a.song_action_rename').on('click', function(){
      var origTitle = scriptJquery(this).parents('li:first').find('.file-name').text();
          origTitle = origTitle.substring(0, origTitle.length-6);
      var newTitle  = prompt('<?php echo $this->translate('What is the title of this song?') ?>', origTitle);
      var song_id   = song_action_rename(this).parents('li:first').id.split(/_/);
          song_id   = song_id[ song_id.length-1 ];

      if (newTitle && newTitle.length > 0) {
        newTitle = newTitle.substring(0, 60);
        scriptJquery(this).parents('li:first').find('.file-name').text(newTitle);
        scriptJquery.ajax({
          url: '<?php echo $this->url(array('controller'=>'song','action'=>'rename'), 'music_extended') ?>',
          method:'post',
          dataType: 'json',
          data: {
            format: 'json',
            song_id: song_id,
            playlist_id: <?php echo $this->playlist->playlist_id ?>,
            title: newTitle
          }
        });
      }
      return false;
    });

    // REMOVE/DELETE SONG FROM PLAYLIST
    scriptJquery('a.file-remove').on('click', function() {
      deleteFile(scriptJquery(this));
    });
  } catch(error){ console.log(error,"error"); }


  });

  var deleteFile = function (el) {
    var song_id = el.attr('data-file_id');
    el.parents('li:first').remove();
    scriptJquery.ajax({
      url: '<?php echo $this->url(array('controller'=>'song','action'=>'delete'), 'music_extended') ?>',
      method:'post',
      dataType:'json',
      data: {
        format: 'json',
        song_id: song_id,
        playlist_id: <?php echo $this->playlist->playlist_id ?>
      }
    });
  }
//]]>
</script>


<script type="text/javascript">
  scriptJquery('.core_main_music').parent().addClass('active');
</script>
