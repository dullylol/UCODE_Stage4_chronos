<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\EventGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;

class GroupController extends Controller
{
    public function __construct()
    {
        try {
            $this->user = JWTAuth::toUser(JWTAuth::getToken());
        } catch (JWTException $exception) {
            $this->user = null;
        }
    }


    public function all()
    {
        if (!$this->user) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        return EventGroup::all()->where('user_id', $this->user['id']);
    }

    public function create(Request $request)
    {
        if (!$this->user) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        if (!isset($request['event_id']) || !isset($request['user_id']) || !isset($request['role'])) {
            return response()->json(['message' => 'incorrect_request_data'], 400);
        }
        
        if (!Event::all()->where('id', $request['event_id'])->where('user_id', $this->user['id'])->first()) {
            return response()->json(['message' => 'it_is_not_user\'s_event'], 400);
        }

        if ($this->user['id'] == $request['user_id']) {
            return response()->json(['message' => 'it_is_event\'s_creator'], 400);
        }

        if (EventGroup::all()->where('user_id', $request['user_id'])->where('event_id', $request['event_id'])->first()) {
            return response()->json(['message' => 'such_group_row_already_exist'], 400);
        }
        
        try {
            return EventGroup::create($request->all());
        } catch (Exception $exception) {
            return response()->json(['message' => 'incorrect_request_data'], 400);
        }
    }

    // can delete to confidential
    public function byId($id)
    {
        if (!$this->user) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        $groupRow = EventGroup::find($id);

        if (!$groupRow) {
            return response()->json(['message' => 'no_such_group_row'], 404);
        }

        return response()->json($groupRow, 200);
    }

    public function update(Request $request, $id)
    {
        if (!$this->user) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        $groupRow = EventGroup::find($id);

        if (!$groupRow) {
            return response()->json(['message' => 'no_such_group_row'], 400);
        }

        $event = Event::find($groupRow['event_id']);

        if ($this->user['id'] != $event['user_id']) {
            return response()->json(['message' => 'user_cannot_edit_role_in_group_of_another_users'], 400);
        }

        $correctRequest = $request->only('role');

        try {
            if (isset($correctRequest['role'])) {
                $groupRow->update($correctRequest);
            }

        } catch (Exception $exception) {
            return response()->json(['message' => 'incorrect_request_data'], 400);
        }

        return $groupRow;
    }

    public function destroy($id)
    {
        if (!$this->user) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        $groupRow = EventGroup::find($id);

        if (!$groupRow) {
            return response()->json(['message' => 'no_such_group_row'], 400);
        }

        if ($this->user['id'] != $groupRow['user_id']) {
            return response()->json(['message' => 'user_cannot_delete_group_row'], 400);
        }

        EventGroup::destroy($id);

        return response()->json(['message' => 'user_successful_deleted'], 200);
    }
}
