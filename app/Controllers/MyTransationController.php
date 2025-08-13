<?php
namespace App\Controllers;
use App\Models\AgentModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;


/**
 * Save Commands
 * Save Operations on acounts
 * Chechk the account
 * 
 * 
 * 
 */
class MyTransationController extends BaseController {



	/**
     * Handles the transfer of funds from an agent to a client.
     */
   
	


    public function transfertAgentToClient()
    {
        $data = $this->getRequestInput($this->request);
        $errors = [];

        try {
            // 1. Retrieve and validate request data
            $idAgent = $data['idAgent'] ?? null;
            $idClient = $data['idClient'] ?? null;
            $montant = $data['montant'] ?? null;
            $devise = $data['devise'] ?? null;
            $idUser = $data['idUser'] ?? null;

            if (empty($idAgent)) throw new Exception("Missing Agent ID", 1);
            if (empty($idClient)) throw new Exception("Missing Client ID", 1);
            if (empty($montant) || $montant <= 0) throw new Exception("Invalid amount", 1);
            if (empty($devise) || !in_array($devise, ['USD', 'CDF'])) throw new Exception("Invalid currency", 1);
            // if (empty($idUser)) throw new Exception("Missing User ID", 1);

            // 2. Fetch agent, client, and system accounts
            $modelAgent = model('AgentModel');
            $agent = $modelAgent->findAgentById($idAgent);
            if (empty($agent)) throw new Exception("Agent not found", 1);

            $modelClient = model('ClientModel');
            $client = $modelClient->findClientById($idClient);
            if (empty($client)) throw new Exception("Client not found", 1);

            $modelCompte = model('CompteModel');
            $modelParametre = model('ParametreModel');
            $parametre = $modelParametre->findParametreById(1);
            if (empty($parametre) || empty($parametre->taux_change)) throw new Exception("System parameters not found", 1);
            $tauxChange = $parametre->taux_change - 0;

            // Agent's accounts
            $compteUSDAgent = $modelCompte->search(['where' => ['fkagent' => $idAgent, 'devise' => 'USD']]);
            $compteCDFAgent = $modelCompte->search(['where' => ['fkagent' => $idAgent, 'devise' => 'CDF']]);
            

            if (empty($compteUSDAgent) || empty($compteCDFAgent)) {
            	// create an acount for the agent
            	$cdf = $modelCompte -> insert(['fkagent' => $agent -> id, 'intitule' => 'COMPTE CDF Agent : ' . $agent -> name_complet , 'type_compte' => 'COMPTE_AGENT','devise' => 'CDF']);
            	$usd = $modelCompte -> insert(['fkagent' => $agent -> id, 'intitule' => 'COMPTE CDF Agent : ' . $agent -> name_complet , 'type_compte' => 'COMPTE_AGENT','devise' => 'USD']);
            	
            }



            // Agent's accounts
            $compteUSDAgent = $modelCompte->search(['where' => ['fkagent' => $agent -> id, 'devise' => 'USD']]);
            $compteCDFAgent = $modelCompte->search(['where' => ['fkagent' => $agent -> id, 'devise' => 'CDF']]);


            // return	$this ->getResponse	(['agentusd' => $agent -> id,'cdf' => $compteCDFAgent,	]);

            $idCpteUSDAgent = $compteUSDAgent[0]['id'];
            $idCpteCDFAgent = $compteCDFAgent[0]['id'];
            $soldeUSDAgent = $agent-> solde_usd - 0;
            $soldeCDFAgent = $agent -> solde_cdf - 0;

            // Client's accounts
            $compteUSDClient = $modelCompte->search(['where' => ['fkclient' => $idClient, 'devise' => 'USD']]);
            $compteCDFClient = $modelCompte->search(['where' => ['fkclient' => $idClient, 'devise' => 'CDF']]);
            if (empty($compteUSDClient) || empty($compteCDFClient)) throw new Exception("Client accounts not found", 1);

            $idCpteUSDClient = $compteUSDClient[0]['id'];
            $idCpteCDFClient = $compteCDFClient[0]['id'];
            $soldeCDFClient = $client->solde_cdf - 0;
            $soldeUSDClient = $client->solde_usd - 0;




            // 3. Check agent's balance NOT NOW BUT SOON
            $totalAgentSoldeEnDeviseTransfert = 0;
            if ($devise == 'CDF') {
                $totalAgentSoldeEnDeviseTransfert = $soldeCDFAgent + ($soldeUSDAgent * $tauxChange);
            } else { // USD
                $totalAgentSoldeEnDeviseTransfert = $soldeUSDAgent + ($soldeCDFAgent / $tauxChange);
            }

            if ($totalAgentSoldeEnDeviseTransfert < $montant) {
                // throw new Exception("Insufficient balance for the agent", 1);
            }

            // 4. Calculate new balances and amounts for operation
            $newSoldeUSDAgent = $devise == 'USD' ? $soldeUSDAgent - $montant : $soldeUSDAgent ;
            $newSoldeCDFAgent = $devise == 'CDF' ? $soldeCDFAgent - $montant : $soldeCDFAgent;

            $newSoldeUSDClient = $devise == 'USD' ? $soldeUSDClient + $montant : $soldeUSDClient ;
            $newSoldeCDFClient = $devise == 'CDF' ? $soldeCDFClient + $montant : $soldeCDFClient;


            // Initialisation of the variables that were missing
            $montantDebiteUSD = 0;
            $montantDebiteCDF = 0;
            $montantCrediteUSD = 0;
            $montantCrediteCDF = 0;

            if ($devise == 'USD') {
                $montantDebiteUSD = $montant;
                $montantCrediteUSD = $montant;
            } else {
                $montantDebiteCDF = $montant;
                $montantCrediteCDF = $montant;
            }


            // 5. Update balances in the database
            $modelAgent->update($idAgent, ['solde_usd' => $newSoldeUSDAgent]);            
            $modelAgent->update($idAgent, ['solde_cdf' => $newSoldeCDFAgent]);

            $modelClient->update($idClient, ['solde_usd' => $newSoldeUSDClient]);
            $modelClient->update($idClient, ['solde_cdf' => $newSoldeCDFClient]);


            // 6. Create operation
            $modelOperation = model('OperationModel');

            $user = model('UserModel') -> search (['where' => ['fkagent' => $idAgent]]);


            $idUser = $user[0]['id'];


            $idOperation = $modelOperation->insert([
                'type_operation' => 'DEPOT_CLIENT',
                'fkuser_create' => $idUser,
                'libelle' => 'Transfert de fonds Agent à Client'
            ]);
            if (empty($idOperation)) throw new Exception("Failed to create operation", 1);


            // 7. Create operation lines
            $modelLigneOperation = model('LigneOperationModel');

            // Agent Debit
            if ($montantDebiteCDF > 0) {
                $modelLigneOperation->insert([
                    'fkcompte' => $idCpteCDFAgent,
                    'fkoperation' => $idOperation,
                    'montant' => $montantDebiteCDF,
                    'operation' => 'CREDIT' // Agent's account is credited (money is taken out)
                ]);
            }
            if ($montantDebiteUSD > 0) {
                $modelLigneOperation->insert([
                    'fkcompte' => $idCpteUSDAgent,
                    'fkoperation' => $idOperation,
                    'montant' => $montantDebiteUSD,
                    'operation' => 'CREDIT'
                ]);
            }

            // Client Credit
            if ($montantCrediteCDF > 0) {
                $modelLigneOperation->insert([
                    'fkcompte' => $idCpteCDFClient,
                    'fkoperation' => $idOperation,
                    'montant' => $montantCrediteCDF,
                    'operation' => 'DEBIT' // Client's account is debited (money is added)
                ]);
            }
            if ($montantCrediteUSD > 0) {
                $modelLigneOperation->insert([
                    'fkcompte' => $idCpteUSDClient,
                    'fkoperation' => $idOperation,
                    'montant' => $montantCrediteUSD,
                    'operation' => 'DEBIT'
                ]);
            }

            return $this->getResponse(['message' => 'Transfer successful', 'idOperation' => $idOperation]);

        } catch (Exception $e) {
            return $this->getResponse([
                'error' => $e->getMessage(),
                'message' => 'An error occurred during the transfer.'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }
    }



	private function calculerTotal($panier, $devise) {
		$modelProduit = model('ProduitModel');


		foreach ( $pannier as $item) {
			$produit = $modelProduit -> findProduitById($item['idProduit']);
		}

		return 10;


	}


	function saveCashPower(){
		$data = $this -> getRequestInput($this -> request);
		$errors = [];

		try {
			$idClient = ! isset($data['idClient'])? '' : $data['idClient'];
			$idUser = ! isset($data['idUser'])? '' : $data['idUser'];
			$montant = ! isset($data['montant'])? '' : $data['montant'];
			$devise = ! isset($data['devise'])? '' : $data['devise'];

			$errors [] = "CLIENT $idClient USER $idUser montant $montant Devise $devise:";

			if(empty($idClient)) throw new Exception("Missing Client", 1);
			if (empty($idUser)) throw new Exception("Missing User", 1);
			if (empty($devise)) throw new Exception("Missing devise", 1);
			if (empty($montant)) throw new Exception("Missing montant", 1);


			$errors [] = "variables";


			// trouver les caisses a utiliser
			$idCaisseVente = CAISSE_VENTE_PAR_DEFAUT;

			$idCaisseUSD = '';
			$idCaisseCDF = '';

			$modelCompte = model('CompteModel');

			$caisseUSD = $modelCompte -> search(['where' => [
				'fkcaisse' => $idCaisseVente,
				'devise' => 'USD']
			]);


			// $errors['COMPTE CAISSE USD'] = $caisseUSD;

			if(!empty($caisseUSD)) {
				$idCaisseUSD = $caisseUSD[0]['id'];
				$caisseUSD = $caisseUSD[0];	
			}


			$caisseCDF = $modelCompte -> search(['where' => [
				'fkcaisse' => $idCaisseVente,
				'devise' => 'CDF']
			]);
			if(!empty($caisseCDF)) {
				$idCaisseCDF = $caisseCDF[0]['id'];
				$caisseCDF = $caisseCDF[0];	
			}

			

			if(empty($idCaisseCDF) || empty($idCaisseUSD)) throw new Exception("Comptes de caisse non trouvés", 1);



			//TROUVER LE CLIENT ET LE Solde du client
			$modelClient = model('ClientModel');
			$client = $modelClient -> findClientById($idClient);
 
			if (empty($client)) throw new Exception("Client non trouvé", 1);


			/*
			 * ENREGISTRER LES OPERATIONS
			 * 1. CHERCHER LES COMPTES DU CLIENT
			 * 2. CHERCHER D'ABORD LE SOLDE
			 * 3. SOLDER EN PRIORITE LES COMPTES DE LA MEME DEVISE QUE LA COMMANDE
			 * 4. INDIQUER EN LA LIASON ENTRE LA COMMANDE ET L'OPERATION
			 *
			 *
			*/



			
			// chercher les comptes du client
			$modelCompte = model('CompteModel');
			$compteUSDClient = $modelCompte -> search(['where' => [
				'fkclient' => $idClient,
				'devise' => 'USD']
			]);

			$idCpteUSD = '';
			$idCpteCDF = '';

			if(!empty($compteUSDClient)) {
				$idCpteUSD = $compteUSDClient[0]['id'];
				$compteUSDClient = $compteUSDClient[0];	
			}

			$compteCDFClient = $modelCompte -> search(['where' => [
				'fkclient' => $idClient,
				'devise' => 'CDF']
			]);


			if(!empty($compteCDFClient)) {
				$idCpteCDF = $compteCDFClient[0]['id'];
				$compteCDFClient = $compteCDFClient[0];
			}

			if(empty($idCpteUSD) || empty($idCpteCDF)) throw new Exception("Comptes du client non trouvés", 1);

			


			//Preparer les lignes commande
			$parametreModel = model('ParametreModel');
			$parametre = $parametreModel -> findParametreById(1);

			if (empty($parametre)) throw new Exception("Paramètres système non trouvés", 1);


			if (!isset($parametre -> taux_change)) throw new Exception("Missing taux_change", 1);
			

			$tauxChange = $parametre -> taux_change - 0 ;
			if ($tauxChange == 0) throw new Exception("Le taux de change ne peut pas être zéro", 1);


			if ($devise != 'USD' && $devise != 'CDF')  throw new Exception("devise inconnue", 1);

			// Le taux de change est déjà en USD, donc on l'ajuste pour la devise de la transaction si nécessaire
			$tauxUtilise = $parametre->taux_change - 0;
			if ($devise === 'USD') {
				$tauxUtilise = 1;
			} else { // CDF
				$tauxUtilise = 1 / $parametre->taux_change;
			}


			$modelProduit = model('ProduitModel');
			$produit = $modelProduit->findProduitById(ID_PRODUCT_CASH_POWER);

			if (empty($produit)) throw new Exception("Produit Cash Power non trouvé", 1);

			$idProduit = $produit->id;
			$prix = $montant > 0 ? $montant : 0;
			$quantite = 1;
			$total = $quantite * $prix;
			$totalCommande = $total;

			$lignesCommande = [
				"fkproduit" => $idProduit,
				"montant" => $total,
				"quantite" => $quantite,
				"fkcommande" => null,
				"prix" => $prix - 0,
				"tauxChange" => $tauxUtilise,
				"produit" => $produit->designation
			];

			$fraisLivraison = $totalCommande * TAUX_FRAIS_CASH_POWER;
			$total = $totalCommande + $fraisLivraison;
			
			// Enregistrer la Commande
			$modelCommande = model('CommandeModel');
			$commande = [
				'fkclient' => $idClient,
				'total_cmd' => $totalCommande,
				'frais_livraison' => $fraisLivraison,
				'devise' => $devise,
				'libelle' => 'Achat courant cash power'
			];

			$idCommande = $modelCommande -> insert($commande);
			if (empty($idCommande)) throw new Exception("Erreur lors de la création de la commande", 1);

			// Enregistrer les lignes commandes
			$ligneCommandeModel = model('LigneCommandeModel');
			$lignesCommande ['fkcommande'] = $idCommande;
			$ligneCommandeModel -> insert($lignesCommande);

			// SOLDE DU CLIENT
			$soldeCDF = $client->solde_cdf - 0;
			$soldeUSD = $client->solde_usd - 0;

			// Traitement d'une transaction: calcul des montants à débiter
			$newSoldeCDF = $soldeCDF;
			$newSoldeUSD = $soldeUSD;
			$montantCDF = 0;
			$montantUSD = 0;

			if ($devise == 'CDF') {
				$totalEnCDF = $total;
				$soldeTotalEnCDF = $soldeCDF + ($soldeUSD * $parametre->taux_change);

				if ($soldeTotalEnCDF < $totalEnCDF) {
					throw new Exception("Solde insuffisant pour la transaction", 1);
				}

				if ($soldeCDF >= $totalEnCDF) {
					$newSoldeCDF = $soldeCDF - $totalEnCDF;
					$montantCDF = $totalEnCDF;
				} else {
					$resteADebiter = $totalEnCDF - $soldeCDF;
					$newSoldeCDF = 0;
					$montantCDF = $soldeCDF;

					$montantADebiterUSD = $resteADebiter / $parametre->taux_change;
					$newSoldeUSD = $soldeUSD - $montantADebiterUSD;
					$montantUSD = $montantADebiterUSD;
				}
			} else { // devise == 'USD'
				$totalEnUSD = $total;
				$soldeTotalEnUSD = $soldeUSD + ($soldeCDF / $parametre->taux_change);

				if ($soldeTotalEnUSD < $totalEnUSD) {
					throw new Exception("Solde insuffisant pour la transaction", 1);
				}

				if ($soldeUSD >= $totalEnUSD) {
					$newSoldeUSD = $soldeUSD - $totalEnUSD;
					$montantUSD = $totalEnUSD;
				} else {
					$resteADebiter = $totalEnUSD - $soldeUSD;
					$newSoldeUSD = 0;
					$montantUSD = $soldeUSD;

					$montantADebiterCDF = $resteADebiter * $parametre->taux_change;
					$newSoldeCDF = $soldeCDF - $montantADebiterCDF;
					$montantCDF = $montantADebiterCDF;
				}
			}


			// Actualiser les comptes du client
			$client->solde_cdf = $newSoldeCDF;
			$client->solde_usd = $newSoldeUSD;
			$modelClient->update($idClient, (array)$client);

			// CREER L'OPERATION
			$modelOperation = model('OperationModel');
			$idOperation = $modelOperation->insert([
				'type_operation' => 'PAIEMENT_CMD',
				'fkuser_create' => $idUser,
				'libelle' => 'paiement commande'
			]);

			if (empty($idOperation)) throw new Exception("Erreur lors de la création de l'opération", 1);


			// CREER OPERATION_COMMANDE
			$modelCmdOperation = model('OperationCommandeModel');
			$modelCmdOperation->insert([
				'fkcommande' => $idCommande,
				'fkoperation' => $idOperation,
			]);

			// inserer les lignes lignes operations
			$modelLigneOperation = model('LigneOperationModel');

			if ($montantCDF != 0) {
				// Débit du compte client en CDF
				$modelLigneOperation->insert([
					'fkcompte' => $idCpteCDF,
					'fkoperation' => $idOperation,
					'montant' => $montantCDF,
					'operation' => 'CREDIT' // "CREDIT" for the client's account means a debit
				]);
				// Crédit de la caisse en CDF
				$modelLigneOperation->insert([
					'fkcompte' => $idCaisseCDF,
					'fkoperation' => $idOperation,
					'montant' => $montantCDF,
					'operation' => 'DEBIT' // "DEBIT" for the cash box means a credit
				]);
			}

			if ($montantUSD != 0) {
				// Débit du compte client en USD
				$modelLigneOperation->insert([
					'fkcompte' => $idCpteUSD,
					'fkoperation' => $idOperation,
					'montant' => $montantUSD,
					'operation' => 'CREDIT'
				]);
				// Crédit de la caisse en USD
				$modelLigneOperation->insert([
					'fkcompte' => $idCaisseUSD,
					'fkoperation' => $idOperation,
					'montant' => $montantUSD,
					'operation' => 'DEBIT'
				]);
			}

			// Gérer la réponse de succès
			$confirmationMessage = "Cher client, votre achat de Cash Power a été enregistré. Votre transaction est en cours de traitement.";
			$messageModel = model('MessageModel');
			$messageModel->insert([
				"fkuser" => ID_USER_SYSTEM, 
				"corps_message" => $confirmationMessage, 
				"fkuser_destinataire" => $idUser
			]);

			// Vous pouvez également générer une facture pour le cash power si nécessaire
			// $facture = $this->generateMardownBill($idCommande, [$lignesCommande], $total, $fraisLivraison, $devise, '');

			return $this->getResponse(['message' => 'success']);

		} catch (Exception $e) {
			return $this->getResponse([
				'error' => $e->getMessage(),
				'message' => 'Une erreur est survenue lors du traitement de la transaction.'
			], ResponseInterface::HTTP_BAD_REQUEST);
		}
	}


	function saveCommande() {

		$data = $this -> getRequestInput($this -> request);
		$errors = [];

		try {

			$idClient = ! isset($data['idClient'])? '' : $data['idClient'];
			$idUser = ! isset($data['idUser'])? '' : $data['idUser'];
			$pannier = ! isset($data['pannier'])? '' : $data['pannier'];
			$adresse = ! isset($data['adresse'])? '' : $data['adresse'];
			$libelle = ! isset($data['libelle'])? '' : $data['libelle'];
			$devise = ! isset($data['devise'])? 'CDF' : $data['devise'];



			if(empty($idClient)) throw new Exception("Missing Client", 1);
			if (empty($idUser)) throw new Exception("Missing User", 1);
			if (empty($adresse)) throw new Exception("Missing adresse", 1);
			if (empty($pannier)) throw new Exception("Missing pannier", 1);
			
			// trouver les caisses a utiliser
			$idCaisseVente = CAISSE_VENTE_PAR_DEFAUT;

			$idCaisseUSD = '';

			$modelCompte = model('CompteModel');

			$caisseUSD = $modelCompte -> search(['where' => [
				'fkcaisse' => $idCaisseVente,
				'devise' => 'USD']
			]);


			// $errors['COMPTE CAISSE USD'] = $caisseUSD;

			if(!empty($caisseUSD)) {
				$idCaisseUSD = $caisseUSD[0]['id'];
				$caisseUSD = $caisseUSD[0];	
			} 


			$caisseCDF = $modelCompte -> search(['where' => [
				'fkcaisse' => $idCaisseVente,
				'devise' => 'CDF']
			]);
			if(!empty($caisseCDF)) {
				$idCaisseCDF = $caisseCDF[0]['id'];
				$caisseCDF = $caisseCDF[0];	
			} 

			

			if(empty($idCaisseCDF) || empty($idCaisseUSD)) throw new Exception("", 1);



			//TROUVER LE CLIENT ET LE Solde du client 
			$modelClient = model('ClientModel');
			$client = $modelClient -> findClientById($idClient);



			/*
			 * ENREGISTRER LES OPERATIONS
			 * 1. CHERCHER LES COMPTES DU CLIENT
			 * 2. CHERCHER D'ABORD LE SOLDE
			 * 3. SOLDER EN PRIORITE LES COMPTES DE LA MEME DEVISE QUE LA COMMANDE
			 * 4. INDIQUER EN LA LIASON ENTRE LA COMMANDE ET L'OPERATION
			 * 
			 *
			*/



			
			// chercher les comptes du client
			$modelCompte = model('CompteModel');
			$compteUSDClient = $modelCompte -> search(['where' => [
				'fkclient' => $idClient,
				'devise' => 'USD']
			]);

			$idCpteUSD = '';
			$idCpteCDF = '';

			if(!empty($compteUSDClient)) {
				$idCpteUSD = $compteUSDClient[0]['id'];
				$compteUSDClient = $compteUSDClient[0];	
			} 

			$compteCDFClient = $modelCompte -> search(['where' => [
				'fkclient' => $idClient,
				'devise' => 'CDF']
			]);


			if(!empty($compteCDFClient)) {
				$idCpteCDF = $compteCDFClient[0]['id'];
				$compteCDFClient = $compteCDFClient[0];
			}

			if(empty($idCpteUSD) || empty($idCpteCDF)) throw new Exception("", 1);

			


			//Preparer les lignes commande
			$parametreModel = model('ParametreModel');
			$parametre = $parametreModel -> findParametreById(1);


			if (!isset($parametre -> taux_change)) throw new Exception("Missing taux_change", 1);
			

			$tauxChange = $parametre -> taux_change - 0 ;
			if ($tauxChange == 0) throw new Exception("", 1);


			if ($devise != 'USD' && $devise != 'CDF')  throw new Exception("devise inconnue", 1);

			if($devise == 'USD') $tauxChange = 1/$tauxChange;

			$modelProduit = model('ProduitModel');

			$i = -1;

			$lignesCommande = [];

			$totalCommande = 0;

			foreach($pannier as $item) {

				$produit =  $modelProduit -> findProduitById($item ['produit']);

				$prix = $produit -> devise == $devise ? $produit -> prix_vente : $produit -> prix_vente * $tauxChange;

				$idProduit = $produit -> id;


				$quantite = $item ['quantite'];

				$total = $quantite * $prix;

				$totalCommande += $total;

				$i ++;

				$lignesCommande [$i] = [
					"fkproduit" => $idProduit,
					"montant" => $total,
					"quantite" => $quantite,
					"fkcommande" => null,
					"prix" => $prix - 0,
					"tauxChange" => $tauxChange,
					"produit" => $produit -> designation
					
				];

			}


			


			//Enregistrer d'abord l'adresse
			$modelAdresse = model('AdresseModel');
			$idAdresse = ! isset($adresse['id'])? '' : $adresse['id'];
			if (empty($idAdresse) ) {
				$idAdresse = $modelAdresse -> insert($adresse);
			} 


			helper('text');
        	$code = intval( random_string('nozero', 4));
        	$code = str_shuffle($code . $code);
        	$code = substr($code, 0, 6);

        	
        	$fraisLivraison = 0;

			//Enregistrer la Commande
			$modelCommande = model('CommandeModel');
			$commande = [
				'fkclient' => $idClient,
				'fkadresse' => $idAdresse,
				'total_cmd' => $totalCommande,
				'frais_livraison' => $fraisLivraison,
				'devise' => $devise,
				'code' => $code,
				'libelle' => $libelle
			];

			$idCommande = $modelCommande -> insert($commande);

			//Enregistrer les lignes commandes

			$ligneCommandeModel = model('LigneCommandeModel');



			foreach($lignesCommande as $item) {
				$item ['fkcommande'] = $idCommande;
				$ligneCommandeModel -> insert($item);
			}



			$total = $totalCommande + $fraisLivraison;

			//SOLDE DU CLIENT 
			$soldeCDF = $client -> solde_cdf - 0;
			$soldeUSD = $client -> solde_usd - 0;


			//test

			// $soldeCDF = 7000;
			// $soldeUSD = 4;


			//traitement d'une transaction calcul des montants 
			if($devise == 'CDF') {

				$tauxChange = $parametre -> taux_change - 0;
				if($soldeCDF >= $total){
					$newSoldeCDF = $soldeCDF - $total;
					$newSoldeUSD = $soldeUSD;

				} else if (($soldeCDF + $soldeUSD * $tauxChange) >= $total){

					//VIDER SOLDE CDF ET AJOUTER 
					$newSoldeCDF = 0;
					$newSoldeUSD = $soldeUSD - ($total - $soldeCDF)/$tauxChange;

				} else {

					$newSoldeUSD = $soldeUSD > 0 ? 0 : $soldeUSD;
					$newSoldeCDF = $soldeCDF - $total + ($soldeUSD > 0 ? $soldeUSD : 0) * $tauxChange;
				}
			} 
			else if ($devise == 'USD')
			{
				$tauxChange = 1 / $parametre -> taux_change;
				if($soldeUSD >= $total){
					$newSoldeUSD = $soldeUSD - $total;
					$newSoldeCDF = $soldeCDF;

				} else if (($soldeUSD + $soldeCDF * $tauxChange) >= $total){

					//VIDER SOLDE USD ET AJOUTER 
					$newSoldeUSD = 0;
					$newSoldeCDF = $soldeCDF - ($total - $soldeUSD)/$tauxChange;

				} else {

					$newSoldeCDF = $soldeCDF > 0 ? 0 : $soldeCDF;
					$newSoldeUSD = $soldeUSD - $total + ($soldeCDF > 0 ? $soldeCDF : 0) * $tauxChange;
				}
			}


			// $errors['COMPTE CAISSE USD'] = $caisseUSD;

			//Actualiser les comptes du client
			$client -> solde_cdf = $newSoldeCDF;
			$client -> solde_usd = $newSoldeUSD;

			$modelClient -> update( $idClient, (array)$client );

			// CREER L'OPERATION
			$modelOperation = model('OperationModel');
			$idOperation = $modelOperation -> insert([
				'type_operation' => 'PAIEMENT_CMD',
				'fkuser_create' => $idUser,
				'libelle' => 'paiement commande'
			]);


			// CREER OPERATION_COMMANDE
			$modelCmdOperation = model('OperationCommandeModel');
			$modelCmdOperation -> insert([
				'fkcommande' => $idCommande,
				'fkoperation' => $idOperation,
			]);

			//inserer les lignes lignes operations

			$modelLigneOperation = model('LigneOperationModel');

			$montantCDF = $soldeCDF - $newSoldeCDF;

			if ($montantCDF != 0) {
				$modelLigneOperation -> insert([
					'fkcompte' => $idCpteCDF,
					'fkoperation' => $idOperation,
					'montant' => $montantCDF,
					'operation' => 'CREDIT'
				]);

				$modelLigneOperation -> insert([
					'fkcompte' => $idCaisseCDF,
					'fkoperation' => $idOperation,
					'montant' => $montantCDF,
					'operation' => 'DEBIT'
				]);
			}


			$montantUSD = $soldeUSD - $newSoldeUSD;

			if ($montantUSD != 0) {
				$modelLigneOperation -> insert([
					'fkcompte' => $idCpteUSD,
					'fkoperation' => $idOperation,
					'montant' => $montantUSD,
					'operation' => 'CREDIT'
				]);

				$modelLigneOperation -> insert([
					'fkcompte' => $idCaisseUSD,
					'fkoperation' => $idOperation,
					'montant' => $montantUSD,
					'operation' => 'DEBIT'
				]);
			}



			$confirmationMessage = "Cher client, votre commande a été enregistrée. Veuillez noter le code $code qui  vous sera demandé à la livraison.";
			$messageModel = model('MessageModel');


			// $errors['COMPTE CAISSE USD'] = $confirmationMessage;

			$errors	 = $messageModel	;

			$messageId = $messageModel -> insert	(["fkuser" => ID_USER_SYSTEM, "corps_message"=> $confirmationMessage, "fkuser_destinataire" => $idUser, 	]);
			$facture = $this->generateMardownBill($idCommande, $lignesCommande, $total, $fraisLivraison , $devise,$code);


			$messageId = $messageModel -> insert	(["fkuser" => ID_USER_SYSTEM, "corps_message"=> $facture, "fkuser_destinataire" => $idUser, 	]);

 
			// $errors['COMPTE CAISSE USD'] = $confirmationMessage;


			


			return $this ->getResponse (['data' => ['facture' => $facture], 'message' => 'success']);
				
			 





			
		} catch (DatabaseException $e) {
			return $this ->getResponse([
				'error' => $e
			], ResponseInterface::HTTP_BAD_REQUEST);
		} 

	}

	function generateMardownBill($commande, $lignesCommande, $total, $frais, $devise, $code) {


		






		$markdown = $this -> arrayToMarkdownTable($lignesCommande, $devise);

		return "COMMANDE No $commande\n------------------------------------\n" .  $markdown . "------------------\nFrais de livraison : \t$frais $devise\n" . "Total :\t$total $devise\n------------------------------------\nCODE LIVRAISON : $code\n------------------";
		// return "COMMANDE No $commande\n---\n" .  $markdown . "|Frais de livraison|||$frais|\n" . "| **Total**|||**$total**|\n";

	}


	function saveSMSTransactionId()
    {


        $data = $this->getRequestInput($this->request);
        $errors = [];



        try {

        	$sender = isset($data['sender'])? $data['sender'] : null;
        	$message = isset($data['body'])? $data['body'] : null;

        	
        	

        	switch ($sender) {
        		case '436':
        			$operateur = 'AIRTEL_MONEY';
        			break;
        		case 'OrangeMoney':
        			$operateur = 'ORANGE_MONEY';
        			break;
        		case 'M-PESA':
        			$operateur = 'MPESA';
        			break;
        		
        		default:
        			$operateur =null;
        			break;
        	}

	        if( !empty($operateur))
	        {
	        	$model = model('TransactionCinetpayModel');

	        	helper('MobileMoney');

	        	$data = parseSMSNotification($message, $operateur);
	        	
	        	
	        	$id = $model -> insert($data);
	        	return $this -> getResponse(['inserted' => $model -> findTransactionCinetpayById($id)]);
	        }  
	        else 
	        {
	        	return $this->getResponse(['error' => 'Sender is not a registred Mobile Money Operator'], ResponseInterface::HTTP_BAD_REQUEST);
	        }
        } 
        catch(Exception $e)
        {
            return $this->getResponse(['error' => $e], ResponseInterface::HTTP_BAD_REQUEST);
        }
	    

    }


    






	function arrayToMarkdownTable(array $data, $devise = CDF): string {
	    
	    $markdown = "";

	    foreach($data as $item){
	    	$markdown .= '  - [' . $item['quantite'] . '] ' . strtolower($item['produit']) . ' = ' .  $item['montant'] . "$devise\n";
	    }
	    return $markdown;
	}



		
}