<?php
 
 $sqls = array();
 $sqls[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'news` (
                                `id_news` int(11) AUTO_INCREMENT,
                                `active`   TINYINT( 11 ),
                                 PRIMARY KEY (`id_news`)
                                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

 $sqls[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'news_lang` (
                                `id_news` int(11),
                                `id_lang` INT( 11 ),
                                `tittle` varchar(50),
                                `description` varchar(100),
                                 PRIMARY KEY (id_news, id_lang),
                                 FOREIGN KEY (`id_news`) REFERENCES ps_news(`id_news`)
                                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

 foreach ($sqls as $sql)
 {
  if (!Db::getInstance()->execute($sql))
 }
 return false;
