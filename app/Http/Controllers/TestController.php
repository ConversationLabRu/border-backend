<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TestController extends Controller
{
    public function testMethod()
    {
        return json_encode("{\"message\": \"dsadad\", \"id\": 1}");
    }
}
