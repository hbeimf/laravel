<?php

namespace App\Listeners;


use Dingo\Api\Event\ResponseWasMorphed;
use Illuminate\Support\Facades\DB;


class AddQueryLogToResponse
{
    public function handle(ResponseWasMorphed $event)
    {
        if ( is_array($event->content) && !isset($event->content['meta']['debug']) && env('APP_DEBUG')) {
            $queries = $this->getQueryLog();
            $event->content['meta']['DBQuery'] = $queries;

        }
    }


    protected function getQueryLog()
    {
        $queries = [];
        foreach(DB::getQueryLog() as $query)
        {
            $queryString  = array_get($query,'query');
            $bindings = array_get($query,'bindings');
            $time = array_get($query,'time');

            $queries[] = [

                'query' => vsprintf(str_replace('?', '%s',$queryString),$bindings),
                'time' => $time
            ];
        }
        return $queries;

    }
}