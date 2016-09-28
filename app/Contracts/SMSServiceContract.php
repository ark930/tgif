<?php

namespace App\Contracts;


interface SMSServiceContract
{
    public function SendSMS($tel, $message);

    public function SendSMSByTemplate($tel, $temp_id, $code);
}