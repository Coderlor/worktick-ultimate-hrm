<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LocalController extends Controller
{
	public function languageSwitch($locale)
	{
		setcookie('language', $locale, time() + (86400 * 365), "/");
        App::setLocale($locale);
		return back();
	}

}
