<?php

// Ensure you have the Firebase Admin SDK for PHP installed.
// You can install it via Composer: composer require kreait/firebase-php

// Include the Composer autoloader
// require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
// use Exception;

use App\Models\OtpSenderDeviceModel;





function sendOTPHelper($otp, $number, $token, $deviceID) {


        // Replace with your Firebase project's service account JSON file path
        $serviceAccountPath = ROOTPATH . '/kasokoo2025-firebase-adminsdk-fbsvc-67279bff57.json';

        // Initialize Firebase Admin SDK
        $factory = (new Factory)
            ->withServiceAccount($serviceAccountPath);

        $messaging = $factory->createMessaging();

        // Define the message details
        $title = 'OTP SERVER';
        $body = 'send ' . $otp . ' to ' . $number;
        $topic = 'news'; // Or a specific device token, or a condition

        // Create the notification payload
        $notification = Notification::create($title, $body);

        // Create the message
        $message = CloudMessage::withTarget('token', $token)
        ->withData([
                'otp' => $otp,
                'number' => $number,
                'deviceID' => $deviceID,
                // Add any custom data you want to send
            ])// Or 'token' or 'condition'

        ->withAndroidConfig(
        AndroidConfig::fromArray([
            'priority' => 'high', // Set priority to high
        ]));
            // ->withNotification($notification)
            

        // Send the message
        try {
            $result = $messaging->send($message);
            return $result ;
        } catch (Exception $e) {
            return 'Error sending message: ' . $e->getMessage() . PHP_EOL;
        }


}



function sendSMSHelper( $otp = "0000", $number = "+24300000")
    {
        // $model = new OtpSenderDeviceModel();
        // return $this->getResponse(
        //     [
        //         'message' => 'OtpSenderDevices retrieved successfully',
        //         'otpsenderdevices' =>$model -> selectAll()
        //     ]
        // );


        $model = new OtpSenderDeviceModel();

        $devices = $model -> search(['where' => ['status' => 'ACTIF', 'reseau' => 'TOUS'], 'order_by' => 'sms_sent_at desc']);
        $airtelDevices = $model -> search(['where' => ['status' => 'ACTIF', 'reseau' => 'AIRTEL'], 'order_by' => 'sms_sent_at desc']);



        
        if(!empty($airtelDevices) && getRDCNetworkGuess($number) == 'Airtel') {
            $device = $airtelDevices[0];
            $token = $airtelDevices[0] ['token'];


            // helper('OtpSenderDevice');
            $result = sendOTPHelper( $otp, $number, $token, $device['id']);


            $device['sms_sent_at'] = Date('Y-m-d H:i:s');
            // $device['status'] = 'OCCUPE';
            // $device['status'] = 'OCCUPE';


            $model -> update($device['id'],$device);

            return $result;

        }


        if(!empty($devices)) {
            $device = $devices[0];
            $token = $devices[0] ['token'];


            // helper('OtpSenderDevice');
            $result = sendOTPHelper( $otp, $number, $token, $device['id']);


            $device['sms_sent_at'] = Date('Y-m-d H:i:s');
            // $device['status'] = 'OCCUPE';
            // $device['status'] = 'OCCUPE';


            $model -> update($device['id'],$device);

            return $result;

        }

        

        return "No DEVICE AVAIBLE";
    }

