<?php

namespace Workbench\App\Http\Controllers\Test;

use Illuminate\Http\Request;

class TestShowController
{
    public function __invoke(Request $request)
    {
        return view('app');
    }
}
