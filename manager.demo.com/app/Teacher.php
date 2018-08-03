<?php
namespace App;

// use Illuminate\Database\Capsule\Manager as DB;
use \Illuminate\Database\Eloquent\Model;

class Teacher extends Model {
	protected $table = 't_teacher';

	public $timestamps = false;

	public function getRowById($id) {
		return $this->where('id', $id)->first()->toArray();
	}

	public function getRowBySchoolId($school_id) {
		return $this->where('school_id', $school_id)->get()->toArray();
	}

	public function getRowBySchoolIdEnabled($school_id) {
		return $this->where('school_id', $school_id)->where('is_enabled', '=', 1)->get()->toArray();
	}

	public function getRowBySchoolIdEnabledCourseType($school_id, $course_type) {
		return $this->where('school_id', $school_id)->where('is_enabled', '=', 1)->whereRaw("FIND_IN_SET('{$course_type}', course_type)")->get()->toArray();
	}

}
