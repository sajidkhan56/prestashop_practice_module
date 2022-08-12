<?php
class MyNews extends ObjectModel {

	public $active;
	public $tittle;
	public $description;

	public static $definition = [
		'table' => 'news',
		'primary' => 'id_news',
		'multilang' => true,
		'fields' => [
			'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
			'tittle' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
			'description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255],
		],

	];

	public static function allRecords($lang) {
		$sql = new DbQuery();
		$sql->select('nl.id_news, n.active, nl.tittle, nl.description');
		$sql->from('news', 'n');
		$sql->innerJoin('news_lang', 'nl', 'n.id_news = nl.id_news AND nl.id_lang =' . (int) $lang);
		return Db::getInstance()->executeS($sql);
	}

	public static function tittleFrontoffice($lang) {
		$sql = new DbQuery();
		$sql->select('n.id_news');
		$sql->select('tittle');
		$sql->from('news', 'n');
		$sql->innerJoin('news_lang', 'nl', 'n.id_news = nl.id_news AND nl.id_lang =' . (int) $lang);
		$sql->where('n.active = ' . pSQL(1) . '');
		return Db::getInstance()->executeS($sql);
	}

	public static function descriptionFrontoffice($lang, $value) {
		$sql = new DbQuery();
		$sql->select('description');
		$sql->from('news', 'n');
		$sql->innerJoin('news_lang', 'nl', 'n.id_news = nl.id_news AND nl.id_lang =' . (int) $lang);
		$sql->where('n.active = ' . pSQL(1) . ' AND n.id_news =' . pSQL($value));
		return Db::getInstance()->executeS($sql);
	}
}
