<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>

<?php $widgetIdentity = $this->identity; ?>

<?php if(!$this->isAjax): ?>
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


  <script type="text/javascript" >
    function switchview_<?php echo $widgetIdentity ?>(flage){
      if(flage==1){

        $('grid-<?php echo $widgetIdentity ?>').removeClass('dnone');
        $('list-<?php echo $widgetIdentity ?>').addClass('dnone');
        $('listicon-<?php echo $widgetIdentity ?>').removeClass('selected');
        $('gridicon-<?php echo $widgetIdentity ?>').addClass('selected');
      }

      if(flage==0){

        $('list-<?php echo $widgetIdentity ?>').removeClass('dnone');
        $('grid-<?php echo $widgetIdentity ?>').addClass('dnone');
        $('gridicon-<?php echo $widgetIdentity ?>').removeClass('selected');
        $('listicon-<?php echo $widgetIdentity ?>').addClass('selected');
      }

    }
    en4.core.runonce.add(function() {
        switchview_<?php echo $widgetIdentity ?>(1);
    })
  </script>

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

<img src="application/modules/Sitebooking/externals/images/loader.gif" height=30 width=30 style="display: none;" id="loader-<?php echo $widgetIdentity ?>">      

<?php if(!$this->isAjax): ?>
  <div class="sitebooking_more" id="view_more-<?php echo $widgetIdentity ?>" onclick = " view_<?php echo $widgetIdentity ?>() ">View more</div>
<?php endif; ?>

<!-- VIEW MORE -->
<script type="text/javascript">

  function view_<?php echo $widgetIdentity ?>(){

    var params = {
      requestParams:<?php echo json_encode($this->params) ?>
    };

    en4.core.request.send(new Request.HTML({

      url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
      data: $merge(params.requestParams, {
        format : 'html',
        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
        isAjax : 1
      }),
      
      onRequest: function () {
        $('loader-<?php echo $widgetIdentity ?>').show();
      },     

      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

        $('loader-<?php echo $widgetIdentity ?>').hide();
        var element = new Element('div', {
            'html': responseHTML
        });
        Elements.from(element.getElement('.sitebooking_list').innerHTML).inject($('list-<?php echo $widgetIdentity ?>'));

        if($('list-<?php echo $widgetIdentity ?>').innerHTML.trim() == '')
        {

           $('list-<?php echo $widgetIdentity ?>').innerHTML = $('list-<?php echo $widgetIdentity ?>').innerHTML + "<p>No Providers Found Related to this</p>";
        }

        var element = new Element('div', {
            'html': responseHTML
        });
        Elements.from(element.getElement('.sitebooking_grid').innerHTML).inject($('grid-<?php echo $widgetIdentity ?>'));


        if($('grid-<?php echo $widgetIdentity ?>').innerHTML.trim() == '')
        {

           $('grid-<?php echo $widgetIdentity ?>').innerHTML = $('grid-<?php echo $widgetIdentity ?>').innerHTML + "<p>No Providers Found Related to this grid view</p>";
        }

        Smoothbox.bind($('list-<?php echo $widgetIdentity ?>'));
        Smoothbox.bind($('grid-<?php echo $widgetIdentity ?>'));
        en4.core.runonce.trigger();
      }     
    }));
  };

  var cpage_<?php echo $widgetIdentity ?> = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  
  var pages_<?php echo $widgetIdentity ?> = <?php echo $this->paginator->count() ?>;

  if(cpage_<?php echo $widgetIdentity ?> >= pages_<?php echo $widgetIdentity ?>) {
      document.getElementById("view_more-<?php echo $widgetIdentity ?>").style.display = "none";
  } else {
      document.getElementById("view_more-<?php echo $widgetIdentity ?>").style.display = "block";
  }     

</script>