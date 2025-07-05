<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AuthUserScope implements Scope
{
    private string $field;

    public function __construct(string $field = 'user_id')
    {
        $this->field = $field;
    }

    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $builder->where($this->field, Auth::id());
        }
    }
}
