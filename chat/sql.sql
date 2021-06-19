CREATE TABLE `chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text COLLATE utf8_bin NOT NULL,
  `color` text COLLATE utf8_bin NOT NULL,
  `msg` longtext COLLATE utf8_bin NOT NULL,
  `time` bigint(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin