<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\prtg_model;


class PRTGcontoller extends Controller
{
 
    public function search(Request $request)
    {
        $validator = validator()->make($request->only('device_ip'), [
            'device_ip' => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->get('device_ip');
            return response()->json(['error' => $errors[0]]);
        }

         return response()->json(prtg_model::PRTG($request->device_ip));
    }

    public function graph(Request $request)
    {
        $validator = validator()->make($request->only('device_ip','select'), [
            'device_ip' => 'required|ipv4',
            'select'    => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors[0]]);
        }

         return response()->json(prtg_model::GRAPH($request->device_ip,$request->select));
    }

    public function NameSearch(Request $request)
    {
        $validator = validator()->make($request->only('name'), [
            'name' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->get('name');
            return response()->json(['error' => $errors[0]]);
        }

         return response()->json(prtg_model::NAME_SEARCH_PRTG($request->name));
    }
     
     
}
