<?php


namespace App\GraphQL\Types;

class UserType {
    public function jobs(User $user){
        return $user->jobs()->get();
    }
}