<?php

namespace App\Listeners;


use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\DB;


class AddDBQueryLogToResponse
{
    public function handle(RequestHandled $event)
    {
        $data = $event->response->getData();

        if(!empty($data) && env('APP_DEBUG'))
        {
            $data->DBQuery = $this->getQueryLog();
            $event->response->setData($data);
        }
    }


    protected function getQueryLog()
    {
        $queries = [];
        foreach(DB::getQueryLog() as $query)
        {
            $queryString  = array_get($query,'query');
            $bindings = array_get($query,'bindings');
            $bindings = array_map(function($item) {
                if($item instanceof  \DateTime)
                {
                    return $item->format('Y-m-d H:i:s');
                }
            },$bindings);
            $time = array_get($query,'time');

            $queries[] = [

                'query' => vsprintf(str_replace('?', '%s',$queryString),$bindings),
                'time' => $time
            ];
        }
        return $queries;

    }
}