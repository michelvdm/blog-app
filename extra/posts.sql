SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` bigint(20) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `publishdate` datetime NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status` enum('Draft','Published','Retired','') NOT NULL DEFAULT 'Published',
  `body` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE posts ADD PRIMARY KEY (`id`);
ALTER TABLE posts MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
ALTER TABLE posts ADD FULLTEXT( subject, description, body );
