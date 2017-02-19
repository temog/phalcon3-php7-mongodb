<?php

use Phalcon\Di;
use Phalcon\Mvc\User\Component;

class Base extends Component {

	private static $_mongo = [];

	private static function mongo(){

		if(! isset(self::$_mongo[static::$_database])){

			$config = Di::getDefault()->get('config');

			self::$_mongo[static::$_database] =
				Di::getDefault()->get(static::$_database)->
				{$config->database->{$config->env}->{static::$_database}->dbname};
		}

		return self::$_mongo[static::$_database];
	}

	public static function find($collection, $where = [], $options = []){

		$where = self::convertWhere($where);

		$col = self::mongo()->{$collection};
		if(! $col->count($where, $options)){
			return false;
		}
		return $col->find($where, $options);
	}

	public static function findOne($collection, $where, $options = []){

		$where = self::convertWhere($where);

		$col = self::mongo()->{$collection};
		if(! $col->count($where, $options)){
			return false;
		}
		return $col->findOne($where, $options);
	}

	public static function count($collection, $where = [], $options = []){

		$where = self::convertWhere($where);

		$col = self::mongo()->{$collection};
		return $col->count($where, $options);
	}

	public static function create($collection, $data){

		$now = time();

		$data['created_at'] = $now;
		$data['updated_at'] = $now;

		$col = self::mongo()->{$collection};
		$result = $col->insertOne($data);
		if(! $result->getInsertedCount()){
			Logger::error([__METHOD__, $collection, $data]);
			return false;
		}

		return (string) $result->getInsertedId();
	}

	public static function createMany($collection, $data){

		$now = time();

		// 参照渡しで
		foreach($data as &$v){
			$v['created_at'] = $now;
			$v['updated_at'] = $now;
		}

		$col = self::mongo()->{$collection};
		$result = $col->insertMany($data);
		if(! $result->getInsertedCount()){
			Logger::error([__METHOD__, $collection, $data]);
			return false;
		}

		return true;
	}


	public static function update($collection, $where, $data){

		$where = self::convertWhere($where);

		$data['updated_at'] = time();

		$col = self::mongo()->{$collection};
		$result = $col->updateMany($where, ['$set' => $data]);

		if(! $result->getModifiedCount()){
			Logger::error([__METHOD__, $collection, $where, $data]);
			return false;
		}

		return true;
	}

	/*
		なかったら insert あったら update する便利メソッド
		insert で getUpsertedCount に追加
		update で getModifiedCount に追加
	 */
	public static function upsert($collection, $where, $data){

		$where = self::convertWhere($where);

		$now = time();
		$data['updated_at'] = $now;

		$col = self::mongo()->{$collection};
		$result = $col->updateMany($where,
			['$set' => $data, '$setOnInsert' => ['created_at' => $now]],
			['upsert' => true]);

		if(! $result->getUpsertedCount() && ! $result->getModifiedCount()){
			Logger::error([__METHOD__, $collection, $where, $data]);
			return false;
		}

		return true;
	}

	public static function delete($collection, $where){

		$where = self::convertWhere($where);

		$col = self::mongo()->{$collection};
		$result = $col->deleteMany($where);

		if(! $result->getDeletedCount()){
			Logger::error([__METHOD__, $collection, $where]);
			return false;
		}

		return true;
	}

	private static function convertWhere($where){

		if(isset($where['_id'])){
			$where['_id'] = new MongoDB\BSON\ObjectID($where['_id']);
		}

		foreach($where as $key => $val){
			if(is_array($val) && key($val) === '$regex'){
				$index = key($val);
				$where[$key] = new MongoDB\BSON\Regex(
					$val[$index][0], $val[$index][1]
				);
			}
		}

		return $where;
	}



}
