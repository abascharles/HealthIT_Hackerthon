<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Controllers\BaseController;
use Exception;
use App\Api\V1\Requests\ApiRequest;
use App\Email;
use App\Attachment;

class RandomController extends BaseController
{
    public function protected_route()
    {
        return response()->json([
                    'message' => 'Access to protected resources granted! You are seeing this text as you provided the token correctly.'
                ]);
    }  

    public function refresh_route()
    {
        return response()->json([
                        'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                    ]);
    }  

    public function hello()
    {
        return response()->json([
                'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
            ]);
    } 

    public function hello_nascop()
    {
        try {
            \App\Synch::test_nascop();
        } catch (Exception $e) {
            return response()->json([
                    'message' => 'NASCOP cannot be reached.'
                ], 400);
        }
        return response()->json([
                'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
            ], 200);
    }

    public function email(ApiRequest $request)
    {
        $email_id = $request->input('email');
        $email = Email::find($email_id);
        $attachments = $email->attachment->count();

        $filename = storage_path('app/emails') . '/' . $email->id . '.txt';        
        $str = file_get_contents($filename);

        return response()->json([
                'email_contents' => $str,
                'attachments' => $attachments,
            ], 200);
    }

    public function attachment(ApiRequest $request)
    {
        $email_id = $request->input('email');
        $email = Email::find($email_id);
        $attachment = $email->attachment()->offset($request->input('attachment'))->first();

        // return response()->json([
        //         'email_contents' => $str,
        //         'attachment' => $attachments,
        //     ], 200);


        if(!$attachment) return null;

        return response()->download($attachment->path);
    }

}
