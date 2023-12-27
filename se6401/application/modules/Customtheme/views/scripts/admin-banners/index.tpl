<?php
/**
* SocialEngine
*
* @category   Application_Core
* @package    Core
* @copyright  Copyright 2006-2010 Webligo Developments
* @license    http://www.socialengine.com/license/
* @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
* @author     John
*/
?>


<h2>
  <?php echo $this->translate('HPB Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<br />
<div>
    <?php echo $this->htmlLink(array('action' => 'create', 'reset' => false),
    $this->translate("Create New Banner"), array(
    'class' => 'buttonlink',
    'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Announcement/externals/images/admin/add.png);')) ?>
</div>

<br/>
<div class='admin_results'>
   <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s banner found", "%s banners found", $count), $count) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<table class='admin_table'>
    <thead>
        <tr>
            <th style="width: 1%;">
                <?php echo $this->translate("ID") ?>
            </th>
             <th>
                <?php echo $this->translate("Image") ?>
            </th>
            <th>
                <?php echo $this->translate("URL") ?>
            </th>
            <th style="width: 1%;">
                <?php echo $this->translate("Options") ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $this->paginator as $item ): ?>
        <tr>
            <td><?php echo $item->banner_id ?></td>
            <td><img src="<?php echo $item->getPhotoUrl();?>" style="width:200px; height: 100px;"/></td>
            <td style="white-space: normal;"><?php echo $item->getCTAHref() ? $item->getCTAHref() : '-' ?></td>
            <td class="admin_table_options">
                <a href='<?php echo $this->url(array('action' => 'edit', 'id' => $item->banner_id)) ?>'>
                   <?php echo $this->translate("edit") ?>
                </a> 
                <?php if($item->custom): ?>
                |
                <a class='smoothbox' href='<?php echo $this->url(array('action' => 'delete', 'id' => $item->banner_id)) ?>'>
                   <?php echo $this->translate("delete") ?>
                </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else:?>

<div class="tip">
    <span><?php echo $this->translate("There are no banners created.") ?></span>
</div>
<?php endif; ?>
