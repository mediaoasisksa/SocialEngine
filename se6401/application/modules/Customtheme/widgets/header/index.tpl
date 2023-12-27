
 <?php // $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/all.min.css'); ?> 
  <?php //$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/bootstrap.min.css'); ?> 




<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;1,100;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/styles/styles.css'); ?>
    
    <!-- start Nav -->
    <nav class="navbar navbar-expand-lg navbar-light">
      <div class="container">
        <a class="navbar-brand" href="">
          <img src="./images/logo-consulto-update.png" alt="" />
        </a>
        <a href="javascript:void(0);" class="navbar-toggler" id="header_mobile_toggle" onclick="mtoggle()">
          <span class="navbar-toggler-icon"></span>
        </a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav m-auto mb-2 mb-lg-0">
          
            <?php foreach( $this->navigation as $navigationMenu ): 
                $class = explode(' ', $navigationMenu->class); 
            ?>
            
            <?php 
                // $mainMenuIcon = Engine_Api::_()->getApi('menus', 'sescompany')->getIconsMenu(end($class)); 
                // $mainMenuID = Engine_Api::_()->getApi('menus', 'sescompany')->getMenuId(end($class));
                // $menus = Engine_Api::_()->getApi('menus', 'sescompany')->getMenuObject($mainMenuID);
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
                <button class="header_btn btn" type="button" onclick="window.location.href='<?php echo $this->viewer->getHref();?>'">
                  <span><?php echo $this->translate("Dashboard")?></span>
                </button>
               <?php } else{?>
                <button class="header_btn btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="header_setting_toggle">
                  <span><?php echo $this->translate("Login / Signup")?></span>
                </button>
                <?php } ?>
             
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1" id="header_setting_menu">
                  
                  
                      <?php foreach( $this->navigationMini as $item ): ?>
                        <?php if(end(explode(' ', $item->class)) == 'core_mini_signup'):?>
                          <li><a class="dropdown-item" href="/signup"><?php echo $this->translate('Signup');?></a></li>
                            <!-- <li><a class="dropdown-item" href="/upgrade/signup?profile_type=13"><?php // echo $this->translate('Signup');?></a></li> -->
                            <li><a class="dropdown-item" href="/login"><?php echo $this->translate('Login');?></a></li>
                        <?php elseif(end(explode(' ', $item->class)) == 'core_mini_auth' && $this->viewer->getIdentity() == 0):?>
                            <!--<li><a class="dropdown-item" href="/login"><?php echo $this->translate($item->getLabel());?></a></li>-->
                        <?php elseif(end(explode(' ', $item->class)) == 'core_mini_auth' && $this->viewer->getIdentity() != 0):?>
                            <?php continue;?>
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
  function mtoggle() {
    var element = document.getElementById("navbarSupportedContent");
    element.classList.toggle("show");
  }
</script>