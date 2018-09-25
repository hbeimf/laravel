<?php

/*
 * 打码量表
 * 主要用于存储打码量和稽核打码量是否达标
 * 每次存入和取出都写入一条打码量记录,取出时成功时,在计入一条打码量信息在这里
 */
namespace App\Http\Model;

use \Illuminate\Database\Eloquent\Model;
use App\Http\Model\Users;
use App\Http\Model\Inmoney;


//打码量表 
class Audit extends Model {
    
    protected $table = 'bw_audit';
    
    public $timestamps = true;
    
//    protected $fillable = ['uid', 'audit_total', 'in_id', 'status', 'out_id'];
  
	public function Users()
    {
        return $this->belongsTo(Users::class, 'uid')->select("name","id");
    }
	
	public function Inmoney()
    {
        return $this->belongsTo(Inmoney::class, 'in_id');
    }
}
