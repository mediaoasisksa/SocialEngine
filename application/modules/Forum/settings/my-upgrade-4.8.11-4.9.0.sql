--
-- Update notification body of `forum_topic_reply`
--

UPDATE `engine4_activity_notificationtypes` SET
`body`= '{item:$subject} has {item:$postGuid:posted} on a {item:$object:forum topic} you posted on.'
WHERE `type`= 'forum_topic_reply';

--
-- Update `param` column in `engine4_core_menuitems` table
--

UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"forum_general","icon":"fa-comments"}'
WHERE `name` = 'core_main_forum';
