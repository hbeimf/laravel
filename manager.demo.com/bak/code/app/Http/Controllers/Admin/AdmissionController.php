<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Admin\V1\Controller as BaseController;
use App\Repository\AdmissionRepository;
use App\Transformer\AdmissionTransformer;
use App\Validator\AdmissionValidator;
use Illuminate\Http\Request;
use App\Http\Model\Admission;
use Illuminate\Support\Facades\DB;

class AdmissionController extends BaseController
{
    protected $validator;

    protected $repo;


    public function __construct(AdmissionRepository $_repo, AdmissionValidator $_validator) {
        parent::__construct();
        $this->repo = $_repo;
        $this->validator = $_validator;

    }



    public function listAdmission()
    {
        return $this->response()->paginator($this->repo->paginate(),new AdmissionTransformer());
    }



    public function get($id)
    {
        return $this->response()->item($this->repo->find($id), new AdmissionTransformer());
    }



    public function add(Request $request)
    {
        $data=$request->all();
        $model = $this->repo->create($data);
        $response = [
            'message' => '数据创建成功.',
            'data'    => $model->toArray(),
        ];
        return $this->response()->array($response);
    }



    public function update(Request $request, $id)
    {
        $model = $this->repo->update($request->all(), $id);

        $response = [
            'message' => 'test updated.',
            'data' => $model->toArray(),
        ];
        return $this->response()->array($response);
    }



    public function updateStatus(Request $request,$id)
    {   //
        $data=$request->all();
        $data['id']=$id;
        $this->validator->with($data)->setId($id)->passesOrFail('updateStatus');
        $model=$this->repo->find($id);
        $model['status']=$model['status']==1?2:1;
        $model->save();

        $response = [
            'message' => '修改状态成功',
            'data' => $model->toArray(),
        ];
        return $this->response()->array($response);
    }




    public function delete(Request $request,$id)
    {
        $data=$request->all();
        $data['id']=$id;
        $this->validator->with($data)->setId($id)->passesOrFail('updateStatus');

        $this->repo->delete($id);
        $response = [
            'message' => '分组删除成功.',
        ];
        return $this->response()->array($response);
    }


}