CREATE TABLE xi18n_languages (
  lang_id int(5) unsigned NOT NULL auto_increment,
  lang_title varchar(100) NOT NULL,
  dirname varchar(30) NOT NULL,
  charset varchar(15) NOT NULL,
  code varchar(3) NOT NULL,
  codeml varchar(3) NOT NULL,
  PRIMARY KEY  (lang_id)
) TYPE=MyISAM;

INSERT INTO `xi18n_languages` VALUES ('', 'English', 'english', 'ISO-8859-1', 'en' , 'en' );
INSERT INTO `xi18n_languages` VALUES ('', 'French', 'french', 'ISO-8859-1', 'fr', 'fr' );
INSERT INTO `xi18n_languages` VALUES ('', 'FrenchUTF8', 'frenchutf8', 'UTF-8', 'fr', 'fr');
INSERT INTO `xi18n_languages` VALUES ('', 'Greek', 'greek', 'UTF-8', 'el', 'el');