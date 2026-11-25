<?php

namespace App\Http\Controllers;

use App\Models\Certificate;

class CertificateVerifyController extends Controller
{
    public function show(string $code)
    {
        $certificate = Certificate::with(['user', 'course'])->where('code', $code)->first();
        return view('certificates.verify', compact('certificate', 'code'));
    }
}
