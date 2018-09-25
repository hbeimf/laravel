<?php
/**
 * Created by PhpStorm.
 * User: Michael Wang
 * Date: 12/6/17
 * Time: 2:40 PM
 */

namespace App\Repository\Criteria;


use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class RangeRequestCriteria implements CriteriaInterface
{

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     * Retrieve the soft delete record
     *
     * @param \Illuminate\Database\Eloquent\Builder  $builder
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($builder, RepositoryInterface $repository)
    {

        $rangeField = $this->request->get('rangeField');
        $min        = $this->request->get('rangeMin');
        $max        = $this->request->get('rangeMax');

        $dateField  = $this->request->get('dateField');
        $dateMin    = $this->request->get('dateMin');
        $dateMax    = $this->request->get('dateMax');

        $dateMin    = date('Y-m-d H:i:s',$dateMin);
        $dateMax    = date('Y-m-d H:i:s',$dateMax);


        if($rangeField && $min){

            $builder->where($rangeField,'>=',$min);
        }

        if($rangeField && $max){

            $builder->where($rangeField,'<=',$max);
        }

        if($dateField && $dateMin){

            $builder->where($dateField,'>=',$dateMin);
        }

        if($dateField && $dateMax){
            $builder->where($dateField,'<=',$dateMax);
        }


        return $builder;
    }
}