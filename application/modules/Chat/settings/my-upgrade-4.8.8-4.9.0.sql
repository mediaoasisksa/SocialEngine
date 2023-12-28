--
-- Update `param` column in `engine4_core_menuitems` table
--

UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"default","module":"chat","icon":"fa-comment-o"}'
WHERE `name` = 'core_main_chat';
