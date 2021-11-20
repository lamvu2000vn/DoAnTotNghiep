<?php

namespace App\Http\Controllers;

use App\Traits\PushNotificationTrait;

class PushNotificationController extends Controller
{

    use PushNotificationTrait;

    public function sendPush($deviceToken, $title, $body)
    {
           $totalUnread = 1;
           $data = [
                'func_name' => config('firebase.notification.func'),
                'screen' => config('firebase.notification.screen'),
                'total_unread' => $totalUnread,
                'total_count' => 2,
                'device_type' => 'android', // Loại device, có thể là androi, web, ios
            ];
            $content = [
                "title" => $title, // tiêu đề tin nhắn
                "body" => $body, // nội dung tin nhắn
                'badge' => $totalUnread, // số message chưa đọc
                'sound' => config('firebase.sound') // âm báo tin nhắn
            ];

            // Push notification
            $this->pushMessage($deviceToken, $content, $data);
    }
}
?>