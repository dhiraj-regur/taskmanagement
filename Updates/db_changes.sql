CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `task` varchar(255) NOT NULL,
  `userId` int(10) NOT NULL,
  `projectId` int(10) NOT NULL,
  `urgent` int(10) NOT NULL DEFAULT '1',
  `important` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

=====================================================================================

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `projectName` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

=====================================================================================