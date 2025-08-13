<?php

/**
 * Defines metadata for the database tables, including foreign key relationships.
 * This array can be used to programmatically understand the database schema.
 *
 * @var array<string, array{tablename: string, displayname: string, fields: array<array{name: string, type: string, label: string, editable?: bool, isEnum?: bool, enumValues?: string[], foreignKey?: array{relatedTable: string, displayField: string, valueField: string}}>}>
 */
 function getTableMetadata () {
    return [
    'user' => [
        'tablename' => 'user',
        'displayname' => 'Utilisateurs',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'username', 'type' => 'text', 'label' => 'Nom d\'utilisateur'],
            ['name' => 'email', 'type' => 'text', 'label' => 'Email'],
            ['name' => 'phone', 'type' => 'text', 'label' => 'Téléphone'],
            ['name' => 'password', 'type' => 'text', 'label' => 'Mot de passe', 'editable' => false],
            ['name' => 'access_token', 'type' => 'text', 'label' => 'Jeton', 'editable' => false],
            ['name' => 'status', 'type' => 'enum', 'label' => 'Statut', 'isEnum' => true, 'enumValues' => ['ACTIF','INACTIF']],
            ['name' => 'fkclient', 'type' => 'int', 'label' => 'Client', 'foreignKey' => ['relatedTable' => 'client', 'displayField' => 'name_complet', 'valueField' => 'id']],
            ['name' => 'fkagent', 'type' => 'int', 'label' => 'Agent', 'foreignKey' => ['relatedTable' => 'agent', 'displayField' => 'name_complet', 'valueField' => 'id']],
            ['name' => 'pref_lang', 'type' => 'enum', 'label' => 'Langue', 'isEnum' => true, 'enumValues' => ['FR','EN','SW','L']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'client' => [
        'tablename' => 'client',
        'displayname' => 'Clients',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'solde_cdf', 'type' => 'float', 'label' => 'Solde CDF'],
            ['name' => 'solde_usd', 'type' => 'float', 'label' => 'Solde USD'],
            ['name' => 'name_complet', 'type' => 'text', 'label' => 'Nom Complet'],
            ['name' => 'email', 'type' => 'text', 'label' => 'Email'],
            ['name' => 'primary_phone', 'type' => 'text', 'label' => 'Tél. Principal'],
            ['name' => 'phone_is_verified', 'type' => 'enum', 'label' => 'Tél. Vérifié', 'editable' => false, 'isEnum' => true, 'enumValues' => ['TRUE','FALSE']],
            ['name' => 'pincode', 'type' => 'text', 'label' => 'Code PIN', 'editable' => false],
            ['name' => 'devise_pref', 'type' => 'enum', 'label' => 'Devise Préf.', 'isEnum' => true, 'enumValues' => ['CDF','USD']],
            ['name' => 'status', 'type' => 'enum', 'label' => 'Statut', 'isEnum' => true, 'enumValues' => ['ACTIF','INACTIF']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false],
            ['name' => 'profession', 'type' => 'text', 'label' => 'Profession'],
            ['name' => 'adresse', 'type' => 'text', 'label' => 'Adresse'],
            ['name' => 'photo', 'type' => 'text', 'label' => 'Photo'],
            ['name' => 'statut_juridique', 'type' => 'enum', 'label' => 'Statut Juridique', 'isEnum' => true, 'enumValues' => ['PERSONNE PHYSIQUE','PERSONNE MORALE']],
            ['name' => 'avoir_credit', 'type' => 'enum', 'label' => 'Avoir Crédit', 'isEnum' => true, 'enumValues' => ['OUI','NON']]
        ]
    ],
    'produit' => [
        'tablename' => 'produit',
        'displayname' => 'Produits',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'code', 'type' => 'text', 'label' => 'Code'],
            ['name' => 'designation', 'type' => 'text', 'label' => 'Désignation'],
            ['name' => 'description', 'type' => 'text', 'label' => 'Description'],
            ['name' => 'unite', 'type' => 'text', 'label' => 'Unité'],
            ['name' => 'status', 'type' => 'enum', 'label' => 'Statut', 'isEnum' => true, 'enumValues' => ['ACTIF','INACTIF']],
            ['name' => 'devise', 'type' => 'enum', 'label' => 'Devise', 'isEnum' => true, 'enumValues' => ['CDF','USD']],
            ['name' => 'prix_vente', 'type' => 'float', 'label' => 'Prix Vente'],
            ['name' => 'poids', 'type' => 'float', 'label' => 'Poids'],
            ['name' => 'volume', 'type' => 'float', 'label' => 'Volume'],
            ['name' => 'photo', 'type' => 'text', 'label' => 'Photo'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false],
            ['name' => 'fkcategorie_prod', 'type' => 'int', 'label' => 'Catégorie', 'foreignKey' => ['relatedTable' => 'categorie_prod', 'displayField' => 'designation', 'valueField' => 'id']]
        ]
    ],
    'commande' => [
        'tablename' => 'commande',
        'displayname' => 'Commandes',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'code', 'type' => 'text', 'label' => 'Code'],
            ['name' => 'type_commande', 'type' => 'enum', 'label' => 'Type', 'isEnum' => true, 'enumValues' => ['NORMAL','EXPRESS']],
            ['name' => 'delivered_at', 'type' => 'text', 'label' => 'Livré le', 'editable' => false],
            ['name' => 'status_cmd', 'type' => 'enum', 'label' => 'Statut Cmd', 'isEnum' => true, 'enumValues' => ['ATTENTE','LIVRE','REJETE','ACHEMINEMENT']],
            ['name' => 'status_payement', 'type' => 'enum', 'label' => 'Statut Paiement', 'isEnum' => true, 'enumValues' => ['NO-PAYE','ACCOMPTE','PAYE']],
            ['name' => 'fkclient', 'type' => 'int', 'label' => 'Client', 'foreignKey' => ['relatedTable' => 'client', 'displayField' => 'name_complet', 'valueField' => 'id']],
            ['name' => 'fkadresse', 'type' => 'int', 'label' => 'Adresse', 'foreignKey' => ['relatedTable' => 'adresse', 'displayField' => 'libelle_kasokoo', 'valueField' => 'id']],
            ['name' => 'total_cmd', 'type' => 'float', 'label' => 'Total'],
            ['name' => 'frais_livraison', 'type' => 'float', 'label' => 'Frais Livraison'],
            ['name' => 'devise', 'type' => 'enum', 'label' => 'Devise', 'isEnum' => true, 'enumValues' => ['CDF','USD']],
            ['name' => 'libelle', 'type' => 'text', 'label' => 'Libellé'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'achat' => [
        'tablename' => 'achat',
        'displayname' => 'Achats',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false],
            ['name' => 'delivered_at', 'type' => 'text', 'label' => 'Livré le', 'editable' => false],
            ['name' => 'status_payement', 'type' => 'enum', 'label' => 'Statut Paiement', 'isEnum' => true, 'enumValues' => ['NO-PAYE','ACCOMPTE','PAYE']],
            ['name' => 'fkagent', 'type' => 'int', 'label' => 'Agent', 'foreignKey' => ['relatedTable' => 'agent', 'displayField' => 'name_complet', 'valueField' => 'id']],
            ['name' => 'total_achat', 'type' => 'float', 'label' => 'Total Achat'],
            ['name' => 'frais_logistique', 'type' => 'float', 'label' => 'Frais Logistique'],
            ['name' => 'devise', 'type' => 'enum', 'label' => 'Devise', 'isEnum' => true, 'enumValues' => ['CDF','USD']],
            ['name' => 'code_cmd', 'type' => 'text', 'label' => 'Code CMD'],
            ['name' => 'code_achat', 'type' => 'text', 'label' => 'Code Achat'],
            ['name' => 'libelle_cmd', 'type' => 'text', 'label' => 'Libellé CMD'],
            ['name' => 'libelle_achat', 'type' => 'text', 'label' => 'Libellé Achat'],
            ['name' => 'status_cmd', 'type' => 'enum', 'label' => 'Statut CMD', 'isEnum' => true, 'enumValues' => ['RECU','STOCKAGE','ATTENTE','VALIDE']],
            ['name' => 'fkfournisseur', 'type' => 'int', 'label' => 'Fournisseur', 'foreignKey' => ['relatedTable' => 'fournisseur', 'displayField' => 'denomination', 'valueField' => 'id']]
        ]
    ],
    'adresse' => [
        'tablename' => 'adresse',
        'displayname' => 'Adresses',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkuser_create', 'type' => 'int', 'label' => 'Créé par', 'foreignKey' => ['relatedTable' => 'user', 'displayField' => 'username', 'valueField' => 'id']],
            ['name' => 'fkuser_validate', 'type' => 'int', 'label' => 'Validé par', 'foreignKey' => ['relatedTable' => 'user', 'displayField' => 'username', 'valueField' => 'id']],
            ['name' => 'longitude', 'type' => 'float', 'label' => 'Longitude'],
            ['name' => 'latitude', 'type' => 'float', 'label' => 'Latitude'],
            ['name' => 'is_registred', 'type' => 'enum', 'label' => 'Enregistrée', 'isEnum' => true, 'enumValues' => ['TRUE','FALSE']],
            ['name' => 'code_OLC', 'type' => 'text', 'label' => 'Code OLC'],
            ['name' => 'numero_rue', 'type' => 'text', 'label' => 'N° Rue'],
            ['name' => 'description_batiment', 'type' => 'text', 'label' => 'Description Bâtiment'],
            ['name' => 'libelle_client', 'type' => 'text', 'label' => 'Libellé Client'],
            ['name' => 'libelle_kasokoo', 'type' => 'text', 'label' => 'Libellé Kasokoo'],
            ['name' => 'avenue', 'type' => 'text', 'label' => 'Avenue'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'agent' => [
        'tablename' => 'agent',
        'displayname' => 'Agents',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'name_complet', 'type' => 'text', 'label' => 'Nom Complet'],
            ['name' => 'fonction', 'type' => 'enum', 'label' => 'Fonction', 'isEnum' => true, 'enumValues' => ['LIVREUR','ADMIN','LOGISTICIEN']],
            ['name' => 'solde_cdf', 'type' => 'float', 'label' => 'Solde CDF'],
            ['name' => 'solde_usd', 'type' => 'float', 'label' => 'Solde USD'],
            ['name' => 'phone', 'type' => 'text', 'label' => 'Téléphone'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'caisse' => [
        'tablename' => 'caisse',
        'displayname' => 'Caisses',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'designation', 'type' => 'text', 'label' => 'Désignation'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'categorie_prod' => [
        'tablename' => 'categorie_prod',
        'displayname' => 'Catégories Produits',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'designation', 'type' => 'text', 'label' => 'Désignation'],
            ['name' => 'description', 'type' => 'text', 'label' => 'Description'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'compte' => [
        'tablename' => 'compte',
        'displayname' => 'Comptes',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'devise', 'type' => 'enum', 'label' => 'Devise', 'isEnum' => true, 'enumValues' => ['CDF','USD']],
            ['name' => 'intutile', 'type' => 'text', 'label' => 'Intitulé'],
            ['name' => 'type_compte', 'type' => 'enum', 'label' => 'Type de Compte', 'isEnum' => true, 'enumValues' => ['COMPTE_DE_GESTION','COMPTE_CLIENT','COMPTE_FOURNISSEUR']],
            ['name' => 'fkclient', 'type' => 'int', 'label' => 'Client', 'foreignKey' => ['relatedTable' => 'client', 'displayField' => 'name_complet', 'valueField' => 'id']],
            ['name' => 'fkagent', 'type' => 'int', 'label' => 'Agent', 'foreignKey' => ['relatedTable' => 'agent', 'displayField' => 'name_complet', 'valueField' => 'id']],
            ['name' => 'fkfournisseur', 'type' => 'int', 'label' => 'Fournisseur', 'foreignKey' => ['relatedTable' => 'fournisseur', 'displayField' => 'denomination', 'valueField' => 'id']],
            ['name' => 'fkcaisse', 'type' => 'int', 'label' => 'Caisse', 'foreignKey' => ['relatedTable' => 'caisse', 'displayField' => 'designation', 'valueField' => 'id']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'droits' => [
        'tablename' => 'droits',
        'displayname' => 'Droits',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'name', 'type' => 'text', 'label' => 'Nom'],
            ['name' => 'code', 'type' => 'text', 'label' => 'Code'],
            ['name' => 'fkmodule', 'type' => 'int', 'label' => 'Module', 'foreignKey' => ['relatedTable' => 'module', 'displayField' => 'name', 'valueField' => 'id']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'droits_agent' => [
        'tablename' => 'droits_agent',
        'displayname' => 'Droits Agents',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'status', 'type' => 'enum', 'label' => 'Statut', 'isEnum' => true, 'enumValues' => ['ACTIF','INACTIF']],
            ['name' => 'fkagent', 'type' => 'int', 'label' => 'Agent', 'foreignKey' => ['relatedTable' => 'agent', 'displayField' => 'name_complet', 'valueField' => 'id']],
            ['name' => 'fkdroit', 'type' => 'int', 'label' => 'Droit', 'foreignKey' => ['relatedTable' => 'droits', 'displayField' => 'name', 'valueField' => 'id']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'fournisseur' => [
        'tablename' => 'fournisseur',
        'displayname' => 'Fournisseurs',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'denomination', 'type' => 'text', 'label' => 'Dénomination'],
            ['name' => 'adresse', 'type' => 'text', 'label' => 'Adresse'],
            ['name' => 'phone', 'type' => 'text', 'label' => 'Téléphone'],
            ['name' => 'email', 'type' => 'text', 'label' => 'Email'],
            ['name' => 'fkadresse', 'type' => 'int', 'label' => 'Adresse FK', 'foreignKey' => ['relatedTable' => 'adresse', 'displayField' => 'libelle_kasokoo', 'valueField' => 'id']],
            ['name' => 'forme_juridique', 'type' => 'enum', 'label' => 'Forme Juridique', 'isEnum' => true, 'enumValues' => ['PERSONNE PHYSIQUE','PERSONNE MORALE']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'information_paiement' => [
        'tablename' => 'information_paiement',
        'displayname' => 'Infos Paiement',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'operateur', 'type' => 'enum', 'label' => 'Opérateur', 'isEnum' => true, 'enumValues' => ['MPESA','ORANGE_MONEY','AIRTEL_MONEY']],
            ['name' => 'numero_compte', 'type' => 'text', 'label' => 'N° Compte'],
            ['name' => 'intutile_compte', 'type' => 'text', 'label' => 'Intitulé Compte'],
            ['name' => 'banque', 'type' => 'text', 'label' => 'Banque'],
            ['name' => 'fkclient', 'type' => 'int', 'label' => 'Client', 'foreignKey' => ['relatedTable' => 'client', 'displayField' => 'name_complet', 'valueField' => 'id']],
            ['name' => 'fkcaisse', 'type' => 'int', 'label' => 'Caisse', 'foreignKey' => ['relatedTable' => 'caisse', 'displayField' => 'designation', 'valueField' => 'id']],
            ['name' => 'fkfournisseur', 'type' => 'int', 'label' => 'Fournisseur', 'foreignKey' => ['relatedTable' => 'fournisseur', 'displayField' => 'denomination', 'valueField' => 'id']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'ligne_achat' => [
        'tablename' => 'ligne_achat',
        'displayname' => 'Lignes Achat',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkproduit', 'type' => 'int', 'label' => 'Produit', 'foreignKey' => ['relatedTable' => 'produit', 'displayField' => 'designation', 'valueField' => 'id']],
            ['name' => 'fkfournisseur', 'type' => 'int', 'label' => 'Fournisseur', 'foreignKey' => ['relatedTable' => 'fournisseur', 'displayField' => 'denomination', 'valueField' => 'id']],
            ['name' => 'fkachat', 'type' => 'int', 'label' => 'Achat', 'foreignKey' => ['relatedTable' => 'achat', 'displayField' => 'code_achat', 'valueField' => 'id']],
            ['name' => 'quantite', 'type' => 'float', 'label' => 'Quantité'],
            ['name' => 'montant', 'type' => 'float', 'label' => 'Montant'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'ligne_commande' => [
        'tablename' => 'ligne_commande',
        'displayname' => 'Lignes Commande',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkproduit', 'type' => 'int', 'label' => 'Produit', 'foreignKey' => ['relatedTable' => 'produit', 'displayField' => 'designation', 'valueField' => 'id']],
            ['name' => 'fkcommande', 'type' => 'int', 'label' => 'Commande', 'foreignKey' => ['relatedTable' => 'commande', 'displayField' => 'code', 'valueField' => 'id']],
            ['name' => 'quantite', 'type' => 'float', 'label' => 'Quantité'],
            ['name' => 'montant', 'type' => 'float', 'label' => 'Montant'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'ligne_operation' => [
        'tablename' => 'ligne_operation',
        'displayname' => 'Lignes Opération',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkcompte', 'type' => 'int', 'label' => 'Compte', 'foreignKey' => ['relatedTable' => 'compte', 'displayField' => 'intutile', 'valueField' => 'id']],
            ['name' => 'fkoperation', 'type' => 'int', 'label' => 'Opération', 'foreignKey' => ['relatedTable' => 'operation', 'displayField' => 'libelle', 'valueField' => 'id']],
            ['name' => 'fkinfo_paiement', 'type' => 'int', 'label' => 'Info Paiement', 'foreignKey' => ['relatedTable' => 'information_paiement', 'displayField' => 'numero_compte', 'valueField' => 'id']],
            ['name' => 'fkuser_create', 'type' => 'int', 'label' => 'Créé par', 'foreignKey' => ['relatedTable' => 'user', 'displayField' => 'username', 'valueField' => 'id']],
            ['name' => 'operation', 'type' => 'enum', 'label' => 'Opération', 'isEnum' => true, 'enumValues' => ['DEBIT','CREDIT']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'livraison' => [
        'tablename' => 'livraison',
        'displayname' => 'Livraisons',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkcommande', 'type' => 'int', 'label' => 'Commande', 'foreignKey' => ['relatedTable' => 'commande', 'displayField' => 'code', 'valueField' => 'id']],
            ['name' => 'fkagent', 'type' => 'int', 'label' => 'Agent', 'foreignKey' => ['relatedTable' => 'agent', 'displayField' => 'name_complet', 'valueField' => 'id']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false],
            ['name' => 'delivered_at', 'type' => 'text', 'label' => 'Livré le', 'editable' => false],
            ['name' => 'status', 'type' => 'enum', 'label' => 'Statut', 'isEnum' => true, 'enumValues' => ['ENCOURS','LIVRE','NON_LIVRE']]
        ]
    ],
    'message' => [
        'tablename' => 'message',
        'displayname' => 'Messages',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkuser', 'type' => 'int', 'label' => 'Utilisateur', 'foreignKey' => ['relatedTable' => 'user', 'displayField' => 'username', 'valueField' => 'id']],
            ['name' => 'fkuser_destinataire', 'type' => 'int', 'label' => 'Destinataire', 'foreignKey' => ['relatedTable' => 'user', 'displayField' => 'username', 'valueField' => 'id']],
            ['name' => 'fkmessage_prec', 'type' => 'int', 'label' => 'Msg Préc.', 'foreignKey' => ['relatedTable' => 'message', 'displayField' => 'id', 'valueField' => 'id']],
            ['name' => 'isread', 'type' => 'enum', 'label' => 'Lu', 'isEnum' => true, 'enumValues' => ['TRUE','FALSE']],
            ['name' => 'media', 'type' => 'text', 'label' => 'Média'],
            ['name' => 'media_type', 'type' => 'enum', 'label' => 'Type Média', 'isEnum' => true, 'enumValues' => ['IMAGE','AUDIO','VIDEO','DOC']],
            ['name' => 'corps_message', 'type' => 'text', 'label' => 'Message'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'module' => [
        'tablename' => 'module',
        'displayname' => 'Modules',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'name', 'type' => 'text', 'label' => 'Nom'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'operation' => [
        'tablename' => 'operation',
        'displayname' => 'Opérations',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkuser_create', 'type' => 'int', 'label' => 'Créé par', 'foreignKey' => ['relatedTable' => 'user', 'displayField' => 'username', 'valueField' => 'id']],
            ['name' => 'libelle', 'type' => 'text', 'label' => 'Libellé'],
            ['name' => 'type_operation', 'type' => 'enum', 'label' => 'Type Opération', 'isEnum' => true, 'enumValues' => ['DEPOT_CLIENT','RETRAIT_CLIENT','PAIEMENT_CMD','ACHAT_STOCK','CONVERSION_DEVISE','CREDIT_ACCORDE','DETTE_CONTRACTEE','AUTRE']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'operation_achat' => [
        'tablename' => 'operation_achat',
        'displayname' => 'Opérations Achat',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkachat', 'type' => 'int', 'label' => 'Achat', 'foreignKey' => ['relatedTable' => 'achat', 'displayField' => 'code_achat', 'valueField' => 'id']],
            ['name' => 'fkoperation', 'type' => 'int', 'label' => 'Opération', 'foreignKey' => ['relatedTable' => 'operation', 'displayField' => 'libelle', 'valueField' => 'id']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'operation_commande' => [
        'tablename' => 'operation_commande',
        'displayname' => 'Opérations Commande',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkcommande', 'type' => 'int', 'label' => 'Commande', 'foreignKey' => ['relatedTable' => 'commande', 'displayField' => 'code', 'valueField' => 'id']],
            ['name' => 'fkoperation', 'type' => 'int', 'label' => 'Opération', 'foreignKey' => ['relatedTable' => 'operation', 'displayField' => 'libelle', 'valueField' => 'id']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'otp' => [
        'tablename' => 'otp',
        'displayname' => 'Codes OTP',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'value', 'type' => 'int', 'label' => 'Valeur'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false],
            ['name' => 'expire_at', 'type' => 'text', 'label' => 'Expire le', 'editable' => false],
            ['name' => 'sent_to', 'type' => 'text', 'label' => 'Envoyé à'],
            ['name' => 'fkuser', 'type' => 'int', 'label' => 'Utilisateur', 'foreignKey' => ['relatedTable' => 'user', 'displayField' => 'username', 'valueField' => 'id']],
            ['name' => 'status', 'type' => 'enum', 'label' => 'Statut', 'isEnum' => true, 'enumValues' => ['SENT','CREATED','EXPIRED','CHECKED']]
        ]
    ],
    'otp_sender_device' => [
        'tablename' => 'otp_sender_device',
        'displayname' => 'Appareils Envoi OTP',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'token', 'type' => 'text', 'label' => 'Jeton'],
            ['name' => 'numero_sim', 'type' => 'text', 'label' => 'N° SIM'],
            ['name' => 'status', 'type' => 'enum', 'label' => 'Statut', 'isEnum' => true, 'enumValues' => ['ACTIF','DESACTIVE','OCCUPE']],
            ['name' => 'reseau', 'type' => 'enum', 'label' => 'Réseau', 'isEnum' => true, 'enumValues' => ['ORANGE','AIRTEL','VODACOM','TOUS']],
            ['name' => 'sms_sent_at', 'type' => 'text', 'label' => 'SMS Envoyé le', 'editable' => false],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'parametre' => [
        'tablename' => 'parametre',
        'displayname' => 'Paramètres',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'devise', 'type' => 'enum', 'label' => 'Devise', 'isEnum' => true, 'enumValues' => ['CDF','USD']],
            ['name' => 'taux_change', 'type' => 'float', 'label' => 'Taux Change'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false],
            ['name' => 'adresse', 'type' => 'text', 'label' => 'Adresse'],
            ['name' => 'phone', 'type' => 'text', 'label' => 'Téléphone'],
            ['name' => 'email', 'type' => 'text', 'label' => 'Email'],
            ['name' => 'logo', 'type' => 'text', 'label' => 'Logo'],
            ['name' => 'app_version', 'type' => 'text', 'label' => 'Version App']
        ]
    ],
    'publicite' => [
        'tablename' => 'publicite',
        'displayname' => 'Publicités',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkproduit', 'type' => 'int', 'label' => 'Produit', 'foreignKey' => ['relatedTable' => 'produit', 'displayField' => 'designation', 'valueField' => 'id']],
            ['name' => 'image', 'type' => 'text', 'label' => 'Image'],
            ['name' => 'corps', 'type' => 'text', 'label' => 'Corps'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'transaction_cinetpay' => [
        'tablename' => 'transaction_cinetpay',
        'displayname' => 'Trans. CinetPay',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'fkclient', 'type' => 'int', 'label' => 'Client', 'foreignKey' => ['relatedTable' => 'client', 'displayField' => 'name_complet', 'valueField' => 'id']],
            ['name' => 'montant', 'type' => 'float', 'label' => 'Montant'],
            ['name' => 'devise', 'type' => 'enum', 'label' => 'Devise', 'isEnum' => true, 'enumValues' => ['CDF','USD']],
            ['name' => 'data_json', 'type' => 'text', 'label' => 'Données JSON', 'editable' => false],
            ['name' => 'status', 'type' => 'text', 'label' => 'Statut'],
            ['name' => 'numero', 'type' => 'text', 'label' => 'Numéro'],
            ['name' => 'transaction_id', 'type' => 'text', 'label' => 'ID Trans.'],
            ['name' => 'transaction_token', 'type' => 'text', 'label' => 'Jeton Trans.'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'user_device' => [
        'tablename' => 'user_device',
        'displayname' => 'Appareils Utilisateurs',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'uuid', 'type' => 'text', 'label' => 'UUID'],
            ['name' => 'fcm_token', 'type' => 'text', 'label' => 'Jeton FCM', 'editable' => false],
            ['name' => 'device_info', 'type' => 'text', 'label' => 'Infos Appareil'],
            ['name' => 'fkuser', 'type' => 'int', 'label' => 'Utilisateur', 'foreignKey' => ['relatedTable' => 'user', 'displayField' => 'username', 'valueField' => 'id']],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ],
    'zone_couverture' => [
        'tablename' => 'zone_couverture',
        'displayname' => 'Zones de Couverture',
        'fields' => [
            ['name' => 'id', 'type' => 'int', 'label' => 'ID', 'editable' => false],
            ['name' => 'status', 'type' => 'enum', 'label' => 'Statut', 'isEnum' => true, 'enumValues' => ['ACTIF','INACTIF']],
            ['name' => 'designation', 'type' => 'text', 'label' => 'Désignation'],
            ['name' => 'frontieres', 'type' => 'text', 'label' => 'Frontières'],
            ['name' => 'created_at', 'type' => 'text', 'label' => 'Créé le', 'editable' => false],
            ['name' => 'updated_at', 'type' => 'text', 'label' => 'Modifié le', 'editable' => false]
        ]
    ]
];
 }

/**
 * META DATA JSON GENERATOR Generate sql code to join all the related table in a single
 */
class MetadataGenerator 
{
    
    function __construct()
    {
        // code...
        // --- Main logic to generate JSON output ---

// Set the header to return JSON
header('Content-Type: application/json');
    $tableMetadata = getTableMetadata();

$graph = $this -> buildGraph($tableMetadata);
$allTableNames = array_keys($tableMetadata);
$finalResult = [];

foreach ($allTableNames as $startTable) {
    foreach ($allTableNames as $endTable) {
        if ($startTable !== $endTable) {
            $allPaths = [];
            $visited = [];
            $path = [];
            findAllPaths($graph, $startTable, $endTable, $visited, $path, $allPaths);

            if (!empty($allPaths)) {
                $pathsForPair = [];
                foreach ($allPaths as $currentPath) {
                    $joinConditions = [];
                    for ($i = 0; $i < count($currentPath) - 1; $i++) {
                        $joinConditions[] = [
                            'source_table' => $currentPath[$i]['table'],
                            'source_field' => $currentPath[$i]['fk_field'],
                            'destination_table' => $currentPath[$i + 1]['table'],
                            'destination_field' => 'id' // Assuming foreign keys join to the 'id' field
                        ];
                    }

                    $pathString = implode(' -> ', array_map(function($tableInfo) {
                        return $tableInfo['table'] . (isset($tableInfo['fk_field']) ? " via '{$tableInfo['fk_field']}'" : "");
                    }, $currentPath));

                    $pathsForPair[] = [
                        'path' => $pathString,
                        'join_conditions' => $joinConditions,
                        'sql_query' => generateSqlQuery($currentPath)
                    ];
                }
                
                $finalResult[] = [
                    'sourcetable' => $startTable,
                    'finaltable' => $endTable,
                    'paths' => $pathsForPair
                ];
            }
        }
    }
}

    // Return the JSON data
    echo json_encode($finalResult, JSON_PRETTY_PRINT);
        
    }

    /**
 * Defines metadata for the database tables, including foreign key relationships.
 * This array can be used to programmatically understand the database schema.
 *
 * @var array<string, array{tablename: string, displayname: string, fields: array<array{name: string, type: string, label: string, editable?: bool, isEnum?: bool, enumValues?: string[], foreignKey?: array{relatedTable: string, displayField: string, valueField: string}}>}>
 */
// Assuming helper('tables_metadata') and getTableMetadata() are defined elsewhere.

// helper('tables_metadata');


/**
 * Builds the graph from the metadata.
 *
 * @param array $metadata The table metadata.
 * @return array The adjacency list representation of the graph, including foreign key fields.
 */
function buildGraph(array $metadata): array
{
    $graph = [];
    foreach ($metadata as $tableName => $table) {
        if (!isset($graph[$tableName])) {
            $graph[$tableName] = [];
        }
        foreach ($table['fields'] as $field) {
            if (isset($field['foreignKey'])) {
                $relatedTable = $field['foreignKey']['relatedTable'];
                $foreignKeyField = $field['name'];
                // Add a directed edge with the foreign key field
                $graph[$tableName][] = [
                    'neighbor' => $relatedTable,
                    'fk_field' => $foreignKeyField
                ];
                // Ensure the related table is in the graph
                if (!isset($graph[$relatedTable])) {
                    $graph[$relatedTable] = [];
                }
            }
        }
    }
    return $graph;
}


/**
 * Finds all paths between two nodes using Depth-First Search (DFS), recording the join fields.
 *
 * @param array $graph The graph in adjacency list format.
 * @param string $startNode The starting table.
 * @param string $endNode The ending table.
 * @param array $visited Array to track visited nodes in the current path.
 * @param array $path The current path being explored.
 * @param array $allPaths All found paths.
 * @return void
 */
function findAllPaths(array $graph, string $startNode, string $endNode, array &$visited, array &$path, array &$allPaths): void
{
    $visited[$startNode] = true;
    $path[] = ['table' => $startNode];

    if ($startNode === $endNode) {
        $allPaths[] = $path;
    } else {
        if (isset($graph[$startNode])) {
            foreach ($graph[$startNode] as $edge) {
                $neighbor = $edge['neighbor'];
                $fk_field = $edge['fk_field'];
                if (!isset($visited[$neighbor])) {
                    $newPath = $path;
                    $newPath[count($newPath) - 1]['fk_field'] = $fk_field;
                    findAllPaths($graph, $neighbor, $endNode, $visited, $newPath, $allPaths);
                }
            }
        }
    }

    array_pop($path);
    unset($visited[$startNode]);
}

/**
 * Generates an SQL JOIN query for a given path of tables.
 *
 * @param array $path The path of tables and join fields.
 * @return string The generated SQL query.
 */
function generateSqlQuery(array $path): string
{
    $selectClause = "SELECT *";
    $fromClause = "FROM {$path[0]['table']}";
    $joinClauses = [];

    for ($i = 0; $i < count($path) - 1; $i++) {
        $currentTable = $path[$i]['table'];
        $nextTable = $path[$i + 1]['table'];
        $fkField = $path[$i]['fk_field'];
        $joinClauses[] = "INNER JOIN {$nextTable} ON {$currentTable}.{$fkField} = {$nextTable}.id";
    }

    return "{$selectClause} {$fromClause} " . implode(" ", $joinClauses) . ";";
}



}