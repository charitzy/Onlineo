<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CsrfTokenController extends Controller
{
    public function generateToken(): JsonResponse
    {
        return response()->json(['csrfToken' => csrf_token()]);
    }
}
