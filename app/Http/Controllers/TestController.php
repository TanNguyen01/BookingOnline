<?php

namespace App\Http\Controllers;

use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    use APIResponse;

    public function test(Request $request)
    {
        $data = ['1', '2', '3'];
        $entry = ['first name', 'last name', 'email'];

        return $this->responseSuccess('thêm thành công',
            [
                'data' => $data,
                'entry' => $entry,
            ]);
    }

    public function testMail(){

        $name = ' Pham Manh';
        Mail::send('emails.test',compact(('name'),function($email){
            $email->to('manhpkph30134@gmail.com','aaaaaaa');
        }));
    }
}
