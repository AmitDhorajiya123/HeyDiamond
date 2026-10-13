<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Legacy placeholder / proxy to AuthApiController
     */
    public function adminLogin(Request $request)
    {
        return app(AuthApiController::class)->login(
            app(\App\Http\Requests\Api\LoginRequest::class)
        );
    }
}
