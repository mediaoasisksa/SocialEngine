<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2015-1-22 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<?php
$firstRowWidth = ($this->width);
$firstRowHeight = ($this->height) * 3;
$secondRowWidth = ($this->width);
$secondRowHeight = (($this->height) * 3 ) / 2;
$thirdRowWidth = $this->width;
$thirdRowHeight = $this->height;
?>
<?php $i = 1; ?>
<ul>
    <?php foreach ($this->categories as $category) : ?>

        <?php if ($i == 1 || $i == 2 || $i == 4) : ?>
            <?php
            switch ($i) {
                case 1 :
                    $width = $firstRowWidth;
                    $height = $firstRowHeight;
                    break;
                case 2 :
                    $width = $secondRowWidth;
                    $height = $secondRowHeight;
                    break;
                case 4 :
                    $width = $thirdRowWidth;
                    $height = $thirdRowHeight;
                    break;
                default :
                    $width = $thirdRowWidth;
                    $height = $thirdRowHeight;
            }
            ?>
            <li>
            <?php endif; ?>

            <?php $src = $this->storage->get($category->video_id, '')->getPhotoUrl(); ?>
            <?php $htmlImage = "<i style='background-image:url(".$src.")'></i>"; ?>
            <?php $link = ""; ?>
            <?php if ($category->cat_dependency == 0 && $category->subcat_dependency == 0): ?>
                <?php
                if ($this->contentModuleSponsoredCategories == 'sitevideo_video') {
                    $url = $this->url(array('category_id' => $category->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_video_category', $category->category_id)->getCategorySlug()), Engine_Api::_()->sitevideo()->getVideoCategoryHomeRoute());
                    $link = $this->htmlLink($url, $htmlImage);
                } else {
                    $url = $this->url(array('category_id' => $category->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $category->category_id)->getCategorySlug()), Engine_Api::_()->sitevideo()->getCategoryHomeRoute());
                    $link = $this->htmlLink($url, $htmlImage);
                }
                ?>
               
            <?php endif; ?>
            <div onclick="window.location = '<?php echo $url; ?>'" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;cursor:pointer;" class="seao_sponscat_thumb_wrap">
                 <?php echo $link; ?>
                <?php if ($category->cat_dependency == 0 && $category->subcat_dependency == 0): ?>
                    <div class="seao_sponscat_info">
                        <span class='seao_sponscat_title'><?php echo $this->translate($category->category_name); ?></span>
                        <?php if ($category->featured_tagline): ?>
                            <div class="seao_sponscat_tagline"><?php echo $category->featured_tagline; ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($i == 1 || $i == 3 || $i == 6) : ?>
            </li>
        <?php endif; ?>
        <?php $i++; ?>
    <?php endforeach; ?>

    <?php for ($start = $i; $start <= 6; $start++): ?>
        <?php if ($start == 1 || $start == 2 || $start == 4) : ?>
            <?php
            switch ($start) {
                case 1 :
                    $width = $firstRowWidth;
                    $height = $firstRowHeight;
                    break;
                case 2 :
                    $width = $secondRowWidth;
                    $height = $secondRowHeight;
                    break;
                case 4 :
                    $width = $thirdRowWidth;
                    $height = $thirdRowHeight;
                    break;
                default :
                    $width = $thirdRowWidth;
                    $height = $thirdRowHeight;
            }
            ?>
            <li>
            <?php endif; ?>
            <div style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;border:1px solid rgba(0, 0, 0, 0.1)" class="seao_sponscat_thumb_wrap"></div>
            <?php if ($start == 1 || $start == 3 || $start == 6) : ?>
            </li>
        <?php endif; ?>
    <?php endfor; ?>
</ul>

