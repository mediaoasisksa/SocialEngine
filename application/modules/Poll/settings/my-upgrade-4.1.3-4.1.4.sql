
UPDATE IGNORE `engine4_core_settings`
SET `name` = 'poll.maxoptions'
WHERE `name` LIKE 'poll%.maxOptions' ;

UPDATE IGNORE `engine4_core_settings`
SET `name` = 'poll.showpiechart'
WHERE `name` LIKE 'poll%.showPieChart' ;

UPDATE IGNORE `engine4_core_settings`
SET `name` = 'poll.canchangevote'
WHERE `name` LIKE 'poll%.canChangeVote' ;

UPDATE IGNORE `engine4_core_settings`
SET `name` = 'poll.perpage'
WHERE `name` LIKE 'poll%.perPage' ;
