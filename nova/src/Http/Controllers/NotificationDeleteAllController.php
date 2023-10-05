<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NotificationRequest;

class NotificationDeleteAllController extends Controller
{
    /**
     * delete all notification.
     *
     * @param \Laravel\Nova\Http\Requests\NotificationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(NotificationRequest $request)
    {
        $request->allOfMyNotifications()->delete();

        return response()->json();
    }
}
