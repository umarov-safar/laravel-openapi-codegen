<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

class CreateSomeRequest extends Request
{
    public function rules(): array
    {
        return [
            'id' => ['integer'],
            'name' => ['required', 'string'],
            'email' => ['string', 'email'],
        ];
    }
}
