<?php

namespace App\Http\Controllers;

use App\Models\WhatsappEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappEnquiryController extends Controller
{
    // WhatsApp number with country code (91 + 9184009601)
    private const WHATSAPP_NUMBER = '919184009601';

    // Paste the Apps Script URL for the WhatsApp sheet here
    private const GOOGLE_SHEET_URL = '';

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wa_number'  => 'required|digits:10',
            'wa_message' => 'required|string|max:1000',
        ], [
            'wa_number.digits' => 'Mobile number must be 10 digits.',
        ]);

        // 1. Save to the database
        try {
            $enquiry = WhatsappEnquiry::create([
                'mobile'  => $validated['wa_number'],
                'message' => $validated['wa_message'],
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp enquiry save failed: ' . $e->getMessage());
            return back()->withInput()->withErrors([
                'wa_message' => 'Could not submit. Please try again.',
            ]);
        }

        // 2. Send to Google Sheet
        $this->sendToSheet($enquiry);

        // 3. Open WhatsApp with the message pre-filled
        $text = $enquiry->message . "\n\nMy number: " . $enquiry->mobile;

        return redirect()->away(
            'https://wa.me/' . self::WHATSAPP_NUMBER . '?text=' . rawurlencode($text)
        );
    }

    private function sendToSheet(WhatsappEnquiry $enquiry): void
    {
        if (empty(self::GOOGLE_SHEET_URL)) {
            Log::warning('WhatsApp enquiry saved, but GOOGLE_SHEET_URL is not set.');
            return;
        }

        try {
            $response = Http::timeout(20)->post(self::GOOGLE_SHEET_URL, [
                'submitted_at' => $enquiry->created_at->format('Y-m-d H:i:s'),
                'mobile'       => $enquiry->mobile,
                'message'      => $enquiry->message,
            ]);

            if (!$response->successful()) {
                Log::error('WhatsApp sheet error: ' . $response->status() . ' ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp sheet exception: ' . $e->getMessage());
        }
    }
}