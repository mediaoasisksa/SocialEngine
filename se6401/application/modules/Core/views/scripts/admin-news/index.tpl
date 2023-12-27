<?php
   /**
    * SocialEngine
    *
    * @category   Application_Core
    * @package    Core
    * @copyright  Copyright 2006-2020 Webligo Developments
    * @license    http://www.socialengine.com/license/
    * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
    * @author     John Boehr <john@socialengine.com>
    */
   ?>
<?php if (!empty($this->channel)) { ?>
<div class="admin_home_news">
   <h3 class="header_section_second">
      <?php echo $this->translate("News & Updates") ?>
   </h3>
   <div class="row admin_home_news_wrapper">
      <?php foreach ($this->channel['items'] as $item) { ?>
      <div class="col-md-6 admin_home_news_main">
         <div class="admin_home_news_item">
            <div class="admin_home_news_date">
               <?php echo $this->locale()->toDate(strtotime($item['pubDate']), array('size' => 'long')) ?>
            </div>
            <div class="admin_home_news_info">
               <a href="<?php echo @$item['link'] ? $item['link'] : $item['guid'] ?>" target="_blank">
               <?php echo $item['title'] ?>
               </a>
               <span class="admin_home_news_blurb">
               <?php echo $this->string()->truncate($this->string()->stripTags($item['description']), 350) ?>
               </span>
            </div>
         </div>
      </div>
      <?php } ?>
      <div class="admin_home_news_info_button">
         <a href="https://blog.socialengine.com/" target="_blank"><?php echo $this->translate("More SE News") ?> &#187;</a>
      </div>
   </div>
</div>
<?php } else { ?>
<?php } ?>