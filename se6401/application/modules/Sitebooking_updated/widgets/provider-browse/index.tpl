<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>

<?php $widgetIdentity = $this->identity; ?>

<?php if(!$this->isAjax): ?>
  <div id="hideResponse"> </div>
  <div class = "tabs_alt">
      
    <ul> 

      <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      
        <li>
          <span>
            <?php echo $this->translate(array('%s Provider', '%s Providers', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
          </span>
        </li>
        
        <li class="fright">
          <span class="seaocore_tab_select_wrapper">
            <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
            <span id = "listicon-<?php echo $widgetIdentity ?>" class="seaocore_tab_icon tab_icon_list_view" onclick="switchview_<?php echo $widgetIdentity ?>(0)"></span>
          </span>
        </li>

        <li class="fright">
          <span class="seaocore_tab_select_wrapper">
              <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
          <span id = "gridicon-<?php echo $widgetIdentity ?>" class="seaocore_tab_icon tab_icon_grid_view" onclick="switchview_<?php echo $widgetIdentity ?>(1)"></span>
          </span>
        </li>
      
      <?php endif; ?>
    
    </ul>

  </div>

  <input type="hidden" id="hidden_filter_type" name="hidden_filter_type" value="">
<img src="application/modules/Sitebooking/externals/images/loader.gif" height=30 width=30 style="display: none;" id="loader-<?php echo $widgetIdentity ?>">      


  <script type="text/javascript" >
    function switchview_<?php echo $widgetIdentity ?>(flage){
      if(flage==1){

        scriptJquery('#grid-<?php echo $widgetIdentity ?>').removeClass('dnone');
        scriptJquery('#list-<?php echo $widgetIdentity ?>').addClass('dnone');
        scriptJquery('#listicon-<?php echo $widgetIdentity ?>').removeClass('selected');
        scriptJquery('#gridicon-<?php echo $widgetIdentity ?>').addClass('selected');
      }

      if(flage==0){

        scriptJquery('#list-<?php echo $widgetIdentity ?>').removeClass('dnone');
        scriptJquery('#grid-<?php echo $widgetIdentity ?>').addClass('dnone');
        scriptJquery('#gridicon-<?php echo $widgetIdentity ?>').removeClass('selected');
        scriptJquery('#listicon-<?php echo $widgetIdentity ?>').addClass('selected');
      }

    }
    en4.core.runonce.add(function() {
        switchview_<?php echo $widgetIdentity ?>(1);
    })
  </script>
<div id="views-<?php echo $widgetIdentity ?>">
<?php endif; ?>

<!-- USER REVIEW -->
<?php
  echo $this->partial('provider_grid_view.tpl', 'sitebooking', array(
      'paginator' => $this->paginator,
      'widgetIdentity' => $this->identity,
  ));
?>
<?php
  echo $this->partial('provider_list_view.tpl', 'sitebooking', array(
     'paginator' => $this->paginator,
     'widgetIdentity' => $this->identity,
  ));
?>


<?php if(!$this->isAjax): ?>
</div>
  <div class="sitebooking_more" id="view_more-<?php echo $widgetIdentity ?>" onclick = " view_<?php echo $widgetIdentity ?>() ">View more</div>
<?php endif; ?>

<!-- VIEW MORE -->
<script type="text/javascript">

  function view_<?php echo $widgetIdentity ?>(){

    var params = {
      requestParams:<?php echo json_encode($this->params) ?>
    };

    en4.core.request.send(scriptJquery.ajax({
      dataType:'html',
      url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
      data: scriptJquery.extend(params.requestParams, {
        format : 'html',
        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
        isAjax : 1
      }),
      
      beforeSend: function () {
        scriptJquery('#loader-<?php echo $widgetIdentity ?>').show();
      },     

      success: function(responseHTML) {

        scriptJquery('#hideResponse').html(responseHTML);
        scriptJquery('#loader-<?php echo $widgetIdentity ?>').hide();

        scriptJquery('#views-<?php echo $widgetIdentity ?>').find('.sitebooking_list').html(scriptJquery('#views-<?php echo $widgetIdentity ?>').find('.sitebooking_list').html() + scriptJquery('#hideResponse').find('.sitebooking_list').html());

        if(scriptJquery('#views-<?php echo $widgetIdentity ?>').html() == '') {

          scriptJquery('#views-<?php echo $widgetIdentity ?>').html(scriptJquery('#list-<?php echo $widgetIdentity ?>').html() + en4.core.language.translate("No Providers Found Related to this"));
        }

        scriptJquery('#views-<?php echo $widgetIdentity ?>').find('.sitebooking_grid').html(scriptJquery('#views-<?php echo $widgetIdentity ?>').find('.sitebooking_grid').html()+scriptJquery('#hideResponse').find('.sitebooking_grid').html());


        if(scriptJquery('#views-<?php echo $widgetIdentity ?>').html() == '') {

          scriptJquery('#views-<?php echo $widgetIdentity ?>').html(scriptJquery('#views-<?php echo $widgetIdentity ?>').html() + en4.core.language.translate("No Providers Found Related to this grid view"));
        }
        scriptJquery('#hideResponse').html('');
      }     
    }));
  };

  var cpage_<?php echo $widgetIdentity ?> = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  
  var pages_<?php echo $widgetIdentity ?> = <?php echo $this->paginator->count() ?>;

  if(cpage_<?php echo $widgetIdentity ?> >= pages_<?php echo $widgetIdentity ?>) {
      scriptJquery("#view_more-<?php echo $widgetIdentity ?>").css('display','none');
  } else {
      scriptJquery("#view_more-<?php echo $widgetIdentity ?>").css('display','block');
  }     

</script>