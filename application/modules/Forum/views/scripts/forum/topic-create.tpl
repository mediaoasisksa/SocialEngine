<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: topic-create.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     John
 */
?>

<script type="text/javascript">
function showUploader()
{
  scriptJquery('#photo').show();
  scriptJquery('#photo-label').hide();
}
</script>

<h2>
<?php echo $this->htmlLink(array('route'=>'forum_general'), $this->translate("Forums"));?>
  &#187; <?php echo $this->htmlLink(array('route'=>'forum_forum', 'forum_id'=>$this->forum->getIdentity()), $this->translate($this->forum->getTitle()));?>
  &#187 <?php echo $this->translate('Post Topic');?>
</h2>

<?php echo $this->form->render($this) ?>


<script type="text/javascript">
  scriptJquery('.core_main_forum').parent().addClass('active');
</script>
