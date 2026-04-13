<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    public function image(): Response
    {
        $phrase  = (new PhraseBuilder())->build(4);
        $builder = new CaptchaBuilder($phrase);
        $builder->build(150, 48);

        Session::put('captcha', strtolower($builder->getPhrase()));

        return response($builder->get(), 200, [
            'Content-Type'  => 'image/jpeg',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma'        => 'no-cache',
        ]);
    }
}
