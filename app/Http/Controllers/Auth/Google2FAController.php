<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FAQRCode\Google2FA;
use App\Models\AuditLogs;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Auth;

class Google2FAController extends Controller
{
    protected $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }
    
    public function register()
    {
        $user = Auth::user();
    
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to register for 2FA.');
        }
    
        if ($user->google2fa_secret) {
            return redirect()->route('google2fa.verify');
        }
    
        // Check if the QR code already exists in the session
        if (!session()->has('qr_code')) {
            // Clear previous session data
            session()->forget(['qr_code', 'secret']);

            // Generate a new secret key
            $secret = $this->google2fa->generateSecretKey();
        
            Log::info("Generating QR code for: " . $user->email);

            // Generate the QR code
            $qrCode = new QrCode(
                data: 'otpauth://totp/RCOA:' . $user->email . '?secret=' . $secret . '&issuer=RCOA',
                encoding: new Encoding('UTF-8'),
                size: 200,
                errorCorrectionLevel: ErrorCorrectionLevel::Low,
                margin: 20,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0, 0),
                backgroundColor: new Color(255, 255, 255, 0)
            );
        
            $writer = new PngWriter();
            $qrCodeImage = $writer->write($qrCode);

            $imageUrl = base64_encode($qrCodeImage->getString());
        
            session(['qr_code' => $imageUrl, 'secret' => $secret]);
        } else {
            $imageUrl = session('qr_code');
            $secret = session('secret');
        }

        // Debugging: Ensure $QR_Image is a string
        if (!is_string($imageUrl)) {
            dd('QR_Image is not a string', $imageUrl);
        }
        
        return view('google2fa.register', compact('imageUrl', 'secret'));
    }
    
    public function store(Request $request)
    {
        $request->validate(['secret' => 'required']);

        $user = Auth::user();
        $user->google2fa_secret = $request->secret;
        $user->save();

        return redirect()->route('google2fa.verify');
    }

    public function verify()
    {
        return view('google2fa.verify');
    }

    public function check(Request $request)
    {
        // Create the validator instance
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'regex:/^\d{6}$/']
        ], [
            'token.required' => 'The token is required.',
            'token.regex' => 'The token must be exactly 6 digits.'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $user = Auth::user();
        $token = trim($request->input('token'));
    
        // Verify token
        if ($this->google2fa->verifyKey($user->google2fa_secret, $request->token)) {
            // Mark 2FA as verified
            $request->session()->put('2fa_verified', true);

            // Redirect to dashboard
            return redirect()->route('dashboard.index');
        }
    
        return back()->with('error', 'Invalid token. Please try again');
    }
}