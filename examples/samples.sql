CREATE TABLE `categories` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `categories` VALUES (1, 'Fruits');
INSERT INTO `categories` VALUES (2, 'Drinks');
INSERT INTO `categories` VALUES (3, 'Fast-food');
INSERT INTO `categories` VALUES (4, 'Vegetables');

CREATE TABLE `products` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `category` int(8) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `category` (`category`)
) TYPE=MyISAM;

INSERT INTO `products` VALUES (1, 'Apples', 'Natural and delicious apples from southern Poland, only for 4,99 zl per kg!', 1);
INSERT INTO `products` VALUES (2, 'Pears', 'Pears from mr Gajewski''s farm near Zielona Gora, always fresh. Only for 4,30 zl per kg.', 1);
INSERT INTO `products` VALUES (3, 'Mineral water', 'Very good regular mineral water from Tatras.', 2);
INSERT INTO `products` VALUES (4, 'Apple juice', 'Apple juice from Polish fruits. Certified with ISO 9001.', 2);

CREATE TABLE `templates` (
  `id` smallint(6) NOT NULL auto_increment,
  `title` varchar(40) NOT NULL,
  `code` text NOT NULL,
  `lastmod` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `lastmod` (`lastmod`)
) ENGINE=MyISAM;

INSERT INTO `templates` VALUES (1, 'example17', '<html> \r\n<head> \r\n  <title>Example 17</title> \r\n</head> \r\n<body>\r\n<h1>Example 17</h1>\r\n<h3>Custom resources</h3>\r\n<p>This template is loaded from the database. You can write your own template resources and use it in OPT,\r\ninstead of using files.</p>\r\n<hr/>\r\n\r\n<p>{$current_date}</p>\r\n</body> \r\n</html>', 1149692299);
