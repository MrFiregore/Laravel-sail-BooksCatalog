<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Requests\DooRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse;

    /**
     * @param   \App\Http\Requests\DooRequest   $request
     * @param                              $validators
     *
     * @return array
     */
    public function queryValidator(Request $request, $validators){
        $validated = $request->validate(
            $validators
        );
        return Arr::only($validated, array_keys($validators));
    }

    public function filterAll(DooRequest $request){
        return $this->queryValidator($request, ['all'=> 'nullable|trueboolean'])['all'] ?? true;
    }
}
