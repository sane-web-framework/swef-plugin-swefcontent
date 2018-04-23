-- SWEF PLUGIN REGISTRATION --

INSERT IGNORE INTO `swef_config_plugin`
    (
      `plugin_Dash_Allow`, `plugin_Dash_Usergroup_Preg_Match`, `plugin_Enabled`,
    `plugin_Context_LIKE`, `plugin_Classname`, `plugin_Handle_Priority`
  )
  VALUES
    ( 1, '<^sysadmin$>', 1, 'dashboard', '\\Swef\\SwefContent', 2 ),
    ( 0, '', 1,  'www-%',  '\\Swef\\SwefContent',  2 );


-- SWEFCONTENT PROCEDURES --

DELIMITER $$

DROP PROCEDURE IF EXISTS `swefContentPermitsLoad`$$
CREATE PROCEDURE `swefContentPermitsLoad`(IN `ctx` VARCHAR(64) CHARSET ascii, IN `pth` VARCHAR(255) CHARSET ascii)
BEGIN
  SELECT `content_Usergroup` AS `usergroup`
        ,`content_Directory` AS `directory`
        ,`content_Read_Permitted` AS `read_permitted`
        ,`content_Create_Permitted` AS `create_permitted`
        ,`content_Update_Permitted` AS `update_permitted`
        ,`content_Delete_Permitted` AS `delete_permitted`
  FROM `swefcontent_permit`
  WHERE ctx LIKE `content_Context_LIKE`
    AND pth LIKE CONCAT (`content_Directory`,'/%')
  ORDER BY LENGTH (`content_Directory`) DESC;
END$$

DELIMITER ;


-- SWEFCONTENT TABLES --

CREATE TABLE IF NOT EXISTS `swefcontent_permit` (
  `content_Context_LIKE` varchar(64) CHARACTER SET ascii NOT NULL,
  `content_Usergroup` varchar(64) CHARACTER SET ascii NOT NULL,
  `content_Directory` varchar(255) CHARACTER SET ascii NOT NULL,
  `content_Read_Permitted` int(1) unsigned NOT NULL,
  `content_Create_Permitted` int(11) NOT NULL,
  `content_Update_Permitted` int(1) unsigned NOT NULL,
  `content_Delete_Permitted` int(1) unsigned NOT NULL,
  PRIMARY KEY (`content_Context_LIKE`,`content_Usergroup`,`content_Directory`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `swefcontent_permit` (`content_Context_LIKE`, `content_Usergroup`, `content_Directory`, `content_Read_Permitted`, `content_Create_Permitted`, `content_Update_Permitted`, `content_Delete_Permitted`) VALUES
('dashboard', 'admin',  '/media/content/admin', 1,  1,  1,  1),
('dashboard', 'admin',  '/media/content/open',  1,  1,  1,  1),
('dashboard', 'admin',  '/media/content/public',  1,  1,  1,  1),
('dashboard', 'sysadmin', '/media/content', 1,  1,  1,  1),
('www-%', 'anon', '/media/content/open',  1,  0,  0,  0),
('www-%', 'public', '/media/content/open',  1,  0,  0,  0),
('www-%', 'public', '/media/content/public',  1,  0,  0,  0);

