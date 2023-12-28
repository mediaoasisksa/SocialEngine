<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Seaocore
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>

<script type="text/javascript">
	var seaocore_content_type = '<?php echo $this->resource_type; ?>';
	var seaocore_favourite_url = en4.core.baseUrl + 'seaocore/favourite/favourite';
</script>

<?php if(!empty($this->viewer_id)): ?>
	<?php $hasFavourite = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite($this->resource_type, $this->resource_id); ?>
	<div class="seaocore_favourite_button" id="<?php echo $this->resource_type; ?>_unfavourites_<?php echo $this->resource_id;?>" style ='display:<?php echo $hasFavourite ?"inline-block":"none"?>' >
		<a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
			<i class="seaocore_like_thumbdown_icon"></i>
			<span><?php echo $this->translate('Unfavourite') ?></span>
		</a>
	</div>
	<div class="seaocore_favourite_button" id="<?php echo $this->resource_type; ?>_most_favourites_<?php echo $this->resource_id;?>" style ='display:<?php echo empty($hasFavourite) ?"inline-block":"none"?>'>
		<a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $this->resource_id; ?>', '<?php echo $this->resource_type; ?>');">
			<i class="seaocore_like_thumbup_icon"></i>
			<span><?php echo $this->translate('Favourite') ?></span>
		</a>
	</div>
	<input type ="hidden" id = "<?php echo $this->resource_type; ?>_favourite_<?php echo $this->resource_id;?>" value = '<?php echo $hasFavourite ? $hasFavourite[0]['favourite_id'] :0; ?>' />
<?php endif; ?>