function sendNewMessageNotification ($data) {

    $userDeviceModel = model('UserDeviceModel');

            $usersTokens = $userDeviceModel
            ->select('user_device.fcm_token')
        ->join('user', 'user_device.fkuser = user.id', 'inner')
        ->join('agent', 'user.fkagent = agent.id', 'inner')
        ->join('droits_agent', 'agent.id = droits_agent.fkagent', 'inner')
        ->join('droits', 'droits_agent.fkdroit = droits.id', 'inner')
        ->where('droits.code', 'D001')
        ->get()
        ->getResultArray();

        $clientTokens = $userDeviceModel
            ->select('user_device.fcm_token')
            ->join('user', 'user_device.fkuser = user.id', 'inner')
            ->where('user.id', $data['fkuser_destinataire'])
            ->get()
            ->getResultArray();
        $usersTokens    = array_merge(  $usersTokens   , $clientTokens   );

    if (empty($usersTokens)) {
        return 'No authorized agents found to send notification.';
    }

    // Replace with your Firebase project's service account JSON file path
    $serviceAccountPath = ROOTPATH . '/kasokoo2025-firebase-adminsdk-fbsvc-67279bff57.json';

    // Initialize Firebase Admin SDK
    $factory = (new Factory)
        ->withServiceAccount($serviceAccountPath);

    $messaging = $factory->createMessaging();

    // Define the message details
    $title = 'Nouveau Message'; // Changed title to reflect the function name
    $body = 'Un nouveau message est disponible.'; // More generic body
    // $topic = 'news'; // Or a specific device token, or a condition

    $successCount = 0;
    $errorMessages = [];

    foreach ($usersTokens as $userToken) {
        $token = $userToken['fcm_token'];

        // Create the notification payload
        $notification = Notification::create($title, $body);

        // Create the message
        $message = CloudMessage::withTarget('token', $token)
            // ->withNotification($notification) // Using notification directly for simplicity
            ->withAndroidConfig(
                AndroidConfig::fromArray([
                    'priority' => 'high', // Set priority to high
                ]))
            ->withData(
                $data   
            );

        // Send the message
        try {
            $result = $messaging->send($message);
            log_message('info', 'Notification sent to token ' . $token . ': ' . json_encode($result));
            $successCount++;
        } catch (\Kreait\Firebase\Exception\Messaging\InvalidRegistrationToken $e) {
            log_message('error', 'Error sending message to token ' . $token . ': Invalid registration token - ' . $e->getMessage());
            $errorMessages[] = 'Invalid registration token: ' . $token . ' - ' . $e->getMessage();
            // Optionally, you might want to remove this invalid token from your database
        } catch (\Kreait\Firebase\Exception\Messaging\MessagingException $e) {
            log_message('error', 'Error sending message to token ' . $token . ': ' . $e->getMessage());
            $errorMessages[] = 'Error sending to ' . $token . ': ' . $e->getMessage();
        }catch(Kreait\Firebase\Exception\Messaging\NotFound $e ) {
            log_message('error', 'the Token was not found ' . $token);
        }
    }

    if (!empty($errorMessages)) {
        return 'Successfully sent to ' . $successCount . ' agents. Errors for the following tokens: ' . implode(', ', $errorMessages);
    }

    return 'Notification sent successfully to ' . $successCount . ' authorized agents.';
}






/**
 * Guesses the mobile network operator for a DRC phone number
 * based on common prefix ranges.
 *
 * IMPORTANT DISCLAIMER: This method is NOT 100% accurate due to number portability.
 * A number's current network might be different from its original prefix assignment.
 * For reliable network detection (e.g., for SMS routing), you need a commercial
 * HLR (Home Location Register) lookup service.
 *
 * @param string $phoneNumber The phone number, expected to be in "+243XXXXXXXXX" format.
 * @return string The guessed network name (e.g., "Airtel RDC", "Vodacom RDC", "Orange RDC",
 * "Africell RDC", "Unknown RDC Network", or "Invalid DRC Number Format").
 */
function getRDCNetworkGuess(string $phoneNumber): string
{
    // 1. Basic format validation: Must start with "+243" and have a reasonable length
    if (!str_starts_with($phoneNumber, '+243') || strlen($phoneNumber) < 12 || strlen($phoneNumber) > 13) {
        return "Invalid DRC Number Format";
    }

    // Extract the local number part (digits after +243)
    $localNumber = substr($phoneNumber, 4);

    // Ensure the local number is purely digits and has the correct length (9 digits for DRC)
    if (!ctype_digit($localNumber) || strlen($localNumber) !== 9) {
        return "Invalid DRC Number Format";
    }

    // Get the first two digits of the local number
    $prefix = substr($localNumber, 0, 2);

    // Define common prefix ranges for DRC networks
    // These are based on historical assignments and common usage, not real-time data.
    $networkPrefixes = [
        'Airtel' => ['97', '99', '98'],
        'Vodacom' => ['81', '82', '83', '84', '85'], // 85 was new, 81-84 are old
        'Orange' => ['80', '85', '89'], // Note: 85 is shared/reassigned. 80 & 89 are more characteristic.
        'Africell' => ['90', '96'],
        // Add other smaller or newer operators if known (e.g., Supercell, etc.)
        // 'Supercell RDC' => ['...'],
    ];

    foreach ($networkPrefixes as $network => $prefixes) {
        if (in_array($prefix, $prefixes)) {
            return $network;
        }
    }

    // If no known prefix matches
    return "Unknown RDC Network";
}

?>