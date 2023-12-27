<style type="text/css">
  .global_form_1 {
    clear:both;
    overflow:hidden;
  }
  .global_form_1 > div {
    -moz-border-radius:7px 7px 7px 7px;
    background-color:#E9F4FA;
    float:left;
    max-width:500px;
    overflow:hidden;
    padding:10px;
  }
  .global_form_1 > div > div {
    background:none repeat scroll 0 0 #FFFFFF;
    border:1px solid #D7E8F1;
    overflow:hidden;
    padding:20px;
  }

  .button{
    +rounded(3px);
    padding: 5px;
    font-weight: bold;
    border: none;
    background-color: #619dbe;
    border: 1px solid #50809b;
    color: #fff;
    background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/buttonbg.png);
    background-repeat: repeat-x;
    background-position: 0px 1px;
    font-family: tahoma, verdana, arial, sans-serif;
    font-size:12px;
    display: inline-block;
  }
  .button:hover
  {
    background-color: #7eb6d5;
    cursor: pointer;
    text-decoration:none;
  }

  .global_form_1 > div > div.global_form_1_footer {
    text-align: center;
    background: inherit;
    border: 0;
    padding: 5px;
  }
</style>
<div class="global_form_1">
  <div>
    <div> The SocialApps.tech Core Plugin is not installed on your site. Please download the latest version of this FREE plugin from your Client Area on <a href="https://socialapps.tech/" target="_blank">SocialApps.tech</a> and install / upgrade on your website before installing / upgrading this plugin.
    </div>
    <div class="global_form_1_footer">
      <a href="javascript::void(0);" onclick="parent.Smoothbox.close();" class="button"> Close</a>
    </div>
  </div>
</div>