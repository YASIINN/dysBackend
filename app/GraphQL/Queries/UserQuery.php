<?php

namespace App\GraphQl\Queries;

class UserQuery 
{
    public function all(){
        return \App\User::all();
    }
    public function find($root, array $args){
        return \App\User::find($args['id']);
    }
    public function paginate($root, array $args){
        return \App\User::query()->paginate($args["count"], ["*"],"page",$args["page"]);

    }
}
