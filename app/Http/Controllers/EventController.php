<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Event;
use App\Models\EventGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use function PHPUnit\Framework\isEmpty;

class EventController extends Controller
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

        $groups = EventGroup::all()->where('user_id', $this->user['id']);

        $users_events = [];
        foreach ($groups as $group) {
            array_push($users_events, [
                'my_role' => $group['role'],
                'event' => Event::find($group['event_id'])
                ]);
        }

        return response()->json([
            'my_events' => Event::all()->where('user_id', $this->user['id']),
            'users_events' => $users_events,
        ], 200);
    }

    public function create(Request $request)
    {
        if (!$this->user) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }
        $correctRequest = $request->only('title', 'content', 'finish_at', 'type', 'color');
        $correctRequest['user_id'] = $this->user['id'];

        try {
            $newEvent = Event::create($request->all());
        } catch (Exception $exception) {
            return response()->json(['message' => 'incorrect_request_data'], 400);
        }

        return response()->json($newEvent, 200);
    }

    public function byId($id)
    {
        if (!$this->user) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }
        $event = Event::find($id);
        $group = EventGroup::all()->where('user_id', $this->user['id'])->where('event_id', $id)->first();

        if (!$event) {
            return response()->json(['message' => 'no_such_event'], 400);
        }
        
        if ($event['user_id'] != $this->user['id'] && !$group) {
            return response()->json(['message' => 'is_not_user\'s_event'], 400);
        }

        if (!$group) {
            return response()->json([
                'my_status' => 'my_event',
                'event' => $event
            ], 200);
        } else {
            return response()->json([
                'my_status' => 'user_event',
                'my_role' => $group['role'],
                'user' => User::find($group['user_id']),
                'event' => $event
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        if (!$this->user) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        $event = Event::find($id);
        $group = EventGroup::all()->where('user_id', $this->user['id'])->where('event_id', $id)->first();

        if (!$event) {
            return response()->json(['message' => 'no_such_event'], 400);
        }

        if ($event['user_id'] != $this->user['id'] && !$group) {
            return response()->json(['message' => 'is_not_user\'s_event'], 400);
        }

        $correctRequest = $request->only('title', 'content', 'finish_at', 'type', 'color');
        $correctRequest['user_id'] = $this->user['id'];

        if (!$group) {
            try {
                $event->update($correctRequest);
            } catch (Exception $exception) {
                return response()->json(['message' => 'incorrect_request_data'], 400);
            }

            return response()->json([
                'my_status' => 'my_event',
                'event' => $event
            ], 200);
        } else if ($group['role'] == 'admin') {
            try {
                $event->update($correctRequest);
            } catch (Exception $exception) {
                return response()->json(['message' => 'incorrect_request_data'], 400);
            }

            return response()->json([
                'my_status' => 'user_event',
                'my_role' => $group['role'],
                'user' => User::find($group['user_id']),
                'event' => $event
            ], 200);

        } else {
            return response()->json('user_is_not_creator_or_admin', 400);
        }
    }

    public function destroy($id)
    {
        if (!$this->user) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'no_such_event'], 400);
        }

        if ($event['user_id'] != $this->user['id']) {
            return response()->json(['message' => 'is_not_user\'s_event'], 400);
        }

        Event::destroy($id);

        return response()->json(['message' => 'event_successful_deleted'], 200);
    }

}
