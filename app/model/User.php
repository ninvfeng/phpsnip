<?php
namespace app\model;
use core\library\Mysql;

//用户model
class User extends Mysql{

	public function __construct(){
		parent::__construct();
		$this->table(uncamelize(array_pop(explode('\\',__CLASS__))));
	}

	public function allUser(){
		return $this->select();
	}
}