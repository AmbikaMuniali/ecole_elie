<?php

// Ensure you have the Firebase Admin SDK for PHP installed.
// You can install it via Composer: composer require kreait/firebase-php

// Include the Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

// Replace with your Firebase project's service account JSON file path
$serviceAccountPath = __DIR__ . '/../kasokoo2025-firebase-adminsdk-fbsvc-67279bff57.json';

// Initialize Firebase Admin SDK
$factory = (new Factory)
    ->withServiceAccount($serviceAccountPath);

$messaging = $factory->createMessaging();

// Define the message details
$title = 'Your Notification Title';
$body = 'Your Notification Body';
$topic = 'news'; // Or a specific device token, or a condition
$condition = 'ev2xITcvTbKNTpnMwjTsVf:APA91bG0oQehX5tn6wgFGKzGk-jG_tVvHBJ5EFgpWXFxTAdVgoq3JOqAh-GQFXWQGe2qniLUJDHcxU2t9bz_woHVLiRUoMEMuVbXPOT2wwrINO-3JU8hKmU';

// Create the notification payload
$notification = Notification::create($title, $body);

// Create the message
$message = CloudMessage::withTarget('token', $condition) // Or 'token' or 'condition'
    ->withNotification($notification)
    ->withData([
        'key1' => 'value1',
        'key2' => 'value2',
        // Add any custom data you want to send
    ]);

// Send the message
try {
    $result = $messaging->send($message);
    echo 'Message sent successfully. Message ID: ' . json_encode($result) . PHP_EOL;
} catch (Exception $e) {
    echo 'Error sending message: ' . $e->getMessage() . PHP_EOL;
}

?>