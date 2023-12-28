--
-- Update classified icon
--

UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"classified_general","icon":"fa-newspaper-o"}'
WHERE `name` = 'core_main_classified';
