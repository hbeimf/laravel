<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;

/**
 * Description of Base
 *
 * @author DELL
 */
class Base extends Model {
	//put your code here

	/**
	 * @todo 返回分页列表和分页信息,隐藏的信息 $page 已经通过 $request->get('page')获取
	 * 		 如果提交了搜索条件,那么附加搜索条件搜索
	 * * */
	public function getPages($limit = 10, $where = false) {

		if (!empty($where)) {
			$list = $this->where($where)->paginate($limit);
		} else {
			$list = $this->paginate($limit);
		}

		$_page['data'] = &$list;
		$_page['totalNum'] = $list->total();
		$_page['currentPage'] = $list->currentPage();
		$_page['totalPage'] = ceil($list->total() / $limit);

		return $_page;
	}

	/**
	 * @todo 返回去掉了添加时间和修改时间信息的部分信息,为防止意外修改数据库
	 *        返回给一个array
	 * * */
	public function getMini() {
		$item = $this->toArray();
		unset($item['created_at']);
		unset($item['updated_at']);
		unset($item['deleted_at']);
		return $item;
	}
	
	public function getIt(){
		return $this->toArray();
	}

	/**
	 * @todo 把一整个对象转成数组
	 * * */
	public function getFull() {
		return $this->toArray();
	}

}
