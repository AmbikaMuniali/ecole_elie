<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasokoo Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/vue-router@4/dist/vue-router.global.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body { 
            display: flex; 
            flex-direction: row;
            min-height: 100vh; 
        }
        
        #navbar-top {
            position: fixed; 
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
            border-right: 1px solid #dee2e6;
            box-shadow: 2px 0 4px rgba(0,0,0,.05);
            overflow-y: auto;
            z-index: 1050;
        }
        #navbar-top .nav { flex-direction: column; }
        #navbar-top .nav-link { color: #495057; }
        #navbar-top .nav-link.router-link-active {
            font-weight: bold;
            color: #0d6efd;
            background-color: #e2e6ea;
        }

        #main-content { 
            flex-grow: 1; 
            padding: 20px; 
            margin-left: 250px;
            height: 100vh;
            overflow-y: auto; 
        }
        
        .table-responsive { 
            max-height: calc(100vh - 220px); 
            overflow-x: auto; 
        }
        thead th { 
            position: sticky; 
            top: 0; 
            z-index: 10; 
            background-color: #343a40; 
            color: white; 
        } 
        th { cursor: pointer; user-select: none; white-space: nowrap; }
        
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px; 
            z-index: 1100; 
        }
        .add-row {
            position: sticky;
            bottom: 0;
            z-index: 9;
            background-color: #f8f9fa;
        }
        .loading-spinner {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div id="app">
        <nav id="navbar-top">
            <h5>Tables de la BDD</h5>
            <ul class="nav">
                <li v-for="table in tables" :key="table" class="nav-item">
                     <router-link :to="'/table/' + table" class="nav-link">
                        {{ formatTableName(table) }}
                    </router-link>
                </li>
            </ul>
        </nav>

        <main id="main-content">
            <router-view :key="$route.fullPath"></router-view>
        </main>
        
        <div class="toast-container notification-toast">
            </div>
    </div>

    <script type="text/x-template" id="table-component-template">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Table: <span class="text-primary">{{ currentTableMeta.displayname }}</span></h4>
                    <div>
                        <button class="btn btn-light btn-sm me-2" @click="openColumnSettingsModal" title="Personnaliser les colonnes">
                            <i class="bi bi-gear-fill"></i>
                        </button>
                        <button class="btn btn-primary btn-sm" @click="refreshData" :disabled="loading" title="Actualiser les données">
                            <i class="bi bi-arrow-clockwise" :class="{'loading-spinner': loading}"></i> Actualiser
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div v-if="error" class="alert alert-danger">{{ error }}</div>
                    
                    <div v-if="loading && !data.length" class="text-center p-5">
                        <div class="spinner-border" role="status"><span class="visually-hidden">Chargement...</span></div>
                    </div>

                    <div v-if="!loading || data.length">
                         <div class="mb-3">
                            <input type="text" v-model="searchQuery" class="form-control" placeholder="Filtrer les données...">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th v-for="field in displayedFields" :key="field.name" @click="sortBy(field.name)" :class="{ active: sortKey === field.name }">
                                            {{ field.label }}
                                            <span v-if="sortKey === field.name" class="sort-icon">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
                                        </th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="add-row">
                                        <td v-for="field in displayedFields" :key="'new-' + field.name">
                                            <template v-if="field.editable !== false">
                                                <!-- Handle ENUM fields for adding new records -->
                                                <select v-if="field.isEnum" v-model="newRecord[field.name]" class="form-select form-select-sm">
                                                    <option :value="undefined">-- Sélectionner --</option>
                                                    <option v-for="enumValue in field.enumValues" :value="enumValue">{{ enumValue }}</option>
                                                </select>
                                                <!-- Handle Foreign Key fields for adding new records -->
                                                <select v-else-if="field.foreignKey" v-model="newRecord[field.name]" class="form-select form-select-sm">
                                                    <option :value="undefined">-- Sélectionner --</option>
                                                    <option v-for="item in relatedData[field.foreignKey.relatedTable]" :value="item[field.foreignKey.valueField]">{{ item[field.foreignKey.displayField] }}</option>
                                                </select>
                                                <!-- Default input for other types -->
                                                <input v-else :type="field.type === 'int' || field.type === 'float' ? 'number' : 'text'" v-model="newRecord[field.name]" class="form-control form-control-sm" />
                                            </template>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm" @click="addRecord"><i class="bi bi-plus-circle"></i> Ajouter</button>
                                        </td>
                                    </tr>
                                    <tr v-for="item in filteredData" :key="item.id">
                                        <td v-for="field in displayedFields" :key="field.name" @dblclick="editCell(item, field)">
                                            <div v-if="editing.id === item.id && editing.field === field.name">
                                                <!-- Handle ENUM fields for editing -->
                                                <select v-if="field.isEnum" v-model="editing.value" @blur="saveCell(item, field.name)" @keyup.enter="saveCell(item, field.name)" @keyup.esc="cancelEdit" v-focus class="form-select form-select-sm">
                                                    <option v-for="enumValue in field.enumValues" :value="enumValue">{{ enumValue }}</option>
                                                </select>
                                                <!-- Handle Foreign Key fields for editing -->
                                                <select v-else-if="field.foreignKey" v-model="editing.value" @blur="saveCell(item, field.name)" @keyup.enter="saveCell(item, field.name)" @keyup.esc="cancelEdit" v-focus class="form-select form-select-sm">
                                                     <option v-for="relatedItem in relatedData[field.foreignKey.relatedTable]" :value="relatedItem[field.foreignKey.valueField]">{{ relatedItem[field.foreignKey.displayField] }}</option>
                                                </select>
                                                <!-- Default input for other types -->
                                                <input v-else :type="field.type === 'int' || field.type === 'float' ? 'number' : 'text'" v-model="editing.value" @blur="saveCell(item, field.name)" @keyup.enter="saveCell(item, field.name)" @keyup.esc="cancelEdit" v-focus class="form-control form-control-sm"/>
                                            </div>
                                            <div v-else>
                                                <!-- Display for ENUM fields -->
                                                <span v-if="field.isEnum">{{ item[field.name] }}</span>
                                                <!-- Display for Foreign Key fields -->
                                                <span v-else-if="field.foreignKey && relatedData[field.foreignKey.relatedTable]">
                                                    {{ (relatedData[field.foreignKey.relatedTable].find(r => r[field.foreignKey.valueField] == item[field.name]) || {})[field.foreignKey.displayField] || item[field.name] }}
                                                </span>
                                                <!-- Default display -->
                                                <span v-else>{{ item[field.name] }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm" @click="deleteRecord(item.id)"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                         <div v-if="!filteredData.length" class="alert alert-info mt-3">Aucune donnée à afficher.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="columnSettingsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Personnaliser l'affichage</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <h6>Colonnes Visibles et Ordre</h6>
                        <ul class="list-group">
                            <li v-for="(field, index) in managedFields" :key="field.name" class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <input class="form-check-input me-2" type="checkbox" v-model="field.visible" :id="'check-' + field.name">
                                    <label class="form-check-label" :for="'check-' + field.name">{{ field.label }}</label>
                                </div>
                                <div>
                                    <button class="btn btn-light btn-sm" @click="moveColumn(index, -1)" :disabled="index === 0"><i class="bi bi-arrow-up"></i></button>
                                    <button class="btn btn-light btn-sm ms-1" @click="moveColumn(index, 1)" :disabled="index === managedFields.length - 1"><i class="bi bi-arrow-down"></i></button>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary" @click="saveColumnSettings">Appliquer</button>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script>
        const API_BASE_URL = '<?php echo base_url(); ?>'; 
        const { createApp, ref, reactive, computed, onMounted, watch, nextTick } = Vue;
        const { createRouter, createWebHashHistory } = VueRouter;

        // Updated tableMetadata with isEnum and enumValues
        const tableMetadata = new Map([
            ['user', { tablename: 'user', displayname: 'Utilisateurs', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'username', type: 'text', label: 'Nom d\'utilisateur'}, {name: 'email', type: 'text', label: 'Email'}, {name: 'phone', type: 'text', label: 'Téléphone'}, {name: 'password', type: 'text', label: 'Mot de passe', editable: false}, {name: 'access_token', type: 'text', label: 'Jeton', editable: false}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['ACTIF','INACTIF']}, {name: 'fkclient', type: 'int', label: 'Client', foreignKey: {relatedTable: 'client', displayField: 'name_complet', valueField: 'id'}}, {name: 'fkagent', type: 'int', label: 'Agent', foreignKey: {relatedTable: 'agent', displayField: 'name_complet', valueField: 'id'}}, {name: 'pref_lang', type: 'enum', label: 'Langue', isEnum: true, enumValues: ['FR','EN','SW','L']}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['client', { tablename: 'client', displayname: 'Clients', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'solde_cdf', type: 'float', label: 'Solde CDF'}, {name: 'solde_usd', type: 'float', label: 'Solde USD'}, {name: 'name_complet', type: 'text', label: 'Nom Complet'}, {name: 'email', type: 'text', label: 'Email'}, {name: 'primary_phone', type: 'text', label: 'Tél. Principal'}, {name: 'phone_is_verified', type: 'enum', label: 'Tél. Vérifié', editable: false, isEnum: true, enumValues: ['TRUE','FALSE']}, {name: 'pincode', type: 'text', label: 'Code PIN', editable: false}, {name: 'devise_pref', type: 'enum', label: 'Devise Préf.', isEnum: true, enumValues: ['CDF','USD']}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['ACTIF','INACTIF']}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false}, {name: 'profession', type: 'text', label: 'Profession'}, {name: 'adresse', type: 'text', label: 'Adresse'}, {name: 'photo', type: 'text', label: 'Photo'}, {name: 'statut_juridique', type: 'enum', label: 'Statut Juridique', isEnum: true, enumValues: ['PERSONNE PHYSIQUE','PERSONNE MORALE']}, {name: 'avoir_credit', type: 'enum', label: 'Avoir Crédit', isEnum: true, enumValues: ['OUI','NON']} ] }],
            ['produit', { tablename: 'produit', displayname: 'Produits', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'code', type: 'text', label: 'Code'}, {name: 'designation', type: 'text', label: 'Désignation'}, {name: 'description', type: 'text', label: 'Description'}, {name: 'unite', type: 'text', label: 'Unité'}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['ACTIF','INACTIF']}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['CDF','USD']}, {name: 'prix_vente', type: 'float', label: 'Prix Vente'}, {name: 'poids', type: 'float', label: 'Poids'}, {name: 'volume', type: 'float', label: 'Volume'}, {name: 'photo', type: 'text', label: 'Photo'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false}, {name: 'fkcategorie_prod', type: 'int', label: 'Catégorie', foreignKey: {relatedTable: 'categorie_prod', displayField: 'designation', valueField: 'id'}} ] }],
            ['commande', { tablename: 'commande', displayname: 'Commandes', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'type_commande', type: 'enum', label: 'Type', isEnum: true, enumValues: ['NORMAL','EXPRESS']}, {name: 'delivered_at', type: 'text', label: 'Livré le', editable: false}, {name: 'status_cmd', type: 'enum', label: 'Statut Cmd', isEnum: true, enumValues: ['ATTENTE','LIVRE','REJETE','ACHEMINEMENT']}, {name: 'status_payement', type: 'enum', label: 'Statut Paiement', isEnum: true, enumValues: ['NO-PAYE','ACCOMPTE','PAYE']}, {name: 'fkclient', type: 'int', label: 'Client', foreignKey: {relatedTable: 'client', displayField: 'name_complet', valueField: 'id'}}, {name: 'fkadresse', type: 'int', label: 'Adresse', foreignKey: {relatedTable: 'adresse', displayField: 'libelle_kasokoo', valueField: 'id'}}, {name: 'total_cmd', type: 'float', label: 'Total'}, {name: 'frais_livraison', type: 'float', label: 'Frais Livraison'}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['CDF','USD']}, {name: 'code', type: 'text', label: 'Code'}, {name: 'libelle', type: 'text', label: 'Libellé'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['achat', { tablename: 'achat', displayname: 'Achats', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false}, {name: 'delivered_at', type: 'text', label: 'Livré le', editable: false}, {name: 'status_payement', type: 'enum', label: 'Statut Paiement', isEnum: true, enumValues: ['NO-PAYE','ACCOMPTE','PAYE']}, {name: 'fkagent', type: 'int', label: 'Agent', foreignKey: {relatedTable: 'agent', displayField: 'name_complet', valueField: 'id'}}, {name: 'total_achat', type: 'float', label: 'Total Achat'}, {name: 'frais_logistique', type: 'float', label: 'Frais Logistique'}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['CDF','USD']}, {name: 'code_cmd', type: 'text', label: 'Code CMD'}, {name: 'code_achat', type: 'text', label: 'Code Achat'}, {name: 'libelle_cmd', type: 'text', label: 'Libellé CMD'}, {name: 'libelle_achat', type: 'text', label: 'Libellé Achat'}, {name: 'status_cmd', type: 'enum', label: 'Statut CMD', isEnum: true, enumValues: ['RECU','STOCKAGE','ATTENTE','VALIDE']}, {name: 'fkfournisseur', type: 'int', label: 'Fournisseur', foreignKey: {relatedTable: 'fournisseur', displayField: 'denomination', valueField: 'id'}} ] }],
            ['adresse', { tablename: 'adresse', displayname: 'Adresses', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkuser_create', type: 'int', label: 'Créé par', foreignKey: {relatedTable: 'user', displayField: 'username', valueField: 'id'}}, {name: 'fkuser_validate', type: 'int', label: 'Validé par', foreignKey: {relatedTable: 'user', displayField: 'username', valueField: 'id'}}, {name: 'longitude', type: 'float', label: 'Longitude'}, {name: 'latitude', type: 'float', label: 'Latitude'}, {name: 'is_registred', type: 'enum', label: 'Enregistrée', isEnum: true, enumValues: ['TRUE','FALSE']}, {name: 'code_OLC', type: 'text', label: 'Code OLC'}, {name: 'numero_rue', type: 'text', label: 'N° Rue'}, {name: 'description_batiment', type: 'text', label: 'Description Bâtiment'}, {name: 'libelle_client', type: 'text', label: 'Libellé Client'}, {name: 'libelle_kasokoo', type: 'text', label: 'Libellé Kasokoo'}, {name: 'avenue', type: 'text', label: 'Avenue'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['agent', { tablename: 'agent', displayname: 'Agents', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'name_complet', type: 'text', label: 'Nom Complet'}, {name: 'fonction', type: 'enum', label: 'Fonction', isEnum: true, enumValues: ['LIVREUR','ADMIN','LOGISTICIEN']}, {name: 'phone', type: 'text', label: 'Téléphone'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['caisse', { tablename: 'caisse', displayname: 'Caisses', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'designation', type: 'text', label: 'Désignation'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['categorie_prod', { tablename: 'categorie_prod', displayname: 'Catégories Produits', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'designation', type: 'text', label: 'Désignation'}, {name: 'description', type: 'text', label: 'Description'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['compte', { tablename: 'compte', displayname: 'Comptes', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['CDF','USD']}, {name: 'intutile', type: 'text', label: 'Intitulé'}, {name: 'type_compte', type: 'enum', label: 'Type de Compte', isEnum: true, enumValues: ['COMPTE_DE_GESTION','COMPTE_CLIENT','COMPTE_FOURNISSEUR']}, {name: 'fkclient', type: 'int', label: 'Client', foreignKey: {relatedTable: 'client', displayField: 'name_complet', valueField: 'id'}}, {name: 'fkfournisseur', type: 'int', label: 'Fournisseur', foreignKey: {relatedTable: 'fournisseur', displayField: 'denomination', valueField: 'id'}}, {name: 'fkcaisse', type: 'int', label: 'Caisse', foreignKey: {relatedTable: 'caisse', displayField: 'designation', valueField: 'id'}}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['droits', { tablename: 'droits', displayname: 'Droits', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'name', type: 'text', label: 'Nom'}, {name: 'code', type: 'text', label: 'Code'}, {name: 'fkmodule', type: 'int', label: 'Module', foreignKey: {relatedTable: 'module', displayField: 'name', valueField: 'id'}}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['droits_agent', { tablename: 'droits_agent', displayname: 'Droits Agents', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['ACTIF','INACTIF']}, {name: 'fkagent', type: 'int', label: 'Agent', foreignKey: {relatedTable: 'agent', displayField: 'name_complet', valueField: 'id'}}, {name: 'fkdroit', type: 'int', label: 'Droit', foreignKey: {relatedTable: 'droits', displayField: 'name', valueField: 'id'}}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['fournisseur', { tablename: 'fournisseur', displayname: 'Fournisseurs', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'denomination', type: 'text', label: 'Dénomination'}, {name: 'adresse', type: 'text', label: 'Adresse'}, {name: 'phone', type: 'text', label: 'Téléphone'}, {name: 'email', type: 'text', label: 'Email'}, {name: 'fkadresse', type: 'int', label: 'Adresse FK', foreignKey: {relatedTable: 'adresse', displayField: 'libelle_kasokoo', valueField: 'id'}}, {name: 'forme_juridique', type: 'enum', label: 'Forme Juridique', isEnum: true, enumValues: ['PERSONNE PHYSIQUE','PERSONNE MORALE']}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['information_paiement', { tablename: 'information_paiement', displayname: 'Infos Paiement', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'operateur', type: 'enum', label: 'Opérateur', isEnum: true, enumValues: ['MPESA','ORANGE_MONEY','AIRTEL_MONEY']}, {name: 'numero_compte', type: 'text', label: 'N° Compte'}, {name: 'intutile_compte', type: 'text', label: 'Intitulé Compte'}, {name: 'banque', type: 'text', label: 'Banque'}, {name: 'fkclient', type: 'int', label: 'Client', foreignKey: {relatedTable: 'client', displayField: 'name_complet', valueField: 'id'}}, {name: 'fkcaisse', type: 'int', label: 'Caisse', foreignKey: {relatedTable: 'caisse', displayField: 'designation', valueField: 'id'}}, {name: 'fkfournisseur', type: 'int', label: 'Fournisseur', foreignKey: {relatedTable: 'fournisseur', displayField: 'denomination', valueField: 'id'}}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['ligne_achat', { tablename: 'ligne_achat', displayname: 'Lignes Achat', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkproduit', type: 'int', label: 'Produit', foreignKey: {relatedTable: 'produit', displayField: 'designation', valueField: 'id'}}, {name: 'fkfournisseur', type: 'int', label: 'Fournisseur', foreignKey: {relatedTable: 'fournisseur', displayField: 'denomination', valueField: 'id'}}, {name: 'fkachat', type: 'int', label: 'Achat', foreignKey: {relatedTable: 'achat', displayField: 'code_achat', valueField: 'id'}}, {name: 'quantite', type: 'float', label: 'Quantité'}, {name: 'montant', type: 'float', label: 'Montant'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['ligne_commande', { tablename: 'ligne_commande', displayname: 'Lignes Commande', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkproduit', type: 'int', label: 'Produit', foreignKey: {relatedTable: 'produit', displayField: 'designation', valueField: 'id'}}, {name: 'fkcommande', type: 'int', label: 'Commande', foreignKey: {relatedTable: 'commande', displayField: 'code', valueField: 'id'}}, {name: 'quantite', type: 'float', label: 'Quantité'}, {name: 'montant', type: 'float', label: 'Montant'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['ligne_operation', { tablename: 'ligne_operation', displayname: 'Lignes Opération', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkcompte', type: 'int', label: 'Compte', foreignKey: {relatedTable: 'compte', displayField: 'intutile', valueField: 'id'}}, {name: 'fkoperation', type: 'int', label: 'Opération', foreignKey: {relatedTable: 'operation', displayField: 'libelle', valueField: 'id'}}, {name: 'fkinfo_paiement', type: 'int', label: 'Info Paiement', foreignKey: {relatedTable: 'information_paiement', displayField: 'numero_compte', valueField: 'id'}}, {name: 'fkuser_create', type: 'int', label: 'Créé par', foreignKey: {relatedTable: 'user', displayField: 'username', valueField: 'id'}}, {name: 'operation', type: 'enum', label: 'Opération', isEnum: true, enumValues: ['DEBIT','CREDIT']}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['livraison', { tablename: 'livraison', displayname: 'Livraisons', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkcommande', type: 'int', label: 'Commande', foreignKey: {relatedTable: 'commande', displayField: 'code', valueField: 'id'}}, {name: 'fkagent', type: 'int', label: 'Agent', foreignKey: {relatedTable: 'agent', displayField: 'name_complet', valueField: 'id'}}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false}, {name: 'delivered_at', type: 'text', label: 'Livré le', editable: false}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['ENCOURS','LIVRE','NON_LIVRE']} ] }],
            ['message', { tablename: 'message', displayname: 'Messages', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkuser', type: 'int', label: 'Utilisateur', foreignKey: {relatedTable: 'user', displayField: 'username', valueField: 'id'}}, {name: 'fkuser_destinataire', type: 'int', label: 'Destinataire', foreignKey: {relatedTable: 'user', displayField: 'username', valueField: 'id'}}, {name: 'fkmessage_prec', type: 'int', label: 'Msg Préc.', foreignKey: {relatedTable: 'message', displayField: 'id', valueField: 'id'}}, {name: 'isread', type: 'enum', label: 'Lu', isEnum: true, enumValues: ['TRUE','FALSE']}, {name: 'media', type: 'text', label: 'Média'}, {name: 'media_type', type: 'enum', label: 'Type Média', isEnum: true, enumValues: ['IMAGE','AUDIO','VIDEO','DOC']}, {name: 'corps_message', type: 'text', label: 'Message'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['module', { tablename: 'module', displayname: 'Modules', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'name', type: 'text', label: 'Nom'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['operation', { tablename: 'operation', displayname: 'Opérations', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkuser_create', type: 'int', label: 'Créé par', foreignKey: {relatedTable: 'user', displayField: 'username', valueField: 'id'}}, {name: 'libelle', type: 'text', label: 'Libellé'}, {name: 'type_operation', type: 'enum', label: 'Type Opération', isEnum: true, enumValues: ['DEPOT_CLIENT','RETRAIT_CLIENT','PAIEMENT_CMD','ACHAT_STOCK','CONVERSION_DEVISE','CREDIT_ACCORDE','DETTE_CONTRACTEE','AUTRE']}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['operation_achat', { tablename: 'operation_achat', displayname: 'Opérations Achat', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkachat', type: 'int', label: 'Achat', foreignKey: {relatedTable: 'achat', displayField: 'code_achat', valueField: 'id'}}, {name: 'fkoperation', type: 'int', label: 'Opération', foreignKey: {relatedTable: 'operation', displayField: 'libelle', valueField: 'id'}}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['operation_commande', { tablename: 'operation_commande', displayname: 'Opérations Commande', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkcommande', type: 'int', label: 'Commande', foreignKey: {relatedTable: 'commande', displayField: 'code', valueField: 'id'}}, {name: 'fkoperation', type: 'int', label: 'Opération', foreignKey: {relatedTable: 'operation', displayField: 'libelle', valueField: 'id'}}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['otp', { tablename: 'otp', displayname: 'Codes OTP', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'value', type: 'int', label: 'Valeur'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false}, {name: 'expire_at', type: 'text', label: 'Expire le', editable: false}, {name: 'sent_to', type: 'text', label: 'Envoyé à'}, {name: 'fkuser', type: 'int', label: 'Utilisateur', foreignKey: {relatedTable: 'user', displayField: 'username', valueField: 'id'}}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['SENT','CREATED','EXPIRED','CHECKED']} ] }],
            ['otp_sender_device', { tablename: 'otp_sender_device', displayname: 'Appareils Envoi OTP', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'token', type: 'text', label: 'Jeton'}, {name: 'numero_sim', type: 'text', label: 'N° SIM'}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['ACTIF','DESACTIVE','OCCUPE']}, {name: 'reseau', type: 'enum', label: 'Réseau', isEnum: true, enumValues: ['ORANGE','AIRTEL','VODACOM','TOUS']}, {name: 'sms_sent_at', type: 'text', label: 'SMS Envoyé le', editable: false}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['parametre', { tablename: 'parametre', displayname: 'Paramètres', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['CDF','USD']}, {name: 'taux_change', type: 'float', label: 'Taux Change'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false}, {name: 'adresse', type: 'text', label: 'Adresse'}, {name: 'phone', type: 'text', label: 'Téléphone'}, {name: 'email', type: 'text', label: 'Email'}, {name: 'logo', type: 'text', label: 'Logo'}, {name: 'app_version', type: 'text', label: 'Version App'} ] }],
            ['publicite', { tablename: 'publicite', displayname: 'Publicités', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkproduit', type: 'int', label: 'Produit', foreignKey: {relatedTable: 'produit', displayField: 'designation', valueField: 'id'}}, {name: 'image', type: 'text', label: 'Image'}, {name: 'corps', type: 'text', label: 'Corps'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['transaction_cinetpay', { tablename: 'transaction_cinetpay', displayname: 'Trans. CinetPay', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkclient', type: 'int', label: 'Client', foreignKey: {relatedTable: 'client', displayField: 'name_complet', valueField: 'id'}}, {name: 'montant', type: 'float', label: 'Montant'}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['CDF','USD']}, {name: 'data_json', type: 'text', label: 'Données JSON', editable: false}, {name: 'status', type: 'text', label: 'Statut'}, {name: 'numero', type: 'text', label: 'Numéro'}, {name: 'transaction_id', type: 'text', label: 'ID Trans.'}, {name: 'transaction_token', type: 'text', label: 'Jeton Trans.'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['user_device', { tablename: 'user_device', displayname: 'Appareils Utilisateurs', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'uuid', type: 'text', label: 'UUID'}, {name: 'fcm_token', type: 'text', label: 'Jeton FCM', editable: false}, {name: 'device_info', type: 'text', label: 'Infos Appareil'}, {name: 'fkuser', type: 'int', label: 'Utilisateur', foreignKey: {relatedTable: 'user', displayField: 'username', valueField: 'id'}}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['zone_couverture', { tablename: 'zone_couverture', displayname: 'Zones de Couverture', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['ACTIF','INACTIF']}, {name: 'designation', type: 'text', label: 'Désignation'}, {name: 'frontieres', type: 'text', label: 'Frontières'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
        ]);

        const App = {
            setup() {
                const tables = ref(Array.from(tableMetadata.keys()).sort());
                const notifications = ref([]);
                const removeNotification = (id) => notifications.value = notifications.value.filter(n => n.id !== id);
                
                window.addNotification = (message, type = 'success') => {
                    const id = Date.now();
                    const toastContainer = document.querySelector('.toast-container');
                    const toastHTML = `
                        <div id="toast-${id}" class="toast align-items-center text-white border-0 ${type === 'success' ? 'bg-success' : 'bg-danger'}" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body">${message}</div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>`;
                    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
                    const toastEl = document.getElementById(`toast-${id}`);
                    const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
                    toast.show();
                    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
                };
                
                const formatTableName = (name) => (tableMetadata.get(name) || { displayname: name.replace(/_/g, ' ') }).displayname;
                return { tables, notifications, formatTableName, removeNotification };
            }
        };

        const TableComponent = {
            template: '#table-component-template',
            props: ['tableName'],
            setup(props) {
                const data = ref([]);
                const loading = ref(true);
                const error = ref(null);
                const searchQuery = ref('');
                const sortKey = ref('');
                const sortOrder = ref('asc');
                const editing = ref({ id: null, field: null, value: null });
                const newRecord = ref({});
                const relatedData = reactive({});
                const managedFields = ref([]);
                let columnSettingsModalInstance = null;

                const currentTableMeta = computed(() => tableMetadata.get(props.tableName) || { tablename: props.tableName, displayname: props.tableName.replace(/_/g, ' '), fields: [] });
                const displayedFields = computed(() => managedFields.value.filter(f => f.visible));

                const setupColumns = () => {
                    const baseFields = currentTableMeta.value.fields;
                    if (!baseFields || baseFields.length === 0) { managedFields.value = []; return; }
                    const savedSettings = JSON.parse(localStorage.getItem(`table-settings-${props.tableName}`));
                    
                    let fields = baseFields.map(field => ({ ...field, visible: savedSettings ? (savedSettings.find(s => s.name === field.name) || {visible:false}).visible : (field.editable !== false || field.name === 'id') }));
                    
                    if (savedSettings) {
                        fields.sort((a, b) => {
                            const orderA = savedSettings.findIndex(s => s.name === a.name);
                            const orderB = savedSettings.findIndex(s => s.name === b.name);
                            return (orderA === -1 ? Infinity : orderA) - (orderB === -1 ? Infinity : orderB);
                        });
                    }
                    managedFields.value = fields;
                };

                const moveColumn = (index, direction) => {
                    const newIndex = index + direction;
                    if (newIndex < 0 || newIndex >= managedFields.value.length) return;
                    [managedFields.value[index], managedFields.value[newIndex]] = [managedFields.value[newIndex], managedFields.value[index]];
                };

                const saveColumnSettings = () => {
                    const settings = managedFields.value.map(({ name, visible }) => ({ name, visible }));
                    localStorage.setItem(`table-settings-${props.tableName}`, JSON.stringify(settings));
                    columnSettingsModalInstance.hide();
                    window.addNotification('Préférences de colonnes enregistrées.', 'success');
                };

                const openColumnSettingsModal = () => columnSettingsModalInstance.show();

                const apiCall = async (endpoint, method = 'GET', body = null) => {
                    try {
                        const options = { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' } };
                        if (body) options.body = JSON.stringify(body);
                        const response = await fetch(`${API_BASE_URL}/${endpoint.replace(/_/g,'')}`, options);
                        if (!response.ok) {
                            const errorBody = await response.json().catch(() => ({ message: response.statusText }));
                            throw new Error(errorBody.message || `Erreur ${response.status}`);
                        }
                        return response.status === 204 ? null : response.json();
                    } catch (e) {
                        window.addNotification(`Erreur API: ${e.message}`, 'danger');
                        throw e;
                    }
                };
                
                const fetchData = async () => {
                    try {
                        const result = await apiCall(props.tableName);
                        const dataKey = props.tableName.replace(/_/g,'') + 's';
                        if (result && Array.isArray(result[dataKey])) {
                            data.value = result[dataKey];
                        } else if (Array.isArray(result)) { // Fallback if API returns a direct array
                            data.value = result;
                        } else {
                            data.value = [];
                            console.warn(`Aucune donnée trouvée pour la clé '${dataKey}' ou en tant que tableau direct.`);
                        }
                        setupColumns(); // Always setup columns after fetch
                    } catch (e) { error.value = `Impossible de charger les données: ${e.message}`; }
                };

                const fetchRelatedData = async () => {
                    const tablesToFetch = new Set(currentTableMeta.value.fields.filter(f => f.foreignKey).map(f => f.foreignKey.relatedTable));
                    for (const table of tablesToFetch) {
                        try {
                            const result = await apiCall(table);
                            const dataKey = table.replace(/_/g,'') + 's';
                            relatedData[table] = (result && Array.isArray(result[dataKey])) ? result[dataKey] : (Array.isArray(result) ? result : []);
                        } catch (e) { console.error(`Echec du chargement des données associées pour ${table}:`, e); }
                    }
                };
                
                const addRecord = async () => {
                    try {
                        const payload = { ...newRecord.value };
                        Object.keys(payload).forEach(key => (payload[key] === undefined || payload[key] === '') && delete payload[key]);
                        if (Object.keys(payload).length === 0) return window.addNotification('Veuillez remplir au moins un champ.', 'danger');

                        const added = await apiCall(props.tableName, 'POST', payload);
                        data.value.push(added);
                        newRecord.value = {};
                        window.addNotification('Enregistrement ajouté!', 'success');
                    } catch (e) { /* notification handled by apiCall */ }
                };

                const deleteRecord = async (id) => {
                    if (!confirm('Voulez-vous vraiment supprimer cet enregistrement ?')) return;
                    try {
                        await apiCall(`${props.tableName}/${id}`, 'DELETE');
                        data.value = data.value.filter(item => item.id !== id);
                        window.addNotification('Enregistrement supprimé.', 'success');
                    } catch (e) { /* notification handled by apiCall */ }
                };

                const editCell = (item, field) => {
                    if (field.editable === false) return window.addNotification(`Le champ '${field.label}' n'est pas modifiable.`, 'danger');
                    editing.value = { id: item.id, field: field.name, value: item[field.name] };
                };

                const saveCell = async (item, fieldName) => {
                    if (editing.value.id === null) return;
                    const originalValue = item[fieldName];
                    let newValue = editing.value.value;
                    
                    if (originalValue == newValue) {
                        cancelEdit();
                        return;
                    }

                    // Find the index of the item in the data array
                    const itemIndex = data.value.findIndex(d => d.id === item.id);
                    if (itemIndex === -1) {
                        cancelEdit();
                        return;
                    }
                    // Get a reference to the actual reactive item
                    const currentItemRef = data.value[itemIndex];
                    // Store the current state of the item for potential rollback
                    const oldItemState = { ...currentItemRef }; // Shallow copy

                    // Optimistic update: Apply the new value locally immediately
                    currentItemRef[fieldName] = newValue;

                    try {
                        const payload = { [fieldName]: newValue };
                        const updatedItemFromServer = await apiCall(`${props.tableName}/${item.id}`, 'PUT', payload);
                        
                        // If the server returns a full updated item, use it to ensure consistency
                        // This will also update any server-generated fields like 'updated_at'
                        if (updatedItemFromServer) {
                            Object.assign(currentItemRef, updatedItemFromServer);
                        }
                        window.addNotification('Cellule mise à jour.', 'success');
                    } catch (e) {
                        // Rollback to original state on error
                        Object.assign(currentItemRef, oldItemState);
                        // The apiCall function already handles displaying the error notification
                    } finally {
                        cancelEdit();
                    }
                };

                const cancelEdit = () => editing.value = { id: null, field: null, value: null };

                const sortBy = (key) => {
                    sortOrder.value = sortKey.value === key ? (sortOrder.value === 'asc' ? 'desc' : 'asc') : 'asc';
                    sortKey.value = key;
                };

                const filteredData = computed(() => {
                    let filtered = [...data.value];
                    if (searchQuery.value) {
                        const lowerQuery = searchQuery.value.toLowerCase();
                        filtered = filtered.filter(item => Object.values(item).some(val => String(val).toLowerCase().includes(lowerQuery)));
                    }
                    if (sortKey.value) {
                        filtered.sort((a, b) => {
                            const valA = a[sortKey.value], valB = b[sortKey.value];
                            if (valA == null) return 1; if (valB == null) return -1;
                            if (!isNaN(Number(valA)) && !isNaN(Number(valB))) return (Number(valA) - Number(valB)) * (sortOrder.value === 'asc' ? 1 : -1);
                            return String(valA).localeCompare(String(valB)) * (sortOrder.value === 'asc' ? 1 : -1);
                        });
                    }
                    return filtered;
                });

                const refreshData = () => {
                    loading.value = true;
                    error.value = null;
                    Promise.all([fetchData(), fetchRelatedData()]).finally(() => loading.value = false);
                };

                onMounted(() => {
                    const modalEl = document.getElementById('columnSettingsModal');
                    if (modalEl) columnSettingsModalInstance = new bootstrap.Modal(modalEl);
                    refreshData();
                });

                watch(() => props.tableName, refreshData);

                return { data, loading, error, searchQuery, sortKey, sortOrder, sortBy, filteredData, deleteRecord, addRecord, editing, editCell, saveCell, cancelEdit, newRecord, relatedData, currentTableMeta, refreshData, managedFields, displayedFields, openColumnSettingsModal, saveColumnSettings, moveColumn };
            },
            directives: { focus: { mounted(el) { nextTick(() => el.focus()); } } }
        };

        const router = createRouter({ history: createWebHashHistory(), routes: [ { path: '/', component: { template: '<div class="text-center mt-5"><h2>Bienvenue !</h2><p>Veuillez sélectionner une table dans le menu de gauche.</p></div>' } }, { path: '/table/:tableName', component: TableComponent, props: true } ] });
        const app = createApp(App);
        app.use(router);
        app.mount('#app');
    </script>
</body>
</html>
