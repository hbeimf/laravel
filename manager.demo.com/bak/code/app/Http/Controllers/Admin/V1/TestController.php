<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Repository\TestRepository;
use App\Transformer\TestTransformer;
use App\Validator\TestValidator;
use Illuminate\Http\Request;

class TestController extends BaseController {

	protected $validator;

	protected $repo;

	public function __construct(TestRepository $_repo, TestValidator $_validator) {
		$this->repo = $_repo;
		$this->validator = $_validator;

	}

	// http://code.demo.com/v1/test/?page=1&limit=2&search=name:以色列&orderBy=id&sortedBy=asc&filter=users.id as uuid;users.name;A.*
	// http://code.demo.com/v1/test/?search=12314@qq.com&searchFields=email:=

	// http://code.demo.com/v1/test/?filter=users.id as uuid;users.name as NameX;A.*
	// http://code.demo.com/v1/test/?page=1&limit=2&search=name:以色列&orderBy=id&sortedBy=asc&filter=users.id as uuid;users.name;A.*
	public function listTest() {
		$limit = request('limit', 2);

		$page = $this->repo->scopeQuery(function ($query) {
			return $query->leftJoin('s_admin_info as A', 'users.id', '=', 'A.uid')
				->where('users.id', '=', '117');
		})->paginate($limit);

		return $this->response()->paginator($page, new TestTransformer());

		// return $this->response()->paginator($this->repo->scopeQuery(function ($query) {
		// 	return $query->leftJoin('s_admin_info as A', 'users.id', '=', 'A.uid');
		// })->paginate($limit, $fields), new TestTransformer());

	}

	// http://code.demo.com/v1/test/61
	public function getTest($id) {
		return $this->response()->item($this->repo->find($id), new TestTransformer());
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 */
	public function createTest(Request $request) {

		$this->validator->with($request->all())->passesOrFail('create');
		$model = $this->repo->create($request->all());
		$response = [
			'message' => '数据创建成功.',
			'data' => $model->toArray(),
		];
		return $this->response()->array($response);

	}

	/**
	 * @param \Illuminate\Http\Request $request
	 */
	public function updateTest(Request $request, $id) {

		$this->validator->with($request->all())->setId($id)->passesOrFail('update');
		$model = $this->repo->update($request->all(), $id);
		$response = [
			'message' => 'test updated.',
			'data' => $model->toArray(),
		];
		return $this->response()->array($response);

	}

	public function destroy($id) {
		$this->repo->delete($id);
		$response = [
			'message' => '账号删除.',
		];
		return $this->response()->array($response);
	}

}