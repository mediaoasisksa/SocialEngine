<?php $height = Engine_Api::_()->getApi('settings', 'core')->getSetting('hpbblock.height', 300);?>
<?php $width = Engine_Api::_()->getApi('settings', 'core')->getSetting('hpbblock.width', 33.3);?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Hpbblock/externals/styles/lity.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hpbblock/externals/scripts/lity.js'); ?>
<div class="hp_banner">
	<ul>
  <?php foreach( $this->paginator as $item ): ?>
    <?php if(strpos($item->getCTAHref(), "youtube")):?>
      <li style="width: <?php echo $width;?>%; height: <?php echo $height;?>px;"><article><a href="<?php echo $item->getCTAHref();?>" data-lity=""><img src="<?php echo $item->getPhotoUrl();?>" /></a></article></li>
    <?php else:?>
      <li style="width: <?php echo $width;?>%; height: <?php echo $height;?>px;"><article><a target="_blank" href="<?php echo $item->getCTAHref();?>"><img src="<?php echo $item->getPhotoUrl();?>" /></a></article></li>
    <?php endif;?>
  <?php endforeach;?>
	</ul>
</div>
