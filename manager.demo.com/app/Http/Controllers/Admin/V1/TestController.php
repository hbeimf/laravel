<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Repository\TestRepository;
use App\Transformer\TestTransformer;
// use Illuminate\Database\Eloquent\Model;
use App\Validator\TestValidator;
use Illuminate\Http\Request;

class TestController extends BaseController {

	protected $validator;

	protected $repo;

	public function __construct(TestRepository $_repo, TestValidator $_validator) {
		$this->repo = $_repo;
		$this->validator = $_validator;

	}

	public function listTest() {
		// event(new \App\Events\QueryLog());
		return $this->response()->paginator($this->repo->paginate(), new TestTransformer());

	}

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