<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    tokens.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
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


<div>
    <?php
    echo $this->htmlLink(array('action' => 'manage', 'reset' => false), $this->translate('Back to API Consumers'), array(
        'class' => 'buttonlink icon_siteapi_admin_back',
    ))
    ?>
</div>

<br />

<h3>
    <?php echo $this->translate("%s - OAuth Tokens", $this->consumer->title); ?>
</h3>

<p class="description">
    <?php echo $this->translate("Here, you will be able to see and manage the OAuth Tokens created for users who are using this API client. You will also be able to delete or revoke desired OAuth tokens. Deleting an OAuth token will log-out the respective user on that Client. Revoking a user's OAuth token will not allow any API responses to be sent for that user's API calls from this Client."); ?>
</p>

<div class='admin_search'>
    <div class="clear">
        <div class="search">
            <form method="get" class="global_form_box">
                <div>
                    <label>
                        <?php echo $this->translate("User") ?>
                    </label>
                    <?php if (empty($this->displayname)): ?>
                        <input type="text" name="displayname" /> 
                    <?php else: ?>
                        <input type="text" name="displayname" value="<?php echo $this->displayname ?>"/>
                    <?php endif; ?>
                </div>

                <div>
                    <label>
                        <?php echo $this->translate("Email") ?>
                    </label>
                    <?php if (empty($this->email)): ?>
                        <input type="text" name="email" /> 
                    <?php else: ?>
                        <input type="text" name="email" value="<?php echo $this->email ?>"/>
                    <?php endif; ?>
                </div>

                <div>
                    <label>
                        <?php echo "Revoked"; ?>
                    </label>
                    <select id="revoke" name="revoke">
                        <option value="2" ></option>     
                        <?php
                        if ($this->revoke == 1):
                            echo '<option value="1" selected="selected">Yes</option>';
                        else:
                            echo '<option value="1">Yes</option>';
                        endif;


                        if ($this->revoke == 0):
                            echo '<option value="0" selected="selected">No</option>';
                        else:
                            echo '<option value="0">No</option>';
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
            <?php echo $this->translate(array("%s token found.", "%s tokens found.", $count), $this->locale()->toNumber($count))
            ?>
        </div>
        <div>
            <?php echo $this->paginationControl($this->paginator); ?>
        </div>
    </div>
<?php endif; ?>

<br />


<?php if (!empty($this->paginator) && count($this->paginator)): ?>
    <table class='admin_table'>
        <thead>
            <tr>
                <th class='admin_table_short'>Token ID</th>
                <th><?php echo $this->translate("User ID") ?></th>
                <th><?php echo $this->translate("User") ?></th>
                <th><?php echo $this->translate("Email") ?></th>
                <th><?php echo $this->translate("Revoked") ?></th>
                <th><?php echo $this->translate("Creation Date") ?></th>
                <th><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->paginator as $item): ?>
                <tr>
                    <td><?php echo $item->token_id; ?></td>
                    <td><?php echo $item->user_id; ?></td>
                    <?php
                    $user = Engine_Api::_()->getItem('user', $item->user_id);
                    ?>
                    <td title="<?php echo $displayname ?>">
                        <?php
                        echo $this->htmlLink($user->getHref(), $this->string()->truncate($user->getTitle(), 10), array('target' => '_blank'))
                        ?>
                    </td>

                    <td email="<?php echo $user->email; ?>">
                        <?php
                        echo $this->htmlLink($user->getHref(), $this->string()->truncate($user->email, 30), array('target' => '_blank'))
                        ?>
                    </td>

                    <?php
                    $revoked = !empty($item->revoked) ? 'Yes' : 'No';
                    ?>

                    <td>
                        <?php
                        if (!empty($item->revoked)) {
                            echo $this->htmlLink(
                                    array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'consumers', 'action' => 'revoked', 'id' => $item->token_id), 'Yes', array('class' => 'smoothbox'));
                        } else {
                            echo $this->htmlLink(
                                    array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'consumers', 'action' => 'revoked', 'id' => $item->token_id), 'No', array('class' => 'smoothbox', 'title' => 'Click to revoke API access.'));
                        }
                        ?>
                    </td>
                    <td><?php echo $this->locale()->toDateTime($item->creation_date); ?></td>
                    <td>
                        <?php
                        echo $this->htmlLink(
                                array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'consumers', 'action' => 'delete-token', 'id' => $item->token_id), $this->translate("delete"), array('class' => 'smoothbox'))
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
            <?php echo $this->translate("No OAuth Tokens are available currently for this API client.") ?>
        </span>
    </div>
<?php endif; ?>