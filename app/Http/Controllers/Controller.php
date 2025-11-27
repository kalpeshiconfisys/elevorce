<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success($success, $msg, $data = [], $code = 200)
    {
        if ($success) {
            return response()->json([
                'success' => $success,
                'message' => $msg,
                'data' => $data,
            ], $code);
        } else {
            return response()->json([
                'success' => $success,
                'message' => $msg,
            ], $code);
        }
    }

    public function error($msg = NULL, $code = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $msg,
        ], $code);
    }

    public function getGoogleAccessToken()
    {

        $keyFilePath = public_path('json/elevorce.json');
        $client = new \Google\Client();
        $client->setAuthConfig($keyFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        return $token['access_token'];
    }

    public function sendPushNotification($device_token, $device_type, $title, $msg, $type, $leave_id=null)
    {
        $data['title'] = $title;
        $data['body'] = $msg;
        $data['device_type'] = $device_type;
        $data['leave_id'] = $leave_id;

        Log::info("[][][][][][][][][][][][][][][]");
        Log::info($device_token);
        Log::info("[][][][][][][][][][][][][][][][");

        $SERVER_API_KEY = self::getGoogleAccessToken();
        $headers = [
            'Authorization: Bearer ' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        if ($device_type == 'android') {
            $dataString = [
                "message" => [
                    "token" => $device_token,
                    "android" => [
                        "data" => $data,
                        "priority" => "high"
                    ]
                ]
            ];
        } else if ($device_type == "iOS") {
            $dataString = [
                "message" => [
                    "token" => $device_token,
                    "apns" => [
                        "headers" => [
                            "apns-priority" => "5",
                        ],
                        "payload" => [
                            "aps" => [
                                "sound" => "default",

                                // "title" => $data['title'],
                                // "body" => $data['body'],
                                "alert" => [
                                    "title" => $data['title'],
                                    "body" => $data['body'],
                                ],
                                "content-available" => 1,
                                "mutable-content" => 1,
                                // "category" => "customNotificationView",
                                // "sound" => $data['sound'],
                            ],
                            "data" => $data,
                        ]
                    ]
                ]
            ];
        } else {
            $dataString = [
                "message" => [
                    "token" => $device_token,
                    "data" => $data,
                ]
            ];
        }

        $dataString = json_encode($dataString);
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/elevorce-uk/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
        return true;

    }

    public function sendPushTopicNotification($topic, $data = [])
    {
        $SERVER_API_KEY = self::getGoogleAccessToken();
        $fcmUrl = 'https://fcm.googleapis.com/v1/projects/elevorce-uk/messages:send';
        $headers = [
            'Authorization: Bearer ' . $SERVER_API_KEY, // Use the method to get the access token
            'Content-Type: application/json',
        ];

        if ($data['device_type'] == 'android') {
            $notification = [
                "message" => [
                    "topic" => $topic,
                    // "token" => $device_token,
                    "android" => [
                        "data" => $data,
                        "priority" => "high"
                    ]
                ]
            ];
        } else if ($data['device_type'] == "iOS") {
            $notification = [
                "message" => [
                    "topic" => $topic,
                    // "token" => $device_token,
                    "apns" => [
                        "headers" => [
                            "apns-priority" => "5",
                        ],
                        "payload" => [
                            "aps" => [
                                // "title" => $data['title'],
                                // "body" => $data['body'],
                                "alert" => [
                                    "title" => $data['title'],
                                    "body" => $data['body'],
                                ],
                                "content-available" => 1,
                                "mutable-content" => 1,
                                // "category" => "customNotificationView",
                                // "sound" => $data['sound'],
                            ],
                            "data" => $data,
                        ]
                    ]
                ]
            ];
        } else {
            $notification = [
                "message" => [
                    "topic" => $topic,
                    // "token" => $device_token,
                    "data" => $data,
                ]
            ];
        }


        // dd($notification);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
        $result = curl_exec($ch);
        curl_close($ch);
        return true;
    }

}
