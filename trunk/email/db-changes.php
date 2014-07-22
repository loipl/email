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
    ADD `create_time` datetime DEFAULT NULL>>>>>>> .r665

#add "source_campaign" to throttles table - 2014/07/22
ALTER TABLE `throttles` ADD `source_campaign` VARCHAR(32) DEFAULT NULL AFTER `domain` ;
