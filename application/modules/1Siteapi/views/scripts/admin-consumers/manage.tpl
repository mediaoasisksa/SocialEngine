<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    manage.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate('SocialEngine REST API Plugin') ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<h3>
    <?php echo $this->translate('API Consumers / API Clients'); ?>
</h3>

<p class="description">
    <?php echo $this->translate("This API uses OAuth (open standard for authorization) for client authentication process, where 'Consumer Key' and 'Consumer Secret Key' are used to make connections between API clients and server. Hence, every Client requires these 2 keys for API communication. These keys are used for creating 'OAuth Tokens' for users to access server resources without sharing their credentials for every API request. Thus, it is important that these keys should be kept secret and confidential between you the API provider, and the respective API clients."); ?>
</p>

<div>
    <?php
    echo $this->htmlLink(array('action' => 'create', 'reset' => false), $this->translate('Create New API Consumer'), array(
        'class' => 'buttonlink icon_siteapi_admin_add',
    ))
    ?>
</div>
<br />

<div class='admin_search'>
    <div class="clear">
        <div class="search">
            <form method="get" class="global_form_box" action="<?php echo $this->url(array('module' => 'siteapi', 'controller' => 'consumers', 'action' => 'manage'), 'admin_default', false) ?>">

                <div>
                    <label>
                        <?php echo $this->translate("Title") ?>
                    </label>
                    <?php if (empty($this->title)): ?>
                        <input type="text" name="title" /> 
                    <?php else: ?>
                        <input type="text" name="title" value="<?php echo $this->title ?>"/>
                    <?php endif; ?>
                </div>

                <div>
                    <label>
                        <?php echo $this->translate("Consumer Key") ?>
                    </label>
                    <?php if (empty($this->title)): ?>
                        <input type="text" name="key" /> 
                    <?php else: ?>
                        <input type="text" name="key" value="<?php echo $this->key ?>"/>
                    <?php endif; ?>
                </div>

                <div>
                    <label>
                        <?php echo $this->translate("Consumer Secret") ?>
                    </label>
                    <?php if (empty($this->title)): ?>
                        <input type="text" name="secret" /> 
                    <?php else: ?>
                        <input type="text" name="secret" value="<?php echo $this->secret ?>"/>
                    <?php endif; ?>
                </div>

                <div>
                    <label>
                        <?php echo "Status"; ?>
                    </label>
                    <select id="status" name="status">
                        <option value="2" ></option>
                        <?php
                        if ($this->status == 1):
                            echo '<option value="1" selected="selected">Enabled</option>';
                        else:
                            echo '<option value="1">Enabled</option>';
                        endif;


                        if ($this->status == 0):
                            echo '<option value="0" selected="selected">Disabled</option>';
                        else:
                            echo '<option value="0">Disabled</option>';
                        endif;
                        ?>                  
                    </select>
                </div>

                <div>
                    <div class="buttons">
                        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>

<br />

<?php if (!empty($this->paginator)): ?>
    <div class='admin_results'>
        <div>
            <?php $count = $this->paginator->getTotalItemCount() ?>
            <?php echo $this->translate(array("%s consumer found.", "%s consumers found.", $count), $this->locale()->toNumber($count))
            ?>
        </div>
        <div>
            <?php echo $this->paginationControl($this->paginator); ?>
        </div>
    </div>
<?php endif; ?>

<br />


<?php if (!empty($this->paginator) && count($this->paginator)): ?>
                            <!--<form id='multidelete_form' method="post" action="<?php // echo $this->url();       ?>" onSubmit="return multiDelete()">-->
    <table class='admin_table'>
        <thead>
            <tr>
              <!--<th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>-->
                <th class='admin_table_short'>ID</th>
                <th><?php echo $this->translate("Title") ?></th>
                <th><?php echo $this->translate("Consumer Key") ?></th>
                <th><?php echo $this->translate("Consumer Secret") ?></th>
                <th><?php echo $this->translate("Status") ?></th>
                <th><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->paginator as $item): ?>
                <tr>
                  <!--<td><input type='checkbox' class='checkbox' name='delete_<?php // echo $item->getIdentity();        ?>' value="<?php // echo $item->getIdentity();        ?>" /></td>-->
                    <td><?php echo $item->consumer_id ?></td>
                    <td title="<?php echo $item->title ?>"><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->title, 20) ?></td>
                    <td title="<?php echo $item->key ?>"><i><?php echo $item->key; ?></i></td>
                    <td><i><?php echo $item->secret; ?></i></td>
                    <td>
                        <?php echo ( $item->status ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'consumers', 'action' => 'status', 'id' => $item->getIdentity()), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Disable it'))), array('class' => 'smoothbox')) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'consumers', 'action' => 'status', 'id' => $item->getIdentity()), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Enable it'))), array('class' => 'smoothbox')) ) ?>
                    </td>
                    <td>
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'consumers', 'action' => 'tokens', 'consumer_id' => $item->getIdentity()), $this->translate('OAuth Tokens')); ?>
                        |
                        <?php
                        echo $this->htmlLink(
                                array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'consumers', 'action' => 'edit', 'id' => $item->getIdentity()), $this->translate("edit"))
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br />

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no OAuth Consumer available yet.") ?>
        </span>
    </div>
<?php endif; ?>