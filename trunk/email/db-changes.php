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