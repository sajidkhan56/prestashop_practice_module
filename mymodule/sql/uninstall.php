<?php
 
 $sqls = array();
 $sqls[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'news_lang`';
 $sqls[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'news`';
 foreach ($sqls as $sql)
 {
  if (!Db::getInstance()->execute($sql))
 }
 return false;
 