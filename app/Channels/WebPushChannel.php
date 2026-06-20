<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;

class WebPushChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWebPush')) {
            return;
        }

        $payload = $notification->toWebPush($notifiable);

        // Always log push payloads locally for easy testing and debugging
        Log::info("Web Push Triggered for User #{$notifiable->id} ({$notifiable->name}): " . json_encode($payload, JSON_PRETTY_PRINT));

        // Attempt real Web Push delivery if the minishlink/web-push package is installed
        if (class_exists(\Minishlink\WebPush\WebPush::class) && class_exists(\Minishlink\WebPush\Subscription::class)) {
            $subscriptions = PushSubscription::where('user_id', $notifiable->id)->get();

            if ($subscriptions->isEmpty()) {
                Log::info("No web push subscriptions found for User #{$notifiable->id}.");
                return;
            }

            // Get VAPID keys from configuration
            $publicKey = config('webpush.vapid.public_key') ?: env('VAPID_PUBLIC_KEY');
            $privateKey = config('webpush.vapid.private_key') ?: env('VAPID_PRIVATE_KEY');
            $subject = config('webpush.vapid.subject') ?: env('VAPID_SUBJECT', 'mailto:admin@elearning.test');

            if (!$publicKey || !$privateKey) {
                Log::warning("VAPID keys are not configured. Real push notifications cannot be sent.");
                return;
            }

            try {
                $auth = [
                    'VAPID' => [
                        'subject' => $subject,
                        'publicKey' => $publicKey,
                        'privateKey' => $privateKey,
                    ],
                ];

                $webPush = new \Minishlink\WebPush\WebPush($auth);

                foreach ($subscriptions as $sub) {
                    $webPush->queueNotification(
                        \Minishlink\WebPush\Subscription::create([
                            'endpoint' => $sub->endpoint,
                            'publicKey' => $sub->public_key,
                            'authToken' => $sub->auth_token,
                            'contentEncoding' => $sub->content_encoding ?: 'aesgcm',
                        ]),
                        json_encode($payload)
                    );
                }

                foreach ($webPush->flush() as $report) {
                    $endpoint = $report->getEndpoint();
                    if ($report->isSuccess()) {
                        Log::info("Web Push sent successfully to endpoint: {$endpoint}");
                    } else {
                        Log::warning("Web Push failed for endpoint {$endpoint}: {$report->getReason()}");
                        // Clean up invalid subscriptions if expired (410 Gone or 404 Not Found)
                        if ($report->isSubscriptionExpired()) {
                            PushSubscription::where('endpoint', $endpoint)->delete();
                            Log::info("Deleted expired web push subscription: {$endpoint}");
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to send Web Push via minishlink/web-push: " . $e->getMessage());
            }
        }
    }
}
