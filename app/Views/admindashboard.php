<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management Dashboard</title>
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
            background-color: #f0f2f5;
        }
        
        #navbar-top {
            position: fixed; 
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #ffffff;
            padding: 20px;
            border-right: 1px solid #dee2e6;
            box-shadow: 2px 0 5px rgba(0,0,0,.1);
            overflow-y: auto;
            z-index: 1050;
        }
        #navbar-top .nav { flex-direction: column; }
        #navbar-top .nav-link { 
            color: #495057; 
            margin-bottom: 5px;
            border-radius: .25rem;
        }
        #navbar-top .nav-link.router-link-active, #navbar-top .nav-link:hover {
            font-weight: bold;
            color: #0d6efd;
            background-color: #e9ecef;
        }

        #main-content { 
            flex-grow: 1; 
            padding: 20px; 
            margin-left: 0;
            transition: margin-left .3s;
        }
        
        body.navbar-visible #main-content {
            margin-left: 250px;
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
        .card {
             border: none;
             box-shadow: 0 0 15px rgba(0,0,0,.05);
        }
    </style>
</head>
<body>

    <div id="app">
        <nav id="navbar-top" v-if="isLoggedIn">
            <h5 class="text-primary">Tableaux de Bord</h5>
            <ul class="nav">
                 <li v-if="hasPermission('user.view')" class="nav-item">
                     <router-link to="/users" class="nav-link"><i class="bi bi-people-fill me-2"></i>Gérer les Utilisateurs</router-link>
                </li>
                 <li v-if="hasPermission('payment.view_daily')" class="nav-item">
                     <router-link to="/daily-payments" class="nav-link"><i class="bi bi-calendar-day me-2"></i>Paiements du Jour</router-link>
                </li>
                 <li v-if="hasPermission('reports.generate')" class="nav-item">
                     <router-link to="/reports" class="nav-link"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Générer Rapports</router-link>
                </li>
            </ul>
            <hr>
            <h5>Données de Base</h5>
            <ul class="nav">
                <li v-for="table in tables" :key="table" class="nav-item">
                     <router-link v-if="hasPermission(table + '.view')" :to="'/table/' + table" class="nav-link">
                        {{ formatTableName(table) }}
                    </router-link>
                </li>
            </ul>
            <hr>
            <button class="btn btn-outline-danger w-100" @click="logout">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </button>
        </nav>

        <main id="main-content">
            <router-view :key="$route.fullPath"></router-view>
        </main>
        
        <div class="toast-container notification-toast"></div>
    </div>

    <script type="text/x-template" id="login-component-template">
        <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="card shadow-lg p-4" style="width: 100%; max-width: 400px;">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Connexion Admin</h2>
                    <form @submit.prevent="handleLogin">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur ou Email</label>
                            <input type="text" class="form-control" id="username" v-model="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" v-model="password" required>
                        </div>
                        <div v-if="loginError" class="alert alert-danger mt-3">{{ loginError }}</div>
                        <button type="submit" class="btn btn-primary w-100 mt-3" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Connexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </script>
    
    <script type="text/x-template" id="daily-payments-template">
         <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Paiements du Jour ({{ new Date().toLocaleDateString() }})</h4>
                    <button class="btn btn-primary btn-sm" @click="fetchPayments" :disabled="loading" title="Actualiser">
                        <i class="bi bi-arrow-clockwise" :class="{'loading-spinner': loading}"></i> Actualiser
                    </button>
                </div>
                <div class="card-body">
                    <div v-if="error" class="alert alert-danger">{{ error }}</div>
                    <div v-if="loading" class="text-center p-5"><div class="spinner-border" role="status"></div></div>
                    <div v-else-if="payments.length === 0" class="alert alert-info">Aucun paiement enregistré aujourd'hui.</div>
                    <div v-else class="table-responsive">
                        <table class="table table-striped table-hover">
                             <thead class="table-dark">
                                <tr>
                                    <th>Élève</th>
                                    <th>Classe</th>
                                    <th>Frais Payé</th>
                                    <th class="text-end">Montant</th>
                                    <th>Perçu par</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment in payments" :key="payment.id">
                                    <td>{{ getRelatedName(payment.fk_eleve, 'eleve') }}</td>
                                    <td>{{ getRelatedName(payment.fk_classe, 'classe') }}</td>
                                    <td>{{ getRelatedName(payment.fk_frais, 'frais') }}</td>
                                    <td class="text-end">{{ formatCurrency(payment.montant, payment.devise) }}</td>
                                    <td>{{ getRelatedName(payment.fk_user, 'user') }}</td>
                                </tr>
                            </tbody>
                             <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total USD:</th>
                                    <td class="text-end fw-bold">{{ formatCurrency(totalUSD, 'USD') }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Total FC:</th>
                                    <td class="text-end fw-bold">{{ formatCurrency(totalFC, 'FC') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="reports-template">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header"><h4 class="mb-0">Génération de Rapports</h4></div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>Cette section est en cours de développement.
                    </div>
                    <p>Ici, vous pourrez bientôt générer divers rapports, tels que :</p>
                    <ul>
                        <li>Liste des élèves par classe</li>
                        <li>Situation des paiements par élève</li>
                        <li>Rapport financier par période</li>
                    </ul>
                </div>
            </div>
        </div>
    </script>

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
                                    <tr class="add-row" v-if="hasPermission(tableName + '.create')">
                                        <td v-for="field in displayedFields" :key="'new-' + field.name">
                                            <template v-if="field.editable !== false">
                                                <select v-if="field.isEnum" v-model="newRecord[field.name]" class="form-select form-select-sm">
                                                    <option :value="undefined">-- Sélectionner --</option>
                                                    <option v-for="enumValue in field.enumValues" :value="enumValue">{{ enumValue }}</option>
                                                </select>
                                                <select v-else-if="field.foreignKey" v-model="newRecord[field.name]" class="form-select form-select-sm">
                                                    <option :value="undefined">-- Sélectionner --</option>
                                                    <option v-for="item in relatedData[field.foreignKey.relatedTable]" :value="item[field.foreignKey.valueField]">{{ item[field.foreignKey.displayField] }}</option>
                                                </select>
                                                <input v-else :type="field.type === 'int' || field.type === 'decimal' ? 'number' : (field.type === 'date' ? 'date' : 'text')" v-model="newRecord[field.name]" class="form-control form-control-sm" />
                                            </template>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm" @click="addRecord"><i class="bi bi-plus-circle"></i> Ajouter</button>
                                        </td>
                                    </tr>
                                    <tr v-for="item in filteredData" :key="item.id">
                                        <td v-for="field in displayedFields" :key="field.name" @dblclick="editCell(item, field)">
                                            <div v-if="editing.id === item.id && editing.field === field.name">
                                                <select v-if="field.isEnum" v-model="editing.value" @blur="saveCell(item, field.name)" @keyup.enter="saveCell(item, field.name)" @keyup.esc="cancelEdit" v-focus class="form-select form-select-sm">
                                                    <option v-for="enumValue in field.enumValues" :value="enumValue">{{ enumValue }}</option>
                                                </select>
                                                <select v-else-if="field.foreignKey" v-model="editing.value" @blur="saveCell(item, field.name)" @keyup.enter="saveCell(item, field.name)" @keyup.esc="cancelEdit" v-focus class="form-select form-select-sm">
                                                     <option v-for="relatedItem in relatedData[field.foreignKey.relatedTable]" :value="relatedItem[field.foreignKey.valueField]">{{ relatedItem[field.foreignKey.displayField] }}</option>
                                                </select>
                                                <input v-else :type="field.type === 'int' || field.type === 'decimal' ? 'number' : (field.type === 'date' ? 'date' : 'text')" v-model="editing.value" @blur="saveCell(item, field.name)" @keyup.enter="saveCell(item, field.name)" @keyup.esc="cancelEdit" v-focus class="form-control form-control-sm"/>
                                            </div>
                                            <div v-else>
                                                <span v-if="field.foreignKey && relatedData[field.foreignKey.relatedTable]">
                                                    {{ getRelatedDisplay(item, field) || item[field.name] }}
                                                </span>
                                                <span v-else-if="field.type === 'tinyint'">{{ item[field.name] == 1 ? 'Oui' : 'Non' }}</span>
                                                <span v-else>{{ item[field.name] }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm" @click="deleteRecord(item.id)" v-if="hasPermission(tableName + '.delete')"><i class="bi bi-trash"></i></button>
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
        const API_BASE_URL = '<?php echo base_url(); // Replace with your actual API base URL ?>'; 
        const { createApp, ref, reactive, computed, onMounted, watch, nextTick } = Vue;
        const { createRouter, createWebHashHistory } = VueRouter;

        // Global state for authentication
        const authState = reactive({
            isLoggedIn: !!localStorage.getItem('access_token'),
            accessToken: localStorage.getItem('access_token') || null,
            userId: localStorage.getItem('user_id') || null,
            userPermissions: JSON.parse(localStorage.getItem('user_permissions')) || []
        });

        // Define addNotification globally BEFORE apiCall
        window.addNotification = (message, type = 'success') => {
            const id = Date.now();
            const toastContainer = document.querySelector('.toast-container');
            const toastHTML = `<div id="toast-${id}" class="toast align-items-center text-white border-0 ${type === 'success' ? 'bg-success' : 'bg-danger'}" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>`;
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            const toastEl = document.getElementById(`toast-${id}`);
            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        };

        // Global apiCall function
        const apiCall = async (endpoint, method = 'GET', body = null) => {
            try {
                const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
                if (authState.accessToken) {
                    headers['Authorization'] = `Bearer ${authState.accessToken}`;
                }
                const options = { method, headers };
                if (body) options.body = JSON.stringify(body);
                const fullUrl = `${API_BASE_URL}/${endpoint}`;
                const response = await fetch(fullUrl, options);
                
                if (response.status === 401 || response.status === 403) {
                    window.addNotification('Session expirée ou non autorisée.', 'danger');
                    // Perform logout
                    localStorage.clear();
                    Object.assign(authState, { isLoggedIn: false, accessToken: null, userId: null, userPermissions: [] });
                    router.push('/login');
                    return null;
                }

                const responseData = await response.json();
                
                if (!response.ok) {
                    throw new Error(responseData.message || `Erreur ${response.status}`);
                }

                // IMPORTANT: Extract data from the "result" key as requested
                return responseData.result || responseData;

            } catch (e) {
                window.addNotification(`Erreur API: ${e.message}`, 'danger');
                throw e;
            }
        };

        // Metadata based on ecole_elie.sql schema
        const tableMetadata = new Map([
            ['user', { tablename: 'user', displayname: 'Utilisateurs', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'username', type: 'text', label: 'Nom d\'utilisateur'}, {name: 'email', type: 'text', label: 'Email'}, {name: 'nom_complet', type: 'text', label: 'Nom Complet'}, {name: 'password', type: 'text', label: 'Mot de passe', editable: false}, {name: 'est_actif', type: 'tinyint', label: 'Actif'}, {name: 'date_creation', type: 'text', label: 'Créé le', editable: false} ] }],
            ['eleve', { tablename: 'eleve', displayname: 'Élèves', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom'}, {name: 'postnom', type: 'text', label: 'Postnom'}, {name: 'prenom', type: 'text', label: 'Prénom'}, {name: 'date_naissance', type: 'date', label: 'Date Naissance'}, {name: 'genre', type: 'enum', label: 'Genre', isEnum: true, enumValues: ['M','F']}, {name: 'adresse', type: 'text', label: 'Adresse'}, {name: 'telephone_parent', type: 'text', label: 'Tél. Parent'}, {name: 'date_inscription', type: 'date', label: 'Inscrit le'}, {name: 'est_actif', type: 'tinyint', label: 'Actif'} ] }],
            ['paiement', { tablename: 'paiement', displayname: 'Paiements', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'date_paiement', type: 'date', label: 'Date Paiement'}, {name: 'montant', type: 'decimal', label: 'Montant'}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['FC','USD']}, {name: 'fk_eleve', type: 'int', label: 'Élève', foreignKey: {relatedTable: 'eleve', displayField: 'nom', valueField: 'id'}}, {name: 'fk_annee', type: 'int', label: 'Année Scolaire', foreignKey: {relatedTable: 'annee_scolaire', displayField: 'nom', valueField: 'id'}}, {name: 'fk_classe', type: 'int', label: 'Classe', foreignKey: {relatedTable: 'classe', displayField: 'nom', valueField: 'id'}}, {name: 'fk_user', type: 'int', label: 'Utilisateur', foreignKey: {relatedTable: 'user', displayField: 'nom_complet', valueField: 'id'}}, {name: 'fk_frais', type: 'int', label: 'Frais', foreignKey: {relatedTable: 'frais', displayField: 'nom', valueField: 'id'}} ] }],
            ['classe', { tablename: 'classe', displayname: 'Classes', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom'} ] }],
            ['annee_scolaire', { tablename: 'annee_scolaire', displayname: 'Années Scolaires', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom (ex: 2023-2024)'} ] }],
            ['frais', { tablename: 'frais', displayname: 'Types de Frais', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom du frais'} ] }],
            ['depense', { tablename: 'depense', displayname: 'Dépenses', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'date_depense', type: 'date', label: 'Date Dépense'}, {name: 'montant', type: 'decimal', label: 'Montant'}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['FC','USD']}, {name: 'motif', type: 'text', label: 'Motif'}, {name: 'fk_user', type: 'int', label: 'Utilisateur', foreignKey: {relatedTable: 'user', displayField: 'nom_complet', valueField: 'id'}} ] }],
        ]);


        const Login = {
            template: '#login-component-template',
            setup() {
                const username = ref('');
                const password = ref('');
                const loading = ref(false);
                const loginError = ref(null);
                
                const fetchPermissions = async (userId) => {
                    const endpoint = 'search';
                    const payload = {
                        table: 'user_permission',
                        where: { fk_user: userId }
                    };
                    try {
                        const userPermissions = await apiCall(endpoint, 'POST', payload);
                        const allPermissions = await apiCall('permission', 'GET');

                        permissionIds = userPermissions.map(p => p.fk)

                        for (var i = .length - 1; i >= 0; i--) {
                            [i]
                        }
                        if (permissionsResponse) {
                            const permissionCodes = permissionsResponse.map(p => p.code);
                            localStorage.setItem('user_permissions', JSON.stringify(permissionCodes));
                            authState.userPermissions = permissionCodes;
                        }
                    } catch (e) {
                        console.error('Failed to fetch permissions:', e);
                        // Optional: Handle error, e.g., set permissions to empty array
                        localStorage.setItem('user_permissions', JSON.stringify([]));
                        authState.userPermissions = [];
                    }
                };
                
                const handleLogin = async () => {
                    loading.value = true;
                    loginError.value = null;
                    try {
                        const response = await fetch(`${API_BASE_URL}/auth/login`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ username: username.value, password: password.value })
                        });
                        var data = await response.json();
                        
                        // Assuming the login response contains user and token under the "result" key
                        const result = data.result;

                        if (response.ok && result && result.id && result.token) {
                            localStorage.setItem('access_token', result.token);
                            localStorage.setItem('user_id', result.id);
                            
                            // Fetch permissions after successful login
                            await fetchPermissions(result.id);

                            Object.assign(authState, { 
                                accessToken: result.token, 
                                userId: result.id, 
                                isLoggedIn: true 
                            });

                            window.addNotification('Connexion réussie!', 'success');
                            router.push('/');
                        } else { throw new Error(data.message || 'Échec de la connexion.'); }
                    } catch (e) {
                        loginError.value = e.message;
                        window.addNotification(e.message, 'danger');
                    } finally { loading.value = false; }
                };
                return { username, password, loading, loginError, handleLogin };
            }
        };

        const App = {
            setup() {
                const tables = ref(Array.from(tableMetadata.keys()).sort());
                const formatTableName = (name) => (tableMetadata.get(name) || { displayname: name.replace(/_/g, ' ') }).displayname;
                
                const hasPermission = (permissionCode) => {
                    // For simplicity, admin (user id 1) has all rights.
                    // In production, rely purely on the permissions list.
                    if (authState.userId == 1) return true;
                    return (authState.userPermissions || []).includes(permissionCode);
                };

                const logout = async () => {
                    // No need for API call if token is just cleared locally
                    localStorage.clear();
                    Object.assign(authState, { isLoggedIn: false, accessToken: null, userId: null, userPermissions: [] });
                    router.push('/login');
                    window.addNotification('Déconnexion réussie!', 'success');
                };
                
                watch(() => authState.isLoggedIn, (loggedIn) => {
                    document.body.classList.toggle('navbar-visible', loggedIn);
                }, { immediate: true });

                return { tables, formatTableName, isLoggedIn: computed(() => authState.isLoggedIn), logout, authState, hasPermission };
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

                const hasPermission = (permissionCode) => {
                    if (authState.userId == 1) return true;
                    return (authState.userPermissions || []).includes(permissionCode);
                };

                const currentTableMeta = computed(() => tableMetadata.get(props.tableName) || { tablename: props.tableName, displayname: props.tableName.replace(/_/g, ' '), fields: [] });
                const displayedFields = computed(() => managedFields.value.filter(f => f.visible));

                const setupColumns = () => {
                    const baseFields = currentTableMeta.value.fields;
                    if (!baseFields) { managedFields.value = []; return; }
                    const savedSettings = JSON.parse(localStorage.getItem(`table-settings-${props.tableName}`));
                    let fields = baseFields.map(field => ({ ...field, visible: savedSettings ? (savedSettings.find(s => s.name === field.name) || {visible:true}).visible : (field.editable !== false || field.name === 'id' || field.name.startsWith('nom')) }));
                    if (savedSettings) { fields.sort((a, b) => (savedSettings.findIndex(s => s.name === a.name) ?? Infinity) - (savedSettings.findIndex(s => s.name === b.name) ?? Infinity)); }
                    managedFields.value = fields;
                };

                const moveColumn = (index, direction) => {
                    const newIndex = index + direction;
                    if (newIndex < 0 || newIndex >= managedFields.value.length) return;
                    [managedFields.value[index], managedFields.value[newIndex]] = [managedFields.value[newIndex], managedFields.value[index]];
                };

                const saveColumnSettings = () => {
                    localStorage.setItem(`table-settings-${props.tableName}`, JSON.stringify(managedFields.value.map(({ name, visible }) => ({ name, visible }))));
                    columnSettingsModalInstance.hide();
                    window.addNotification('Préférences enregistrées.', 'success');
                };
                const openColumnSettingsModal = () => columnSettingsModalInstance.show();

                const fetchData = async () => {
                    if (!hasPermission(props.tableName + '.view')) { error.value = 'Droits insuffisants pour voir cette table.'; loading.value = false; data.value = []; return; }
                    try {
                        const result = await apiCall(props.tableName);
                        data.value = result || [];
                        setupColumns();
                    } catch (e) {
                        error.value = `Erreur chargement: ${e.message}`;
                    } finally {
                        loading.value = false;
                    }
                };

                const fetchRelatedData = async () => {
                    const tablesToFetch = new Set(currentTableMeta.value.fields.filter(f => f.foreignKey).map(f => f.foreignKey.relatedTable));
                    for (const table of tablesToFetch) {
                        try {
                            const result = await apiCall(table);
                            relatedData[table] = result || [];
                        } catch (e) { console.error(`Erreur données associées pour ${table}:`, e); }
                    }
                };
                
                const getRelatedDisplay = (item, field) => {
                    if (!field.foreignKey || !relatedData[field.foreignKey.relatedTable]) return item[field.name];
                    const relatedItem = relatedData[field.foreignKey.relatedTable].find(r => r[field.foreignKey.valueField] == item[field.name]);
                    if (!relatedItem) return item[field.name];

                    // Special case for student full name
                    if (field.foreignKey.relatedTable === 'eleve') {
                        return `${relatedItem.nom || ''} ${relatedItem.postnom || ''} ${relatedItem.prenom || ''}`.trim();
                    }
                    return relatedItem[field.foreignKey.displayField];
                };

                const addRecord = async () => {
                    if (!hasPermission(props.tableName + '.create')) return window.addNotification('Droits insuffisants.', 'danger');
                    try {
                        const payload = { ...newRecord.value };
                        Object.keys(payload).forEach(key => (payload[key] == null || payload[key] === '') && delete payload[key]);
                        if (Object.keys(payload).length === 0) return window.addNotification('Veuillez remplir au moins un champ.', 'danger');
                        
                        const added = await apiCall(props.tableName, 'POST', payload);
                        if (added) {
                            data.value.push(added);
                            newRecord.value = {};
                            window.addNotification('Enregistré!', 'success');
                        }
                    } catch (e) { /* handled by apiCall */ }
                };

                const deleteRecord = async (id) => {
                    if (!hasPermission(props.tableName + '.delete')) return window.addNotification('Droits insuffisants.', 'danger');
                    if (!confirm('Voulez-vous vraiment supprimer cet enregistrement ?')) return;
                    try {
                        await apiCall(`${props.tableName}/${id}`, 'DELETE');
                        data.value = data.value.filter(item => item.id !== id);
                        window.addNotification('Supprimé.', 'success');
                    } catch (e) { /* handled by apiCall */ }
                };

                const editCell = (item, field) => {
                    if (field.editable === false || !hasPermission(props.tableName + '.edit')) return;
                    editing.value = { id: item.id, field: field.name, value: item[field.name] };
                };

                const saveCell = async (item, fieldName) => {
                    if (editing.value.id === null) return;
                    const originalValue = item[fieldName];
                    const newValue = editing.value.value;
                    if (originalValue == newValue) { cancelEdit(); return; }
                    
                    const itemIndex = data.value.findIndex(d => d.id === item.id);
                    if (itemIndex === -1) { cancelEdit(); return; }
                    const currentItemRef = data.value[itemIndex];
                    const oldItemState = { ...currentItemRef };
                    currentItemRef[fieldName] = newValue; // Optimistic update
                    
                    try {
                        await apiCall(`${props.tableName}/${item.id}`, 'PUT', { [fieldName]: newValue });
                        window.addNotification('Mis à jour.', 'success');
                    } catch (e) {
                        Object.assign(currentItemRef, oldItemState); // Revert on failure
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
                            if (valA == null) return 1;
                            if (valB == null) return -1;
                            return String(valA).localeCompare(String(valB), undefined, { numeric: true }) * (sortOrder.value === 'asc' ? 1 : -1);
                        });
                    }
                    return filtered;
                });

                const refreshData = () => {
                    if (authState.isLoggedIn) {
                        loading.value = true;
                        error.value = null;
                        Promise.all([fetchData(), fetchRelatedData()]).finally(() => loading.value = false);
                    } else {
                        data.value = [];
                        error.value = 'Veuillez vous connecter.';
                        loading.value = false;
                    }
                };

                onMounted(() => {
                    const modalEl = document.getElementById('columnSettingsModal');
                    if (modalEl) columnSettingsModalInstance = new bootstrap.Modal(modalEl);
                    refreshData();
                });

                watch(() => props.tableName, refreshData);
                watch([() => authState.isLoggedIn, () => authState.userPermissions], refreshData, {deep: true});

                return { data, loading, error, searchQuery, sortKey, sortOrder, sortBy, filteredData, deleteRecord, addRecord, editing, editCell, saveCell, cancelEdit, newRecord, relatedData, currentTableMeta, refreshData, managedFields, displayedFields, openColumnSettingsModal, saveColumnSettings, moveColumn, hasPermission, getRelatedDisplay };
            },
            directives: { focus: { mounted(el) { nextTick(() => el.focus()); } } }
        };

        const DailyPayments = {
            template: '#daily-payments-template',
            setup() {
                const loading = ref(true);
                const error = ref(null);
                const payments = ref([]);
                const relatedData = reactive({ eleve: [], classe: [], frais: [], user: [] });

                const fetchPayments = async () => {
                    loading.value = true;
                    error.value = null;
                    try {
                        const today = new Date().toISOString().slice(0, 10);
                        // Assuming an API endpoint like /paiement?date_paiement=YYYY-MM-DD
                        const result = await apiCall(`paiement?date_paiement=${today}`);
                        payments.value = result || [];
                        await fetchRelatedData();
                    } catch (e) {
                        error.value = `Erreur: ${e.message}`;
                    } finally {
                        loading.value = false;
                    }
                };

                const fetchRelatedData = async () => {
                    const tablesToFetch = ['eleve', 'classe', 'frais', 'user'];
                    for (const table of tablesToFetch) {
                        try {
                            const result = await apiCall(table);
                            relatedData[table] = result || [];
                        } catch (e) { console.error(`Erreur données associées pour ${table}:`, e); }
                    }
                };

                const getRelatedName = (id, table) => {
                    const item = (relatedData[table] || []).find(d => d.id === id);
                    if (!item) return `ID: ${id}`;
                    if (table === 'eleve') return `${item.nom || ''} ${item.postnom || ''} ${item.prenom || ''}`.trim();
                    if (table === 'user') return item.nom_complet || item.username;
                    return item.nom || `ID: ${id}`;
                };
                
                const formatCurrency = (amount, currency) => {
                    return new Intl.NumberFormat('fr-CD', { style: 'currency', currency: currency || 'USD' }).format(amount || 0);
                };

                const totalUSD = computed(() => payments.value.filter(p => p.devise === 'USD').reduce((sum, p) => sum + parseFloat(p.montant), 0));
                const totalFC = computed(() => payments.value.filter(p => p.devise === 'FC').reduce((sum, p) => sum + parseFloat(p.montant), 0));

                onMounted(fetchPayments);

                return { loading, error, payments, fetchPayments, getRelatedName, formatCurrency, totalUSD, totalFC };
            }
        };

        const Reports = {
            template: '#reports-template',
            setup() {
                // Logic for reports can be added here in the future
                return {};
            }
        };

        const router = createRouter({ 
            history: createWebHashHistory(), 
            routes: [ 
                { path: '/', component: { template: '<div class="text-center mt-5"><h2>Bienvenue sur le tableau de bord de gestion scolaire !</h2><p>Veuillez sélectionner une option dans le menu de gauche.</p></div>' } }, 
                { path: '/table/:tableName', component: TableComponent, props: true },
                { path: '/users', redirect: '/table/user' },
                { path: '/daily-payments', component: DailyPayments },
                { path: '/reports', component: Reports },
                { path: '/login', component: Login }
            ] 
        });

        router.beforeEach((to, from, next) => {
            if (to.path !== '/login' && !authState.isLoggedIn) {
                next('/login');
            } else if (to.path === '/login' && authState.isLoggedIn) {
                next('/');
            } else {
                next();
            }
        });

        const app = createApp(App);
        app.use(router);
        app.mount('#app');
    </script>
</body>
</html>