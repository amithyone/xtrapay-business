<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $business = $user->businessProfile;
        
        return view('notifications.index', [
            'telegram_bot_token' => $business ? $business->telegram_bot_token : '',
            'telegram_chat_id' => $business ? $business->telegram_chat_id : '',
        ]);
    }

    public function sendTelegramNotification($chatId, $message, $botToken = null)
    {
        // Use the provided bot token, or fall back to the global env
        $botToken = $botToken ?: env('TELEGRAM_BOT_TOKEN');
        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        return $response->json();
    }

    public function notifySuccessfulTransaction($transaction)
    {
        $user = $transaction->user;
        $business = $user->businessProfile;
        if ($business && $business->telegram_chat_id && $business->telegram_bot_token) {
            $message = "Transaction successful: {$transaction->amount} for {$transaction->description}";
            $this->sendTelegramNotification($business->telegram_chat_id, $message, $business->telegram_bot_token);
        }
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'telegram_bot_token' => 'required|string|min:10',
            'telegram_chat_id' => 'required|string|min:1',
        ]);

        $user = auth()->user();
        $business = $user->businessProfile;
        
        if (!$business) {
            return redirect()->route('notifications.index')->with('error', 'Business profile not found!');
        }
        
        // Log the data being saved
        \Log::info('Saving Telegram settings', [
            'user_id' => $user->id,
            'business_id' => $business->id,
            'telegram_bot_token' => $request->telegram_bot_token,
            'telegram_chat_id' => $request->telegram_chat_id,
            'all_request_data' => $request->all()
        ]);
        
        $updated = $business->update($request->only('telegram_bot_token', 'telegram_chat_id'));
        
        if ($updated) {
            // Log successful update
            \Log::info('Telegram settings saved successfully', [
                'user_id' => $user->id,
                'business_id' => $business->id
            ]);
            return redirect()->route('notifications.index')->with('success', 'Notification settings saved!');
        } else {
            // Log failed update
            \Log::error('Failed to save Telegram settings', [
                'user_id' => $user->id,
                'business_id' => $business->id
            ]);
            return redirect()->route('notifications.index')->with('error', 'Failed to save notification settings!');
        }
    }

    // Add a method to send a test notification for the authenticated user's business
    public function testBusinessTelegram()
    {
        $user = auth()->user();
        $business = $user->businessProfile;
        if (!$business || !$business->telegram_bot_token || !$business->telegram_chat_id) {
            return response()->json(['success' => false, 'message' => 'Telegram bot token or chat ID not set for this business.'], 422);
        }
        $message = "🚀 Test notification from Xtrabusiness (per-business)!";
        return $this->sendTelegramNotification($business->telegram_chat_id, $message, $business->telegram_bot_token);
    }
} 