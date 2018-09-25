<?php
/**
 * 下级用户管理
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Controller as BaseController;
use App\Repository\PromoteConfigRepository;
use App\Repository\CeilPointRepository;
use App\Repository\UserInfoRepository;
use App\Repository\DomainRepository;
use App\Repository\GameRepository;
use App\Repository\ReturnPointRepository;
use app\Validator\PromoteConfigValidator;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use \Logger; 


class SubuserController extends BaseController
{
	private $user;
	
    public function __construct(GameRepository $_gameRepo, PromoteConfigRepository $_pRepo, ReturnPointRepository $_rpRepo,
		CeilPointRepository $_cpRepo, UserInfoRepository $_uiRepo, DomainRepository $_dRepo,
		PromoteConfigValidator $_validator)
    {
        $this->gameRepo = $_gameRepo;
        $this->promteConfigRepo = $_pRepo;
		$this->returnPointRepo = $_rpRepo;
		$this->ceilPointRepo = $_cpRepo;
		$this->userInfoRepo = $_uiRepo;
		$this->domainRepo = $_dRepo;
        $this->validator = $_validator;

    }

	protected function getUser() {
		return Auth::user();
	}
	
	
	public function getGameList() {
		$this->user = $this->getUser();
		
		$gameList = $this->gameRepo->findByField('status', 1)->toArray();
		$userInfo = $this->userInfoRepo->findByField('uid', $this->user->id)->first();
		$ceilPoint = $this->ceilPointRepo->findByField('uid', $this->user->id)->toArray(); // 上级代理给下级代理最大返点限制
		$returnPoint = $this->returnPointRepo->findByField('promote_id', $userInfo->promote_id)->toArray(); // 上级返点数

		foreach ($gameList as &$val) {
			unset($val['status']);
			$val['point'] = '0';
			$val['maxPoint'] = $val['max_point'];
			$val['inputPoint'] = "";

			if (count($ceilPoint) > 0) {
				foreach($ceilPoint as $ceilV) {
					if ($ceilV->game_id == $val['id']) {
						$val['maxPoint'] = isset($ceilV->max_point) ? $ceilV->max_point : $val['max_point'];
						break;
					}
				}
			}
			if ($userInfo->hierarchy == 1) {
				$returnPoint = $returnPointModel->where([['uid', $this->user->id], ['promote_id', 0]])->get(); // 上级返点数
			} 
			foreach ($returnPoint as $rp) {
				if ($rp->game_id == $val['id']) {
					$val['point'] = $rp->point;
					$val['maxPoint'] = ($val['maxPoint'] > $val['point']) ? $val['point'] : $val['maxPoint'];
				}
			}
			unset($val['max_point']);
		}
		
		return response()->json(['data' => ['hierarchy' => $userInfo->hierarchy, 'gameList' => $gameList], 'code' => 200]);
	}
	
}