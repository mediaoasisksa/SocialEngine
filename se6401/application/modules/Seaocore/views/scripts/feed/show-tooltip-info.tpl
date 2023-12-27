<div class="seaocore_tooltip">
  <div class="seaocore_tip_container">
    <!--banner section start here-->

    <div class="seaocore_tip_cover_photo"  <?php if( !empty($this->sitemusicCoverPhoto) ): ?> style="background:url('<?php echo $this->sitemusicCoverPhoto ?>')" <?php endif; ?>    <?php if( !empty($this->coverPhoto) ): ?> onclick='openSeaocoreLightBox("<?php echo $this->coverPhoto->getHref(); ?>");' style="background:url('<?php echo $this->coverPhoto->getPhotoUrl() ?>')" <?php endif; ?> >
      <?php if( $this->coreModules->isModuleEnabled('sitemember') && !empty($this->informationArray) && in_array("featured", $this->informationArray) && $this->featured ): ?>
        <div class="sitemember_list_featured_label"><?php echo $this->translate('Featured'); ?>
        </div>
      <?php endif; ?>
      <?php if( $this->coreModules->isModuleEnabled('sitemember') && !empty($this->informationArray) && in_array("sponsored", $this->informationArray) && !empty($this->sponsored) ): ?>
        <div class="sitemember_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>">
          <?php echo $this->translate('Sponsored'); ?>
        </div>
      <?php endif; ?>
    </div>
    <!--banner section end here-->

    <!-- profile details section start here-->
    <div class="seaocore_tip_details">
      <div class="seaocore_cover_overlap">
        <div class="seaocore_tip_user_photo">
          <div class="seaocore_user_profile_photo" style="background:url('<?php echo $this->mainPhotoUrl ?>')"></div>
        </div>
        <div class="seaocore_profile_details">
          <h2><?php echo $this->htmlLink($this->result->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->result->getTitle(), 64), array('class' => 'fleft',)) ?>
            <?php if( $this->resource_type == 'user' && !empty($this->showVerified) ): ?>
              <i class="sitemember_list_verify_label mleft5" title="<?php echo $this->translate('Verified') ?>"></i>
            <?php endif; ?> </h2>
          <ul class="seaocore_profile_type">
            <li>
              <?php if( $this->resource_type == 'sitemusic_artist' ): ?>
                <?php echo $this->translate("Artist"); ?>
              <?php elseif( $this->resource_type == 'sitemusic_pl' ): ?>
                <?php echo $this->translate("Music album"); ?>
              <?php elseif( $this->resource_type == 'sitemusic_playlist_song' ): ?>
                <?php echo $this->translate("Song"); ?>
              <?php elseif( $this->resource_type == 'sitemusic_userpl' ): ?>
                <?php echo $this->translate("Playlist"); ?>
              <?php endif; ?>
              <?php if( $this->resource_type != 'user' && $this->resourceCategoryType && !isset($this->sitemusicItemsArray) ) : ?>
                <?php echo $this->translate($this->resourceCategoryType); ?>
              <?php endif; ?>
              <?php if( !empty($this->route_name) && !empty($this->getCategoryText) && !empty($this->category_id)  ) : ?> &#187;
                <?php echo $this->htmlLink($this->url(array("$this->category_id" => $this->result->category_id, 'categoryname' => Engine_Api::_()->seaocore()->getSlug($this->getCategoryText, 225)), $this->route_name), $this->translate($this->getCategoryText)) ?>
              <?php elseif( $this->getCategoryText ): ?> &#187;
                <?php echo $this->getCategoryText; ?>
              <?php endif; ?>
              <?php if( $this->resource_type == 'user' && $this->coreModules->isModuleEnabled('sitemember') ): ?>
                <?php $userRating = Engine_Api::_()->getDbTable('ratings', 'sitemember')->getNumbersOfUserRating($this->result->getIdentity()); ?>
                <?php if( !empty($this->informationArray) && in_array('rating_star', $this->informationArray) && $userRating ) : ?>
                  <div class="seaocore_tip_other_details">
                    <div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_rating" title="<?php echo $this->translate("Rating") ?>"></i>
                      <div class="o_hidden f_small">
                        <span title="<?php echo $this->translate('Overall Rating: %s', $userRating); ?>">
                          <?php for( $x = 1; $x <= $userRating; $x++ ) { ?>
                            <span class="seao_rating_star_generic rating_star_y" title="<?php echo $this->translate('Overall Rating: %s', $userRating); ?>"></span>
                            <?php
                          }
                          $roundrating = round($userRating);
                          if( ($roundrating - $userRating) > 0 ) {
                            ?>
                            <span class="seao_rating_star_generic rating_star_half_y" title="<?php echo $this->translate('Overall Rating: %s', $userRating); ?>"></span>
                            <?php
                          }
                          $roundrating++;
                          for( $x = $roundrating; $x <= 5; $x++ ) {
                            ?>
                            <span class="seao_rating_star_generic seao_rating_star_disabled" title="<?php echo $this->translate('Overall Rating: %s', $userRating); ?>"></span>
                          <?php } ?>
                        </span>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endif; ?>
            </li>

            <?php if( $this->resource_type == 'user' && !empty($this->informationArray) ): ?>
              <li> <div class="seaocore_tip_other_details"> <?php
                  if( $this->verify_count ): echo $this->verify_count . " " . $this->translate('Verified');
                  endif;
                  if( $this->likeCount ): echo $this->verify_count ? " | " . $this->likeCount : $this->likeCount;
                    echo " " . $this->translate('Like');
                  endif;
                  ?> </div>
                <?php echo $this->memberTipInfo($this->result, $this->informationArray, array('customParams' => $this->customParams, 'custom_field_title' => $this->customfieldtitle, 'custom_field_heading' => $this->customfieldHeading)); ?>
              </li>
            <?php endif; ?>
            <?php if( $this->resource_type == 'sitemusic_pl' || $this->resource_type == 'sitemusic_userpl' ): ?>
              <?php $totalSong = $this->result->totalSongs(); ?>
              <?php if( $totalSong ): ?>
                <li class="seaocore_tip_other_details">
                  <i class="fa fa-music" aria-hidden="true"  title="Count songs"></i>
                  <?php echo $totalSong; ?>
                </li>
              <?php endif; ?>
            <?php endif; ?>
            <!-- //here we show date of event content  -->
            <?php if( $this->resource_type == 'event' && !empty($this->result->starttime) ) : ?>
              <li class="seaocore_tip_date" title="Event Start Time"><?php echo $this->locale()->toDateTime($this->result->starttime); ?></li>
            <?php endif; ?>
            <!-- //here we show date of siteevent content  -->
            <?php if( $this->resource_type == 'siteevent_event' && !empty($this->result->starttime) ) : ?>
              <li class="seaocore_tip_date" title="Event Start Time"><?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
                <?php echo $this->locale()->toEventDateTime($this->result->starttime, array('size' => $datetimeFormat)); ?></li>
            <?php endif; ?>
            <?php if( $this->resource_type == "sitereview_wishlist" ) : ?>
              <li class="tip_main_body_stat">
                <?php if( !empty($this->informationArray) && in_array("rwcreated_by", $this->informationArray) ) : ?>
                  <?php echo $this->translate('Created by %s', $this->result->getOwner()->toString()) ?><br />
                <?php endif; ?>
                <?php if( !empty($this->informationArray) && in_array("rewishlist_item", $this->informationArray) ) : ?>
                  <?php echo $this->translate(array('%s entry', '%s entries', count($this->result)), count($this->result)); ?>
                <?php endif; ?>
              </li>
            <?php endif; ?>
            <?php if( $this->resource_type == 'sitereview_listing' && $this->result->price > '0.00' && !empty($this->informationArray) && in_array("price", $this->informationArray) ) : ?>
              <li class="seaocore_tip_price" title="<?php echo $this->translate('Price'); ?>">
                <span class="discount_value"><?php echo $this->locale()->toCurrency($this->result->price, $this->coreSettings->getSetting('payment.currency', 'USD')) ?></span>
              </li>
            <?php elseif( $this->resource_type == 'sitestoreproduct_product' && $this->result->price > '0.00' && !empty($this->informationArray) && in_array("price", $this->informationArray) ): ?>
              <li class="seaocore_tip_price" title="<?php echo $this->translate('Price'); ?>">
                <?php echo Engine_Api::_()->sitestoreproduct()->getProductDiscount($this->result); ?>
              </li>
            <?php endif; ?>
            <?php if( isset($this->result->photos_count) && ($this->resource_type == 'album' || $this->resource_type == 'sitealbum_album') ): ?>
              <li class="seaocore_tip_photo">
                <?php echo $this->translate(array('%s photo', '%s photos', $this->result->photos_count), $this->result->photos_count) ?>&nbsp;&nbsp;
              </li>
            <?php endif; ?>
            <?php if( $this->resource_type == 'sitereview_listing' && !empty($this->informationArray) && ((!empty($this->result->review_count) && in_array("review_count", $this->informationArray)) || (!empty($userRating) && in_array("rating_count", $this->informationArray))) ) : ?>
              <li class="seaocore_tip_reviews">
                <?php if( !empty($this->result->review_count) && in_array("review_count", $this->informationArray) ) : ?>
                  <?php echo $this->translate(array('%s review', '%s reviews', $this->result->review_count), $this->result->review_count) ?>&nbsp;&nbsp;
                <?php endif; ?>
                <?php if( !empty($userRating) && in_array("rating_count", $this->informationArray) ) : ?>
                  <?php echo $this->translate(array('%s rating', '%s ratings', $userRating), $userRating) ?>
                <?php endif; ?>
              </li>
            <?php endif; ?>
            <?php if( $this->resource_type == 'sitestoreproduct_product' && !empty($this->informationArray) && ((!empty($this->result->review_count) && in_array("storeproductreview_count", $this->informationArray)) || (!empty($userRating) && in_array("storeproductrating_count", $this->informationArray))) ) : ?>
              <li class="tip_main_body_stat">
                <?php if( !empty($this->result->review_count) && in_array("storeproductreview_count", $this->informationArray) ) : ?>
                  <?php echo $this->translate(array('%s review', '%s reviews', $this->result->review_count), $this->result->review_count) ?>&nbsp;&nbsp;
                <?php endif; ?>
                <?php if( !empty($userRating) && in_array("storeproductrating_count", $this->informationArray) ) : ?>
                  <?php echo $this->translate(array('%s rating', '%s ratings', $userRating), $userRating) ?>
                <?php endif; ?>
              </li>
            <?php endif; ?>
            <?php if( $this->resource_type == 'sitestoreproduct_review' && !empty($this->informationArray) && in_array("storeproductrecommend", $this->informationArray) ) : ?>
              <li class="tip_main_body_stat">
                <?php echo $this->translate('Recommended: '); ?>
                <?php if( $this->result->recommend == '1' ) : ?>
                  <?php echo $this->translate('Yes'); ?>
                <?php else: ?>
                  <?php echo $this->translate('No'); ?>
                <?php endif; ?>
              </li>
            <?php endif; ?>
            <?php if( $this->resource_type == 'sitestoreproduct_review' && !empty($this->informationArray) && in_array("storeproductreview_helpful", $this->informationArray) ) : ?>
              <?php $review = $this->result; ?>
              <li class="tip_main_body_stat">
                <?php $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitestoreproduct'); ?>
                <span><?php echo $this->translate("Helpful: "); ?></span>
                <?php $this->countHelpfulReviews = $helpfulTable->getCountHelpful($review->review_id, 1); ?>
                <span class="thumb-up"></span>
                <?php echo $this->countHelpfulReviews ?><?php echo $this->translate(" Yes,"); ?>
                <?php $this->countHelpfulReviews = $helpfulTable->getCountHelpful($review->review_id, 2); ?>
                <span class="thumb-down"></span>
                <?php echo $this->countHelpfulReviews; ?><?php echo $this->translate(" No"); ?>
              </li>
            <?php endif; ?>
            <?php if( ($this->resource_type == 'sitereview_review' || $this->resource_type == 'sitestore_review') && !empty($this->informationArray) && in_array("recommend", $this->informationArray) ) : ?>
              <li class="tip_main_body_stat">
                <?php echo $this->translate('Recommended: '); ?>
                <?php if( $this->result->recommend == '1' ) : ?>
                  <?php echo $this->translate('Yes'); ?>
                <?php else: ?>
                  <?php echo $this->translate('No'); ?>
                <?php endif; ?>
              </li>
            <?php endif; ?>
            <?php if( ($this->resource_type == 'sitereview_review' || $this->resource_type == 'sitestore_review') && !empty($this->informationArray) && in_array("review_helpful", $this->informationArray) ) : ?>
              <?php $review = $this->result; ?>
              <li class="tip_main_body_stat">
                <?php $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'sitereview'); ?>
                <span><?php echo $this->translate("Helpful: "); ?></span>
                <?php $this->countHelpfulReviews = $helpfulTable->getCountHelpful($review->review_id, 1); ?>
                <span class="thumb-up"></span>
                <?php echo $this->countHelpfulReviews ?><?php echo $this->translate(" Yes,"); ?>
                <?php $this->countHelpfulReviews = $helpfulTable->getCountHelpful($review->review_id, 2); ?>
                <span class="thumb-down"></span>
                <?php echo $this->countHelpfulReviews; ?><?php echo $this->translate(" No"); ?>
              </li>
            <?php endif; ?>
            <?php if( $this->resource_type == "sitepage_page" || $this->resource_type == "sitebusiness_business" ) : ?>

              <?php if( !empty($this->informationArray) && in_array("phone", $this->informationArray) && !empty($this->result->phone) ) : ?>
                <li class="seaocore_tip_phone" title="<?php echo $this->translate('Phone') ?>"><?php echo $this->result->phone; ?></li>
              <?php endif; ?>
              <?php if( !empty($this->informationArray) && in_array("email", $this->informationArray) && !empty($this->result->email) ) : ?>
                <li class="seaocore_tip_mail" title="<?php echo $this->translate('Email') ?>"><?php echo $this->result->email; ?></li>
              <?php endif; ?>
              <?php if( !empty($this->informationArray) && in_array("website", $this->informationArray) && !empty($this->result->website) ) : ?>
                <li class="seaocore_tip_mail" title="<?php echo $this->translate('Website') ?>"><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($this->result->website, 30); ?> </li>
              <?php endif; ?>

            <?php endif; ?>
            <?php if( !empty($this->informationArray) && in_array("location", $this->informationArray) ) : ?>
              <?php if( !empty($this->result->location) ) : ?>
                <li class="seaocore_tip_location" title="<?php echo $this->translate('Location'); ?>">
                  <?php if( ($this->resource_type == 'event' || $this->resource_type == 'video' || $this->resource_type == 'group' || $this->resource_type == 'user') && $this->coreModules->isModuleEnabled('sitetagcheckin') ): ?>
                    <?php if( $this->resource_type == 'user' && !$this->isHidden ) : ?>
                      <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->result->seao_locationid, 'resouce_type' => 'seaocore'), $this->result->location, array('onclick' => 'owner(this);return false', 'class' => 'smoothbox')); ?>
                    <?php endif; ?>
                  <?php elseif( isset($this->result->seao_locationid) && !$this->coreModules->isModuleEnabled('sitetagcheckin') ): ?>
                    <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->result->seao_locationid, 'resouce_type' => 'seaocore'), $this->result->location, array('onclick' => 'owner(this);return false', 'class' => 'smoothbox')); ?>
                  <?php else: ?>
                    <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->resource_id, 'resouce_type' => $this->resource_type), $this->result->location, array('onclick' => 'owner(this);return false', 'class' => 'smoothbox')); ?>
                  <?php endif; ?>
                </li>
              <?php endif; ?>
              <?php if( $this->resource_type == 'classified' ): ?>
                <?php if( !empty($this->locationItem) ) : ?>
                  <li class="seaocore_tip_location" title="<?php echo $this->translate('Location'); ?>">
                    <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $this->resource_id, 'resouce_type' => 'classified'), $this->locationItem, array('onclick' => 'owner(this);return false', 'class' => 'smoothbox')); ?>
                  </li>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
            <!-- //FOR GROUP SHOW MEMBER. -->
            <?php if( $this->resource_type == 'group' && !empty($this->informationArray) && in_array("groupmember", $this->informationArray) ) : ?>
              <li class="seaocore_tip_mutual_friends">
                <?php echo $this->translate(array('%s member', '%s members', $this->result->member_count), $this->result->member_count) ?>
              </li>
              <?php if( !empty($this->informationArray) && in_array("joingroupfriend", $this->informationArray) ) : ?>
                <li class="seaocore_tip_members_attend">
                  <?php if( !empty($this->friendLikeCount) ) : ?>
                    <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'feed', 'action' => 'common-member-list', 'resouce_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s friend is member', '%s friends are members', $this->friendLikeCount), $this->friendLikeCount); ?> </a>
                  <?php endif; ?>
                </li>
              <?php endif; ?>
            <?php elseif( ($this->resource_type == 'event' || $this->resource_type == 'siteevent_event' ) ): ?>
              <?php $showMemberCount = true; ?>
              <?php
              if( $this->resource_type == 'siteevent_event' ):
                $siteeventVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent')->version;
                if( Engine_Api::_()->seaocore()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent')->version, '4.8.8') && Engine_Api::_()->siteevent()->isTicketBasedEvent() ):
                  $showMemberCount = false;
                endif;
              endif;
              ?>
              <?php if( $showMemberCount && !empty($this->informationArray) && in_array("eventmember", $this->informationArray) ): ?>
                <li class="seaocore_tip_members_attend">
                  <?php echo $this->translate(array('%s member', '%s members', $this->result->member_count), $this->result->member_count) ?>
                </li>
              <?php endif; ?>
              <?php if( $showMemberCount && !empty($this->informationArray) && in_array("attendingeventfriend", $this->informationArray) && !empty($this->friendLikeCount) ) : ?>
                <li class="seaocore_tip_members_attend">
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'feed', 'action' => 'common-member-list', 'resouce_type' => $this->resource_type, 'resource_id' => $this->resource_id, 'format' => 'smoothbox'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s friend is attending', '%s friends are attending', $this->friendLikeCount), $this->friendLikeCount); ?> </a>
                </li>
              <?php endif; ?>
            <?php elseif( $this->resource_type == 'user' ) : ?>
              <?php if( !empty($this->muctualfriendLikeCount) && ($this->resource_id != $this->viewer_id) && !empty($this->informationArray) && in_array("mutualfriend", $this->informationArray) ): ?>
                <li class="seaocore_tip_mutual_friends">
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'feed', 'action' => 'more-mutual-friend', 'id' => $this->resource_id, 'format' => 'smoothbox'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s mutual friend', '%s mutual friends', $this->muctualfriendLikeCount), $this->muctualfriendLikeCount); ?>		</a>
                </li>
              <?php endif; ?>
            <?php endif; ?>
            <?php if( $this->resource_type == 'user' && ($this->resource_id != $this->viewer_id) && !empty($this->informationArray) && in_array("mutualfriend", $this->informationArray) ): ?>
              <?php if( !empty($this->muctualfriendLikeCount) ): ?>
                <?php
                $container = 1;
                foreach( $this->muctualFriend as $friendInfo ):
                  ?>
                  <li class="info_tip_member_thumb info_show_tooltip_wrapper">
                    <?php
                    $user_subject = Engine_Api::_()->user()->getUser($friendInfo['user_id']);
                    $profile_url = $this->url(array('id' => $friendInfo['user_id']), 'user_profile');
                    ?>
                    <div class="info_show_tooltip">
                      <?php echo $this->user($user_subject)->getTitle() ?>
                      <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
                    </div>
                    <a href="<?php echo $profile_url ?>" target="_parent">
                      <?php echo $this->itemPhoto($this->user($user_subject), 'thumb.icon') ?>
                    </a>
                  </li>
                  <?php
                  if( $container == 5 ): break;
                  endif;
                  ?>
                  <?php
                  $container++;
                endforeach;
                ?>
              <?php endif; ?>
            <?php elseif( $this->resource_type != 'user' && !empty($this->informationArray) && in_array("friendcommon", $this->informationArray) ) : ?>
              <?php if( !empty($this->friendLikeCount) ): ?>
                <li class="seaocore_tip_mutual_friends">
                  <a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'seaocore', 'controller' => 'like', 'action' => 'likelist', 'resource_id' => $this->resource_id, 'resource_type' => $this->resource_type, 'call_status' => 'friend', 'format' => 'smoothbox'), 'default', true)); ?>'); return false;" ><?php echo $this->translate(array('%s friend like this', '%s friends like this', $this->friendLikeCount), $this->friendLikeCount); ?>    </a>
                </li>
                <?php
                $container = 1;
                foreach( $this->activity_result as $friendInfo ):
                  ?>
                  <li class="info_tip_member_thumb info_show_tooltip_wrapper">
                    <?php
                    if( $this->resource_type == 'group' || $this->resource_type == 'event' || $this->resource_type == 'siteevent_event' ):
                      $user_subject = Engine_Api::_()->user()->getUser($friendInfo->user_id);
                      $profile_url = $this->url(array('id' => $friendInfo->user_id), 'user_profile');
                    else:
                      $user_subject = Engine_Api::_()->user()->getUser($friendInfo->poster_id);
                      $profile_url = $this->url(array('id' => $friendInfo->poster_id), 'user_profile');
                    endif;
                    ?>
                    <div class="info_show_tooltip">
                      <?php echo $this->user($user_subject)->getTitle() ?>
                      <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
                    </div>
                    <a href="<?php echo $profile_url ?>" target="_parent">
                      <?php echo $this->itemPhoto($this->user($user_subject), 'thumb.icon') ?>
                    </a>
                  </li>
                  <?php
                  if( $container == 5 ): break;
                  endif;
                  ?>
                  <?php
                  $container++;
                endforeach;
                ?>
              <?php endif; ?>
            <?php endif; ?>

            <?php if( $this->coreModules->isModuleEnabled('sitevideo') && $this->resource_type == 'sitevideo_channel' && !empty($this->result->videos_count) ): ?>
              <li class="seaocore_tip_video_count"> <?php echo $this->translate(array('%s video', '%s videos', $this->result->videos_count), $this->locale()->toNumber($this->result->videos_count)) ?></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
      <!--profile information section start here-->
      <?php if( $this->resource_type == 'user' && !empty($this->result->status) ): ?>
        <div class="seaocore_tip_info_details">
          <?php echo $this->viewMore($this->result->status); ?>
        </div>
      <?php endif; ?>

      <!--profile information section end here-->
    </div>
    <!--profile details section end here-->

    <!--tip bottom btn section start here-->
    <div class="seaocore_tip_content_bottom">
      <ul>
        <?php $flag = false; ?>
        <?php if( $this->resource_type == 'user' ): ?>
          <?php
          //POKE WORK
          $user_subject = Engine_Api::_()->user()->getUser($this->resource_id);
          if( !empty($this->pokeEnabled) && (!empty($this->getpokeFriend)) && ($this->resource_id != $this->viewer_id) && !empty($this->info_values) && in_array("poke", $this->info_values) && (!$this->viewer->isBlockedBy($user_subject) || $this->viewer->isAdmin()) ):
            ?>
            <?php
            if( !$flag ) : $flag = true;
            endif;
            ?>
            <li><a class="seaocore_tip_poke" href="javascript:void(0);" onclick="showSmoothBox('<?php
              echo $this->escape($this->url(array('route' => 'poke_general', 'module' => 'poke', 'controller' => 'pokeusers',
                  'action' => 'pokeuser', 'pokeuser_id' => $this->resource_id, 'format' => 'smoothbox'), 'default', true));
              ?>'); return false;" title='<?php echo $this->translate("Poke") ?>'></a></li>
            <?php endif; //END POKE WORK.  ?>
            <?php //FOR SUGGESTION LINK SHOW IF SUGGESTION PLUGIN INSTALL AT HIS SITE.  ?>
            <?php if( !empty($this->suggestionEnabled) && !empty($this->getMemberFriend) && (!empty($this->suggestion_frienf_link_show)) && !empty($this->info_values) && in_array("suggestion", $this->info_values) && (!empty($this->viewer_id)) ): ?>
              <?php
              if( !$flag ) : $flag = true;
              endif;
              ?>
            <li><a class="seaocore_tip_suggest" href="javascript:void(0);" onclick="showSmoothBox('<?php
              echo $this->escape($this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' =>
                  'switch-popup', 'modName' => $this->moduleNmae, 'modContentId' => $this->resource_id, 'modError' => 1, 'format' => 'smoothbox'), 'default', true));
              ?>'); return false;" title='<?php echo $this->translate("Suggest to Friends") ?>'></a></li>
            <?php endif; //END SUGGESTION WORK. ?>
            <?php
            //FOR MESSAGE LINK
            $item = Engine_Api::_()->getItem('user', $this->resource_id);
            if( !empty($this->info_values) && in_array("message", $this->info_values) && (Engine_Api::_()->seaocore()->canSendUserMessage($item)) && (!empty($this->viewer_id)) ) :
              ?>
              <?php
              if( !$flag ) : $flag = true;
              endif;
              ?>
              <?php if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('primemessenger') && Engine_Api::_()->primemessenger()->isPrimeMessengerActive() ) : ?>
              <li><a onClick="window.pm.openChatBox(<?php echo $this->resource_id ?>);" class="seaocore_tip_message primemessenger_icon" href="javascript:void(0)" title='<?php echo $this->translate("Message") ?>'> </a></li>
            <?php else : ?>
              <li><a class="seaocore_tip_message" href="<?php echo $this->base_url; ?>/messages/compose/to/<?php echo $this->resource_id ?>" title='<?php echo $this->translate("Message") ?>'></a></li>
            <?php endif; ?>
          <?php endif; ?>
          <?php if( !empty($this->info_values) && in_array("addfriend", $this->info_values) && (!empty($this->viewer_id)) ): ?>
            <?php $uaseFRIENFLINK = $this->coreModules->isModuleEnabled('sitemember') ? $this->userFriendshipAjax($this->user($this->resource_id), null, true) : $this->userFriendship($this->user($this->resource_id)); ?>
            <?php if( !empty($uaseFRIENFLINK) ) : ?>
              <?php $flag = true; ?>
              <?php
              //VIEWER IS VIEW PROFILE OF ANOTHER USER AND NOT A FRIEND THEN ADD FRIEND
              //LINK IS SHOW.
              ?>
              <li><?php echo $uaseFRIENFLINK; ?></li>
              <?php
            endif;
          endif;
          ?>
          <?php //VIEWER IS VIEW PROFILE OF ANOTHER USER AND NOT A FRIEND THEN ADD FRIEND LINK IS SHOW.?>
        <?php endif; ?>
        <?php if( !empty($this->suggestionEnabled) && ($this->resource_type != 'user') && (!empty($this->suggestion_frienf_link_show)) && !empty($this->info_values) && in_array("suggestion", $this->info_values) && (!empty($this->viewer_id)) ) : ?>
          <?php
          if( !$flag ) : $flag = true;
          endif;
          ?>
          <li><a class="seaocore_tip_suggest" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'switch-popup', 'modName' => $this->moduleNmae, 'modContentId' => $this->resource_id, 'modError' => 1, 'format' => 'smoothbox'), 'default', true)); ?>'); return false;" title='<?php echo $this->translate("Suggest to Friends") ?>' ></a></li>
        <?php endif; ?>
        <?php if( !empty($this->viewer_id) && (!empty($this->info_values) && in_array("review_wishlist", $this->info_values)) && !empty($this->listingtypeArray->wishlist) && $this->resource_type == 'sitereview_listing' ) : ?>
          <?php
          if( !$flag ) : $flag = true;
          endif;
          ?>
          <li><a class="seaocore_tip_wishlist" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitereview_wishlist_general', 'module' => 'sitereview', 'controller' => 'wishlist', 'action' => 'add', 'listing_id' => $this->resource_id), 'default', true)); ?>'); return false;" title='<?php echo $this->translate("Add To Wishlist") ?>'></a></li>
        <?php elseif( !empty($this->viewer_id) && (!empty($this->info_values) && in_array("storeproductreview_wishlist", $this->info_values)) && $this->resource_type == 'sitestoreproduct_product' ) : ?>
          <?php
          if( !$flag ) : $flag = true;
          endif;
          ?>
          <li><a class="seaocore_tip_wishlist" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('action' => 'add', 'product_id' => $this->resource_id), 'sitestoreproduct_wishlist_general', true)); ?>'); return false;" ></a></li>
        <?php endif; ?>
        <?php if( (!empty($this->info_values) && in_array("joinpage", $this->info_values)) && !empty($this->joinFlag) && $this->resource_type == 'sitepage_page' && !empty($this->member_approval) ) : ?>
          <?php
          if( !$flag ) : $flag = true;
          endif;
          ?>
          <?php
            $table = Engine_Api::_()->getDbTable('pages', 'sitepage');
            $select=$table->select()
                          ->where('page_id=?',$this->resource_id);
            $data=$table->fetchRow($select);
            if($data['join_enable'] == 1) : ?>
          <li><a class="seaocore_tip_Join_event" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitepage_profilepagemember', 'module' => 'sitepagemember', 'controller' => 'member', 'action' => 'join', "page_id" => $this->resource_id), 'default', true)); ?>'); return false;"></a></li>
        <?php endif; ?>
        <?php if( (!empty($this->info_values) && in_array("requestpage", $this->info_values)) && !empty($this->requestFlag) && $this->resource_type == 'sitepage_page' && empty($this->member_approval) ) : ?>
          <?php
          if( !$flag ) : $flag = true;
          endif;
          ?>
          <li><a class="seaocore_tip_Join_event" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitepage_profilepagemember', 'module' => 'sitepagemember', 'controller' => 'member', 'action' => 'request', "page_id" => $this->resource_id), 'default', true)); ?>'); return false;"></a></li>
        <?php endif; ?>
      <?php endif;?>
        <?php
        if( $this->resource_type == 'siteevent_event' ) : $info_values = $this->info_values;
          include APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_infotooltip_footer.tpl';
        endif;
        ?>
        <?php if( $this->resource_type == 'sitemusic_pl' ): ?>
          <?php $album = $this->result; ?>
          <li>
            <a href="javascript:void(0)" onclick="onAlbumQueue(<?php echo $album->getIdentity(); ?>)" title="Album Queue">
              <i class="fa fa-bars" aria-hidden="true"></i>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)" onclick="onAlbumPlay(<?php echo $album->getIdentity(); ?>)">
              <i class="fa fa-play" aria-hidden="true"></i>
            </a>
          </li>
        <?php endif; ?>

        <?php if( $this->resource_type == 'sitemusic_playlist_song' ): ?>
          <?php $song = $this->result; ?>
          <?php $jsonSong = Engine_Api::_()->getApi('core', 'sitemusic')->songInfoForPlayer($song); ?>
          <?php if( Engine_Api::_()->getApi('core', 'sitemusic')->isPlaylistCreate() && Engine_Api::_()->sitemusic()->enablePlaylist() ): ?>
            <li>
              <a href='<?php echo $this->url(array("module" => "sitemusic", "controller" => "playlists", "action" => 'append', 'item_id' => $song->song_id), "sitemusic_item_specific", true) ?>' title="Add to Playlist" class="smoothbox buttonlink">
                <i class="fa fa-plus" aria-hidden="true" title="Add to Playlist"></i>
              </a>
            </li>
          <?php endif; ?>
          <li>
            <a href= "javascript:void(0);" onclick="onPlay('<?php echo $jsonSong; ?>')">
              <i class="fa fa-play" aria-hidden="true" title="Play"></i>
            </a>
          </li>
          <li>
            <a href= "javascript:void(0);" onclick="onSongQueue('<?php echo $jsonSong; ?>')">
              <i class="fa fa-bars" aria-hidden="true" title="Add to Queue"></i>
            </a>
          </li>
        <?php endif; ?>

        <?php if( $this->resource_type == 'sitemusic_userpl' ): ?>
          <?php $playlist = $this->result; ?>
          <li>
            <a href="javascript:void(0);" onclick="onAlbumQueue(<?php echo $playlist->getIdentity(); ?>)" title="Playlist Queue">
              <i class="fa fa-bars" aria-hidden="true"></i>
            </a>
          </li>
          <li>
            <a href="javascript:void(0);" onclick="onAlbumPlay(<?php echo $playlist->getIdentity(); ?>)">
              <i class="fa fa-play" aria-hidden="true"></i>
            </a>
          </li>
        <?php endif; ?>

        <?php
//FOR SHARE LINK.
        if( !empty($this->viewer_id) && ($this->resource_type != 'user') && ($this->resource_type != 'siteevent_event') && ($this->resource_type != 'forum_post') && ($this->resource_type != 'forum_topic') && ($this->resource_type != 'album') && !empty($this->info_values) && in_array("share", $this->info_values) ):
          ?>
          <?php
          if( !$flag ) : $flag = true;
          endif;
          ?>
          <li><a class="seaocore_tip_share" href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share', 'type' => $this->resource_type, 'id' => $this->resource_id, 'format' => 'smoothbox'), 'default', true)); ?>'); return false;" title='<?php echo $this->translate("Share") ?>'></a></li>
          <?php endif; ?>
      </ul>
    </div>
    <!--tip bottom btn section end here-->
  </div>
</div>
</body>
</html>
