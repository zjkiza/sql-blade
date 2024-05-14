<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Tests\Resource\model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];
    public $timestamps = false;
}
