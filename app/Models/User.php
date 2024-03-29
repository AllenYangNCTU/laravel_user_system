<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'birthdate',
        'password'
    ];
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
?>