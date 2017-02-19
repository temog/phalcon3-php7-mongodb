<?php

use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

class Blog extends Base {

	protected static $_database = 'notify_unlimited';
	private static $_collection = 'blog';
	private static $_limit = 2;

	// create
	public static function createBlog($title, $body){

		if(! $title || ! $body){
			return false;
		}

		$doc = [
			'title' => $title,
			'body' => $body,
		];

		return self::create(self::$_collection, $doc);
	}

	// read
	public static function getLatestPost($page = 1){

		$page--;

		return self::find(self::$_collection, [], [
			'sort' => ['created_at' => -1],
			'skip' => self::$_limit * $page,
			'limit' => self::$_limit,
		]);
	}

	public static function get($id){

		return self::findOne(self::$_collection, ['_id' => $id]);

	}

	public static function getPaginator($page){

		$count = self::count(self::$_collection);
		//var_dump($count);

		// create pagination dummy data
		$data = [];
		for($i = 0; $i < $count; $i++){
			$data[] = ['id' => $i];
		}

		$paginator = new PaginatorArray([
			'data' => $data,
			'limit' => self::$_limit,
			'page' => $page,
		]);

		return $paginator->getPaginate();


		/*
		var_dump($p);
		var_dump($p->before);
		var_dump($p->next);
		var_dump($p->last);
		foreach($p->items as $item){
			var_dump($item);
		}
		exit;
		 */

	}

	// update
	public static function updateBlog($id, $title, $body){

		$doc = [
			'title' => $title,
			'body' => $body,
		];

		return self::update(self::$_collection, ['_id' => $id], $doc);
	}

	// delete
	public static function deleteBlog($id){

		return self::delete(self::$_collection, ['_id' => $id]);
	}

}

