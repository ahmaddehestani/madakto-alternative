<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeSheetRequest;
use App\Http\Resources\TimeSheetResource;
use Illuminate\Http\Request;
use App\Models\TimeSheet;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Gate;

class TimeSheetController extends ApiController
{
    public function store(TimeSheetRequest $request)
    {
        date_default_timezone_set('Asia/Tehran');
        $key = env('JWT_TOKEN_KEY');
        $jwt = $request->token;
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $timeSheet = new TimeSheet();
        $timeSheet->user_id = $decoded->user_id;
        $timeSheet->shift_id = $request->shift_id;
        $timeSheet->department_id = $request->department_id;
        $timeSheet->date = date("Y-m-d H:i:s", $request->date);
        $timeSheet->save();
        return $this->successResponse("success", 201);
    }

    public function show(Request $request)
    {
        $key = env('JWT_TOKEN_KEY');
        $jwt = $request->token;
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $timeSheet = TimeSheet::with(['department', 'shift'])->where('user_id', $decoded->user_id)->orderBy('date')->get();
        return $this->successResponse(TimeSheetResource::collection($timeSheet), 200);
    }

    public function edit(TimeSheet $timeSheet)
    {
        return $this->successResponse($timeSheet, 200);
    }

    public function update(TimeSheetRequest $request)
    {
        date_default_timezone_set('Asia/Tehran');
        $key = env('JWT_TOKEN_KEY');
        $jwt = $request->token;
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $timeSheet = TimeSheet::find($request->id);
        if (Gate::allows('update', $timeSheet)) {
            TimeSheet::where('id', $request->id)->first()
                ->update([
                    "user_id" => $decoded->user_id,
                    "shift_id" => $request->shift_id,
                    "department_id" => $request->department_id,
                    "date" => date("Y-m-d H:i:s", $request->date),
                ]);
            return $this->successResponse("success", 200);
        } else {
            return $this->errorResponse("unauthorized", 403);
        }

    }

    public function destroy($id)
    {
        $timeSheet = TimeSheet::find($id);
        if (Gate::allows('delete', $timeSheet)) {
            TimeSheet::destroy($id);
            return $this->successResponse("success", 200);
        } else {
            return $this->errorResponse("unauthorized", 403);
        }
    }
}
