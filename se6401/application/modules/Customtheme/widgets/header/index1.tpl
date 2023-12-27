
 <?php // $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/all.min.css'); ?> 
  <?php //$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/bootstrap.min.css'); ?> 



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" /> 
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/styles.css'); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;1,100;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    
    <!-- my css file -->
    <!-- <link href="application/modules/Customtheme/externals/styles/main.css" rel="stylesheet" type="text/css" /> -->
    <!-- 
        <link href="application/modules/Customtheme/externals/styles/owl.theme.default.min.css" rel="stylesheet" type="text/css" />
    <link href="application/modules/Customtheme/externals/styles/owl.carousel.min.css" rel="stylesheet" type="text/css" />
-->

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/styles.css'); ?>
    
    <!-- start Nav -->
    <nav class="navbar navbar-expand-lg navbar-light">
      <div class="container">
        <a class="navbar-brand" href="/">
          <img src="./images/logo-consulto-update.png" alt="" />
        </a>
        <a href="javascript:void(0);" class="navbar-toggler" id="header_mobile_toggle">
          <span class="navbar-toggler-icon"></span>
        </a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav m-auto mb-2 mb-lg-0">
          
            <?php foreach( $this->navigation as $navigationMenu ): 
                $class = explode(' ', $navigationMenu->class); 
            ?>
            
            <?php 
                $mainMenuIcon = Engine_Api::_()->getApi('menus', 'sescompany')->getIconsMenu(end($class)); 
                $mainMenuID = Engine_Api::_()->getApi('menus', 'sescompany')->getMenuId(end($class));
                $menus = Engine_Api::_()->getApi('menus', 'sescompany')->getMenuObject($mainMenuID);
            ?>
            
            <li class="nav-item <?php echo $navigationMenu->get('active') ? 'active' : '' ?>">
                <a href='<?php echo $navigationMenu->getHref() ?>' class="nav-link <?php echo $navigationMenu->getClass() ? ' ' . $navigationMenu->getClass() : ''  ?>" <?php if( $navigationMenu->get('target') ): ?> target='<?php echo $navigationMenu->get('target') ?>' <?php endif; ?> >
                <?php if(!empty($mainMenuIcon) && empty($menus->icon_type)):?>
                    <i style="background-image:url(<?php echo Engine_Api::_()->storage()->get($mainMenuIcon, '')->getPhotoUrl(); ?>);"></i>
                <?php elseif($menus->icon_type && !empty($menus->font_icon)): ?>
                    <i class="fa <?php echo $menus->font_icon ?>"></i>
                <?php endif;?>
                <span><?php echo $this->translate($navigationMenu->label); ?></span>
                </a>
            </li>
            
            <?php endforeach; ?>
          </ul>
          
          <div class="icon-nav">
           <!--  <i class="fas fa-search" onclick="window.location.href='/search';"></i> -->
            <div class="dropdown">
              <?php if($this->viewer_id) { ?>  
                <button class="header_btn btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="header_setting_toggle">
                  <span><?php echo $this->translate("Dashboard")?></span>
                </button>
               <?php } else{?>
                <button class="header_btn btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="header_setting_toggle">
                  <span><?php echo $this->translate("Login / Signup")?></span>
                </button>
                <?php } ?>
             
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1" id="header_setting_menu">
                  
                  <li><a class="dropdown-item" href="/zoom-meeting"><?php echo $this->translate("Zoom Meeting");?></a></li>
                      <?php foreach( $this->navigationMini as $item ): ?>
                        <?php if(end(explode(' ', $item->class)) == 'core_mini_signup'):?>
                            <li><a class="dropdown-item" href="/signup?profile_type=4"><?php echo $this->translate('Consultant/Mentor');?></a></li>
                            <li><a class="dropdown-item" href="/signup?profile_type=92"><?php echo $this->translate('Trainee');?></a></li>
                        <?php elseif(end(explode(' ', $item->class)) == 'core_mini_auth' && $this->viewer->getIdentity() == 0):?>
                            <!--<li><a class="dropdown-item" href="/login"><?php echo $this->translate($item->getLabel());?></a></li>-->
                        <?php elseif(end(explode(' ', $item->class)) == 'core_mini_auth' && $this->viewer->getIdentity() != 0):?>
                            <?php continue;?>
                        <?php elseif(end(explode(' ', $item->class)) == 'core_mini_settings'):?>
                        <li class="dropdown-item">
                            <a class="menu_user_settings user_settings_general" href="<?php echo $this->viewer->getHref();?>"><?php echo $this->viewer->getTitle();?></a>
                        </li>
                        
                        <li class="dropdown-item">
                            <a class="menu_user_settings user_settings_general" href="/members/settings/general"><?php echo $this->translate("General");?></a>
                        </li>
                        <li class="dropdown-item">
                            <a class="menu_user_settings user_settings_privacy" href="/members/settings/privacy"><?php echo $this->translate("Privacy");?></a>
                        </li>
                        <li class="dropdown-item">
                            <a class="menu_user_settings user_settings_notifications" href="/members/settings/notifications"><?php echo $this->translate("Notifications");?></a>
                        </li>
                        <li class="dropdown-item">
                            <a class="menu_user_settings user_settings_emails" href="/members/settings/emails"><?php echo $this->translate("Emails");?></a>
                        </li>
                        <li class="dropdown-item">
                            <a class="menu_user_settings user_settings_payment" href="/payment/settings"><?php echo $this->translate("Subscription");?></a>
                        </li>
                        <li class="dropdown-item">
                            <a class="menu_user_settings user_settings_password" href="/members/settings/password"><?php echo $this->translate("Change Password");?></a>
                        </li>
                        <li class="dropdown-item">
                            <a class="menu_user_settings user_settings_network" href="/members/settings/network"><?php echo $this->translate("Networks");?></a>
                        </li>
                        <?php if($this->viewer->getIdentity() && $this->viewer->level_id == 1):?>
                      <li class="dropdown-item">
                          <a href="/admin/"><?php echo $this->translate("Administrator");?></a>
                        </li>
                        <?php endif;?>
                        <li class="dropdown-item">
                        <a href="/logout"><?php echo $this->translate("Logout");?></a>
                      </li>
                <?php endif;?>
                
              <?php endforeach;?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>
    <!-- end Nav -->

<script>
  
  $('header_setting_toggle').addEvent('click', function(event){
    event.stop();
    if($('header_setting_menu').hasClass('show'))
      $('header_setting_menu').removeClass('show');
    else
      $('header_setting_menu').addClass('show');
    return false;
  });

  $('header_mobile_toggle').addEvent('click', function(event){
    event.stop();
    if($('navbarSupportedContent').hasClass('show'))
      $('navbarSupportedContent').removeClass('show');
    else
      $('navbarSupportedContent').addClass('show');
    return false;
  });
</script>