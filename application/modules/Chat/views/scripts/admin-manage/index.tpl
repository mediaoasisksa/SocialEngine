<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9915 2013-02-15 01:30:19Z alex $
 * @author     John
 */
?>
<h2><?php echo $this->translate('Chat Plugin') ?></h2>
<?php if( engine_count($this->navigation) ): ?>
<div class='tabs'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
</div>
<?php endif; ?>
<p>
  <?php echo $this->translate('Chat room page description.') ?>
</p>

<div>
  <!--
  <div>
    <?php $itemCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%d chat room","%d chat rooms", $itemCount), $itemCount) ?>
  </div>
  <?php echo $this->paginationControl($this->paginator); ?>
  -->
</div>

<br />

<div class="admin_fields_options">
  
  <?php echo $this->htmlLink(array('action' => 'create', 'reset' => false), $this->translate('Create Room'), array(
      'class' => 'buttonlink admin_chat_addroom smoothbox',
      'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Chat/externals/images/admin/add.png);'
      )) ?>
</div>
<br />
<?php
$settings = Engine_Api::_()->getApi('settings', 'core');
if( $settings->getSetting('user.support.links', 0) == 1 ) {
	echo '     More info: <a href="https://community.socialengine.com/blogs/597/48/chat" target="_blank">See KB article</a>.';
} 
?>		
<br />

<br />

<table class='admin_table admin_responsive_table'>
  <thead>
    <tr>
      <th>Title</th>
      <th style='width: 1%;'><?php echo $this->translate('Users In Room') ?></th>
      <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate('Options') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php if( engine_count($this->paginator) ): ?>
      <?php foreach( $this->paginator as $room ): ?>
        <tr>
          <td data-label="TITLE"  class='admin_table_bold'><?php echo $room->title ?></td>
          <td data-label="<?php echo $this->translate('Users In Room') ?>"><?php echo $room->user_count //'0 <= x <= infinity' ?></td>
          <td class='admin_table_options'>
            <?php echo $this->htmlLink(array('module'=>'chat','controller'=>'manage','id'=>$room->room_id,'action'=>'edit'),   
                                       $this->translate('edit'),
                                       array('class'=>'smoothbox')) ?>
            
            <?php echo $this->htmlLink(array('module'=>'chat','controller'=>'manage','id'=>$room->room_id,'action'=>'delete'), 
                                       $this->translate('delete'),
                                       array('class'=>'smoothbox')) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<br/>
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
