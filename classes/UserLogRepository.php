<?php

class UserLogRepository
{
    public function Create(array $inputs)
    {
        UserLog::query()->create([
            'subscription_id' => $inputs['subscription_id'],
            'url' => $inputs['url'],
            'ip' => $inputs['ip'],
            'method' => $inputs['method'],
            'user_agent' => $inputs['user_agent'],
        ]);
    }

    public function Find($log_id)
    {
        return UserLog::query()->where('id', $log_id)->first();
    }

    public function FindAll($needle, $page, $limit)
    {
        $columns = [
            'id',
            'subscription_id',
            'url',
            'method',
            'ip',
            'user_agent',
            'created_at'
        ];

        $query = UserLog::query();

        foreach ($columns as $column) {
            $query->orWhere("$column", 'LIKE', "%$needle%");
        }
        return $query->orderBy('created_at', 'DESC')
            ->paginate($limit,['*'],'page',$page);
    }


    public function FindLogsBySubscription($subscription_id, $needle, $page, $limit)
    {
        return UserLog::where('subscription_id', $subscription_id)->where(function($q) use($needle){
            $q->where('id', 'LIKE', "%$needle%")
                ->orWhere('subscription_id', 'LIKE', "%$needle%")
                ->orWhere('url', 'LIKE', "%$needle%")
                ->orWhere('method', 'LIKE', "%$needle%")
                ->orWhere('ip', 'LIKE', "%$needle%")
                ->orWhere('user_agent', 'LIKE', "%$needle%")
                ->orWhere('created_at', 'LIKE', "%$needle%");
        })->orderBy('user_logs.created_at', 'DESC')
            ->paginate($limit,['*'],'page',$page);
    }


    public function FindLogsBySubscriptionCount($subscription_id, $date): int
    {
        return UserLog::where('subscription_id', $subscription_id)
            ->whereMonth('user_logs.created_at', $date->format('m'))
            ->whereYear('user_logs.created_at', $date->format('Y'))
            ->count();
    }
}