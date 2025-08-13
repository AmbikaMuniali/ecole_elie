<?php

function parseOrange( $message) {
    // Chaîne d'entrée (exemple avec USD)
    // $message = "Vous avez recu 50.00 USD de AMBIKA 0858298122. Nouveau solde: 150.00 USD. Ref: PP250327.2210.D41206";

    // Initialisation des variables pour stocker les données extraites
    $montantRecu = 0;
    $deviseRecu = null;
    $expediteur = null;
    $telephone = null;
    $solde = null;
    $deviseSolde = null;
    $reference = null;

    // Extraction du montant reçu et de la devise
    if (preg_match('/recu\s+([\d.]+)\s+(CDF|USD)/i', $message, $matches)) {
        $montantRecu = $matches[1];
        $deviseRecu = $matches[2];
    }

    // Extraction du nom de l'expéditeur et du numéro de téléphone
    if (preg_match('/de\s+([A-Z]+)\s+(\d+)/i', $message, $matches)) {
        $expediteur = $matches[1];
        $telephone = $matches[2];
    }

    // Extraction du nouveau solde et de la devise
    if (preg_match('/Nouveau solde:\s+([\d.]+)\s+(CDF|USD)/i', $message, $matches)) {
        $solde = $matches[1];
        $deviseSolde = $matches[2];
    }

    // Extraction de la référence
    if (preg_match('/Ref:\s+([A-Z0-9.]+)/i', $message, $matches)) {
        $reference = $matches[1];
    }


    // // Affichage des données extraites
    // echo "Montant reçu : $montantRecu $deviseRecu\n";
    // echo "Expéditeur : $expediteur\n";
    // echo "Téléphone : $telephone\n";
    // echo "Nouveau solde : $solde $deviseSolde\n";
    // echo "Référence : $reference\n";

    return [
        "montant" => $montantRecu,
        "numero" => $telephone,
        "transaction_id" => $reference,
        "devise" => $deviseRecu,
        "solde" => $solde,
        "transaction_token" => $message
    ];


}

function parseAirtel($message){


    // Chaîne d'entrée (exemple avec un autre opérateur)
    // $message = "Trans.ID:PP250327.2308.D81612. Vous avez recu 100.0000 CDF de ambika arsene 975048355. Nouveau solde: 432.4000 CDF.";

    // Initialisation des variables pour stocker les données extraites
    $montantRecu = 0;
    $deviseRecu = 'CDF';
    $expediteur = null;
    $telephone = null;
    $solde = 0;
    $deviseSolde = 'CDF';
    $reference = null;

    // Extraction de la référence (Trans.ID)
    if (preg_match('/Trans\.ID:([A-Z0-9.]+)/i', $message, $matches)) {
        $reference = $matches[1];
    }
    // Extraction de la référence (Trans.ID)
    if (preg_match('/TID: ([A-Z0-9.]+)/i', $message, $matches)) {
        $reference = $matches[1];
    }

    // Extraction du montant reçu et de la devise
    if (preg_match('/recu\s+([\d.]+)\s+(CDF|USD)/i', $message, $matches)) {
        $montantRecu = $matches[1];
        $deviseRecu = $matches[2];
    }

    // Extraction du nom de l'expéditeur et du numéro de téléphone
    if (preg_match('/de\s+([a-zA-Z\s]+)\s+(\d+)/i', $message, $matches)) {
        $expediteur = trim($matches[1]); // Supprime les espaces inutiles
        $telephone = $matches[2];
    }

    // Extraction du nouveau solde et de la devise
    if (preg_match('/Nouveau solde:\s+([\d.]+)\s+(CDF|USD)/i', $message, $matches)) {
        $solde = $matches[1];
        $deviseSolde = $matches[2];
    }

    // // Affichage des données extraites
    // echo "Référence : $reference\n";
    // echo "Montant reçu : $montantRecu $deviseRecu\n";
    // echo "Expéditeur : $expediteur\n";
    // echo "Téléphone : $telephone\n";
    // echo "Nouveau solde : $solde $deviseSolde\n";
    return [
            "montant" => $montantRecu,
            "numero" => $telephone,
            "transaction_id" => $reference,
            "devise" => $deviseRecu,
            "solde" => $solde,
        ];

}
function parseVodacom($message) {
    return [
            "montant" => $montantRecu,
            "numero" => $telephone,
            "transaction_id" => $reference,
            "devise" => $deviseRecu,
            "solde" => $solde,
        ];
}

function parseSMSNotification($message, $reseau) {

    switch ($reseau) {
        case 'AIRTEL_MONEY':
            $result = parseAirtel($message);
            break;
        case 'ORANGE_MONEY':
            $result = parseOrange($message);
            break;
        
        case 'MPESA':
            $result = parseVodacom($message);
            break;
        default:
            $result = [
                "montant" => 0,
                "numero" => null,
                "transaction_id" => null,
                "devise" => null,
                "solde" => null,
            ];
            break;
    }

    $result['data_json'] = json_encode($result);
    $result['status'] = 'CREATED';
    return $result;

}

