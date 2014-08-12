<?php die();?>

#add "throttles" table - 2014/07/02
CREATE TABLE IF NOT EXISTS `throttles` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL,
  `domain` varchar(64) NOT NULL,
  `channel` int(1) NOT NULL,
  `creative_id` int(7) NOT NULL,
  `campaign_id` int(7) NOT NULL,
  `category_id` int(7) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

#add "delay_until" for queue_send table - 2014/07/05
ALTER TABLE `queue_send` ADD `delay_until` datetime DEFAULT NULL;

#add "delay_seconds" for queue_send table - 2014/07/05
ALTER TABLE `queue_send` ADD `delay_seconds` int(7) DEFAULT NULL;

#add channel SMTP.com - 2014/07/15
INSERT INTO `email`.`channels` (`id`, `name`, `type`, `class`, `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass`) VALUES ('2', 'SMTP.com', '2', 'SmtpCom', NULL, NULL, NULL, NULL);

# add table for log_scheduler's 2014/07/22
    CREATE TABLE IF NOT EXISTS `log_scheduler` (
        `id` int(7) NOT NULL AUTO_INCREMENT,
        `eligible_campaign_count` INT(8),
        `eligible_campaign_ids` VARCHAR(50),
        `chosen_campaign_id` INT(8),
        `chosen_campaign_attribute` text CHARACTER SET latin1,
        `lead_count` INT(8),
        `leads` text CHARACTER SET latin1,
        `queued_lead_count` INT(8),
        `message` VARCHAR(50),
        PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# add fields for table for log_scheduler's 2014/07/22
    ALTER TABLE `log_scheduler`
    ADD `scheduler_name` VARCHAR(20) DEFAULT NULL AFTER `id`,
    ADD `create_time` datetime DEFAULT NULL;

#add "source_campaign" to throttles table - 2014/07/22
ALTER TABLE `throttles` ADD `source_campaign` VARCHAR(32) DEFAULT NULL AFTER `domain` ;

#add "tld_group" to throttles table - 2014/07/22
ALTER TABLE `throttles` ADD `tld_group` VARCHAR(32) DEFAULT NULL AFTER `source_campaign` ;

#update log_scheduler, change eligible_campaign_ids 
#for varchar(50) to varchar(255) 2014/07/22
    ALTER TABLE log_scheduler CHANGE `eligible_campaign_ids` `eligible_campaign_ids` VARCHAR(255);

# add index for create_time 2014/07/23
    ALTER TABLE log_scheduler ADD INDEX `create_time`(`create_time`);

#add "delay_seconds" for queue_send table - 2014/07/23
ALTER TABLE `error_log_esp` ADD `delay_seconds` int(7) DEFAULT NULL;

# new user table - 2014/08/06
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(7) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(100) NOT NULL,
  `displayed_name` varchar(250) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `registered` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `email`.`users` (`id`, `username`, `password`, `email`, `displayed_name`, `status`, `registered`) VALUES ('1', 'loiphamle', '173c3fa9ec058c16ba234590c9af2538', 'loiphamle@gmail.com', 'Loi Dep Trai', '1', '2014-08-06 16:13:47');

# add index for error_log_debug table - 2014/08/11
ALTER TABLE error_log_debug ADD INDEX `key_datetime` (`datetime`);