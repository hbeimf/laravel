<?php

namespace App\Http\Model;
use DB;
use \Illuminate\Database\Eloquent\Model;

class StatusLog extends Model {

    protected $table = 'bw_status_log';
    
    public function getStatusLog($uid){
	$sql = "select * from bw_status_log as a right join (select max(id) as bid from bw_status_log where uid = ".$uid;
	$sql .= " group by status_type) as b on a.id = b.bid order by a.status_type asc";
	$result =  DB::select($sql);
	
	$aData = [];
	if($result){
	    foreach ($result as $k=>$v){
		$aData['status'.$v->status_type]['status'.$v->status_type] = $v->status_value;
		$aData['status'.$v->status_type]['msg'] = $v->msg;
		$aData['status'.$v->status_type]['created_at'] = $v->created_at;
	    }
	}
	return $aData;
    }
    
}