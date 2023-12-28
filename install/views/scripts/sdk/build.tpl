<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: build.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class="content sdk" id="content3">


  <h3>Build Packages</h3>

  <p>
    These are the packages we found on your system. Choose the ones you want to
    build into distributable files.
  </p>

  <?php if( $this->status ): ?>

    <div class="tip">
      Your package(s) have been built successfully.
    </div>

  <?php elseif( $this->error ): ?>
  
    <div class="error">
      <?php echo $this->error ?>
    </div>
    
  <?php endif; ?>

  <?php if( empty($this->buildPackages) ): ?>

    <div class="tip">
      No packages were found.
    </div>

  <?php else: ?>

    <form action="<?php echo $this->url() ?>" method="post">
      <table class="sdk_table build">
          <thead>
            <tr>
              <th><input type='checkbox' class='checkbox' onclick="toggle(this);" /></th>
              <th class="package"><a href="javascript:void(0);">Package</a></th>
              <th class="version"><a href="javascript:void(0);">Version</a></th>
              <th class="type"><a href="javascript:void(0);">Type</a></th>
              <th class="author"><a href="javascript:void(0);">Author</a></th>
              <th class="moreinfo">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <?php $j = 1; ?>
            <?php foreach( $this->buildPackages as $package ): $i = !@$i; ?>
  
              <tr<?php if( !$i ) echo ' class="alt"'; ?>>
                <td>
                  <input type='checkbox' class='checkbox' name="build[]" value="<?php echo $package['key'] ?>" />
                </td>
                <td>
                  <span class="sdk_build_title">
                    <strong><?php echo $package['manifest']['package']['title'] ?></strong>
                  </span>
                  <div class="sdk_build_moreinfo_container" id="sdk_build_moreinfo_container_<?php echo $j; ?>">
                    <div class="sdk_build_location">
                      <i>Location:</i>
                      <p>
                        <?php echo $package['manifest']['package']['path'] ?>
                      </p>
                    </div>
                    <div class="sdk_build_description">
                      <i>Description:</i>
                      <p>
                        <?php echo $package['manifest']['package']['description'] ?>
                      </p>
                    </div>
                  </div>
                </td>
                <td>
                  <?php echo $package['manifest']['package']['version'] ?>
                </td>
                <td>
                  <?php echo ucfirst($package['manifest']['package']['type']) ?>
                </td>
                <td>
                  <?php echo @$package['manifest']['package']['author'] ?>
                </td>
                <td class="moreinfo">
                  <a href="javascript:void(0);" onclick="showHide('sdk_build_moreinfo_container_<?php echo $j; ?>');">
                    More info
                  </a>
                </td>
              </tr>
              <?php $j++; ?>
            <?php endforeach; ?>

          </tbody>
        </table>
        <button type="submit">Build Packages</button>
      </form>
      <script type="text/javascript">
        function showHide(source) {
          if(scriptJquery('#'+source).css('display') == 'none')
          scriptJquery('#'+source).show();
          else
          scriptJquery('#'+source).hide();
        }
        function toggle(source) {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            for (var i = 0; i < checkboxes.length; i++) {
              if (checkboxes[i] != source)
                checkboxes[i].checked = source.checked;
            }
        }
      </script>
  <?php endif; ?>
</div>
