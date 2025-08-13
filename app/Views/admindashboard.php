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
            margin-left: var(--main-content-margin-left, 0); 
            height: 100vh;
            overflow-y: auto; 
        }
        
        body.navbar-visible #main-content {
            --main-content-margin-left: 250px;
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
        <nav id="navbar-top" v-if="isLoggedIn">
            <h5>Tables de la BDD</h5>
            <ul class="nav">
                <li v-for="table in tables" :key="table" class="nav-item">
                     <router-link v-if="authState.agentRights.includes(table + '.view')" :to="'/table/' + table" class="nav-link">
                        {{ formatTableName(table) }}
                    </router-link>
                </li>
            </ul>
            <hr>
            <h5>Actions</h5>
            <ul class="nav">
                <li class="nav-item">
                    <router-link v-if="authState.agentRights.includes('client.recharger')" to="/recharger-client" class="nav-link">
                        <i class="bi bi-cash-coin me-2"></i>Recharger Client
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
        
        <div class="toast-container notification-toast">
            </div>
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
                                    <tr class="add-row" v-if="hasCreateRight">
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
                                                <input v-else :type="field.type === 'int' || field.type === 'float' ? 'number' : 'text'" v-model="newRecord[field.name]" class="form-control form-control-sm" />
                                            </template>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm" @click="addRecord" v-if="hasCreateRight"><i class="bi bi-plus-circle"></i> Ajouter</button>
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
                                                <input v-else :type="field.type === 'int' || field.type === 'float' ? 'number' : 'text'" v-model="editing.value" @blur="saveCell(item, field.name)" @keyup.enter="saveCell(item, field.name)" @keyup.esc="cancelEdit" v-focus class="form-control form-control-sm"/>
                                            </div>
                                            <div v-else>
                                                <span v-if="field.isEnum">{{ item[field.name] }}</span>
                                                <span v-else-if="field.foreignKey && relatedData[field.foreignKey.relatedTable]">
                                                    {{ (relatedData[field.foreignKey.relatedTable].find(r => r[field.foreignKey.valueField] == item[field.name]) || {})[field.foreignKey.displayField] || item[field.name] }}
                                                </span>
                                                <span v-else>{{ item[field.name] }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <router-link v-if="currentTableMeta.tablename === 'commande'" :to="'/commande/' + item.id" class="btn btn-info btn-sm me-1" title="Voir les détails">
                                                <i class="bi bi-eye"></i>
                                            </router-link>
                                            <button class="btn btn-danger btn-sm" @click="deleteRecord(item.id)" v-if="hasDeleteRight"><i class="bi bi-trash"></i></button>
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
    
    <script type="text/x-template" id="commande-detail-template">
        <div v-if="loading" class="text-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Chargement...</span></div></div>
        <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
        
        <div v-else-if="hasViewRight">
            <div v-if="commande">
                <h2 class="mb-4">Détails de la Commande <span class="text-primary">#{{ commande.code }}</span></h2>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="mb-0">Articles de la Commande</h5></div>
                            <div class="card-body">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Produit</th>
                                            <th class="text-end">Quantité</th>
                                            <th class="text-end">Prix Unitaire</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ligne in lignesCommande" :key="ligne.id">
                                            <td>{{ getProduitName(ligne.fkproduit) }}</td>
                                            <td class="text-end">{{ ligne.quantite }}</td>
                                            <td class="text-end">{{ formatCurrency(ligne.montant / ligne.quantite, commande.devise) }}</td>
                                            <td class="text-end">{{ formatCurrency(ligne.montant, commande.devise) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Sous-total :</th>
                                            <td class="text-end fw-bold">{{ formatCurrency(commande.total_cmd - commande.frais_livraison, commande.devise) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-end">Frais de livraison :</th>
                                            <td class="text-end fw-bold">{{ formatCurrency(commande.frais_livraison, commande.devise) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-end">Total Général :</th>
                                            <td class="text-end fw-bold fs-5">{{ formatCurrency(commande.total_cmd, commande.devise) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Historique des Livraisons</h5>
                                <button class="btn btn-success btn-sm" @click="openAssignModal">
                                    <i class="bi bi-truck me-2"></i>Affecter une livraison
                                </button>
                            </div>
                            <div class="card-body">
                                <div v-if="!livraisons.length" class="alert alert-info">Aucune livraison n'a encore été affectée à cette commande.</div>
                                <table v-else class="table table-sm">
                                    <thead>
                                        <tr><th>Agent (Livreur)</th><th>Statut</th><th>Date de création</th><th>Date de livraison</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="livraison in livraisons" :key="livraison.id">
                                            <td>{{ getAgentName(livraison.fkagent) }}</td>
                                            <td><span :class="getStatusClass(livraison.status)">{{ livraison.status }}</span></td>
                                            <td>{{ new Date(livraison.created_at).toLocaleString() }}</td>
                                            <td>{{ livraison.delivered_at ? new Date(livraison.delivered_at).toLocaleString() : 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="mb-0">Informations</h5></div>
                            <div class="card-body">
                               <p><strong>Statut Commande:</strong> <span :class="getStatusClass(commande.status_cmd)">{{ commande.status_cmd }}</span></p>
                               <p><strong>Statut Paiement:</strong> <span :class="getStatusClass(commande.status_payement)">{{ commande.status_payement }}</span></p>
                               <p><strong>Date Commande:</strong> {{ new Date(commande.created_at).toLocaleString() }}</p>
                               <p><strong>Type:</strong> {{ commande.type_commande }}</p>
                            </div>
                        </div>
                        <div class="card mb-4" v-if="client">
                            <div class="card-header"><h5 class="mb-0">Client</h5></div>
                            <div class="card-body">
                                <p><strong><i class="bi bi-person-fill me-2"></i></strong>{{ client.name_complet }}</p>
                                <p><strong><i class="bi bi-envelope-fill me-2"></i></strong>{{ client.email }}</p>
                                <p><strong><i class="bi bi-telephone-fill me-2"></i></strong>{{ client.primary_phone }}</p>
                            </div>
                        </div>
                        <div class="card mb-4" v-if="adresse">
                            <div class="card-header"><h5 class="mb-0">Adresse de Livraison</h5></div>
                            <div class="card-body">
                               <p><strong>Avenue: </strong>{{ adresse.avenue }}</p>
                               <a :href="googleMapsUrl" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-geo-alt-fill me-2"></i>Ouvrir dans Google Maps
                               </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="alert alert-danger">
                <h3><i class="bi bi-exclamation-octagon-fill me-2"></i>Accès Refusé</h3>
                <p>Vous n'avez pas les droits nécessaires (commande.view) pour consulter les détails de cette commande.</p>
            </div>
        </div>

        <div class="modal fade" id="assignDeliveryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Affecter à un Livreur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="!livreurs.length">Aucun livreur disponible.</div>
                        <div v-else class="mb-3">
                            <label for="agentSelect" class="form-label">Choisir un livreur :</label>
                            <select id="agentSelect" class="form-select" v-model="selectedAgentId">
                                <option :value="null" disabled>-- Sélectionner un agent --</option>
                                <option v-for="livreur in livreurs" :key="livreur.id" :value="livreur.id">
                                    {{ livreur.name_complet }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary" @click="assignDelivery" :disabled="!selectedAgentId">
                           Confirmer l'affectation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="recharge-client-template">
        <div v-if="!hasRight" class="alert alert-danger">
            <h3><i class="bi bi-exclamation-octagon-fill me-2"></i>Accès Refusé</h3>
            <p>Vous n'avez pas les droits nécessaires (client.recharger) pour accéder à cette page.</p>
        </div>
        <div v-else class="container-fluid">
            <div class="card" style="max-width: 600px; margin: auto;">
                <div class="card-header">
                    <h4 class="mb-0">Recharger le compte d'un client</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="phoneSearch" class="form-label">Téléphone du client</label>
                        <div class="input-group">
                            <input type="tel" id="phoneSearch" class="form-control" v-model="phoneNumber" placeholder="+243..." @keyup.enter="searchClient">
                            <button class="btn btn-outline-secondary" type="button" @click="searchClient" :disabled="searchLoading">
                                <span v-if="searchLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <i v-else class="bi bi-search"></i> Rechercher
                            </button>
                        </div>
                    </div>

                    <div v-if="searchMessage" class="alert" :class="foundClient ? 'alert-success' : 'alert-warning'">
                        {{ searchMessage }}
                    </div>

                    <div v-if="foundClient">
                        <hr>
                        <div class="mb-3">
                            <label for="montant" class="form-label">Montant</label>
                            <input type="number" id="montant" class="form-control" v-model.number="amount" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="devise" class="form-label">Devise</label>
                            <select id="devise" class="form-select" v-model="devise">
                                <option value="CDF">CDF</option>
                                <option value="USD">USD</option>
                            </select>
                        </div>
                        <button class="btn btn-primary w-100" @click="submitRecharge" :disabled="!isValidForRecharge || rechargeLoading">
                            <span v-if="rechargeLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Confirmer la Recharge
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script>
        const API_BASE_URL = '<?php echo base_url(); ?>'; 
        const { createApp, ref, reactive, computed, onMounted, watch, nextTick } = Vue;
        const { createRouter, createWebHashHistory } = VueRouter;

        // Global state for authentication
        const authState = reactive({
            isLoggedIn: !!localStorage.getItem('access_token'),
            accessToken: localStorage.getItem('access_token') || null,
            userId: localStorage.getItem('user_id') || null,
            fkagent: localStorage.getItem('fk_agent') || null,
            agentRights: JSON.parse(localStorage.getItem('agent_rights')) || []
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
                const fullUrl = `${API_BASE_URL}/${endpoint.replace(/_/g,'')}`;
                const response = await fetch(fullUrl, options);
                
                if (response.status === 401 || response.status === 403) {
                    window.addNotification('Session expirée ou non autorisée.', 'danger');
                    // localStorage.clear();
                    // Object.assign(authState, { isLoggedIn: false, accessToken: null, userId: null, fkagent: null, agentRights: [] });
                    // router.push('/login');
                    return null;
                }

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

        const tableMetadata = new Map([
            ['user', { tablename: 'user', displayname: 'Utilisateurs', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'username', type: 'text', label: 'Nom d\'utilisateur'}, {name: 'email', type: 'text', label: 'Email'}, {name: 'phone', type: 'text', label: 'Téléphone'}, {name: 'password', type: 'text', label: 'Mot de passe', editable: false}, {name: 'access_token', type: 'text', label: 'Jeton', editable: false}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['ACTIF','INACTIF']}, {name: 'fkclient', type: 'int', label: 'Client', foreignKey: {relatedTable: 'client', displayField: 'name_complet', valueField: 'id'}}, {name: 'fkagent', type: 'int', label: 'Agent', foreignKey: {relatedTable: 'agent', displayField: 'name_complet', valueField: 'id'}}, {name: 'pref_lang', type: 'enum', label: 'Langue', isEnum: true, enumValues: ['FR','EN','SW','L']}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['client', { tablename: 'client', displayname: 'Clients', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'solde_cdf', type: 'float', label: 'Solde CDF'}, {name: 'solde_usd', type: 'float', label: 'Solde USD'}, {name: 'name_complet', type: 'text', label: 'Nom Complet'}, {name: 'email', type: 'text', label: 'Email'}, {name: 'primary_phone', type: 'text', label: 'Tél. Principal'}, {name: 'phone_is_verified', type: 'enum', label: 'Tél. Vérifié', editable: false, isEnum: true, enumValues: ['TRUE','FALSE']}, {name: 'pincode', type: 'text', label: 'Code PIN', editable: false}, {name: 'devise_pref', type: 'enum', label: 'Devise Préf.', isEnum: true, enumValues: ['CDF','USD']}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['ACTIF','INACTIF']}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false}, {name: 'profession', type: 'text', label: 'Profession'}, {name: 'adresse', type: 'text', label: 'Adresse'}, {name: 'photo', type: 'text', label: 'Photo'}, {name: 'statut_juridique', type: 'enum', label: 'Statut Juridique', isEnum: true, enumValues: ['PERSONNE PHYSIQUE','PERSONNE MORALE']}, {name: 'avoir_credit', type: 'enum', label: 'Avoir Crédit', isEnum: true, enumValues: ['OUI','NON']} ] }],
            ['produit', { tablename: 'produit', displayname: 'Produits', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'code', type: 'text', label: 'Code'}, {name: 'designation', type: 'text', label: 'Désignation'}, {name: 'description', type: 'text', label: 'Description'}, {name: 'unite', type: 'text', label: 'Unité'}, {name: 'status', type: 'enum', label: 'Statut', isEnum: true, enumValues: ['ACTIF','INACTIF']}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['CDF','USD']}, {name: 'prix_vente', type: 'float', label: 'Prix Vente'}, {name: 'poids', type: 'float', label: 'Poids'}, {name: 'volume', type: 'float', label: 'Volume'}, {name: 'photo', type: 'text', label: 'Photo'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false}, {name: 'fkcategorie_prod', type: 'int', label: 'Catégorie', foreignKey: {relatedTable: 'categorie_prod', displayField: 'designation', valueField: 'id'}} ] }],
            ['commande', { tablename: 'commande', displayname: 'Commandes', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'code', type: 'text', label: 'Code'}, {name: 'type_commande', type: 'enum', label: 'Type', isEnum: true, enumValues: ['NORMAL','EXPRESS']}, {name: 'delivered_at', type: 'text', label: 'Livré le', editable: false}, {name: 'status_cmd', type: 'enum', label: 'Statut Cmd', isEnum: true, enumValues: ['ATTENTE','LIVRE','REJETE','ACHEMINEMENT']}, {name: 'status_payement', type: 'enum', label: 'Statut Paiement', isEnum: true, enumValues: ['NO-PAYE','ACCOMPTE','PAYE']}, {name: 'fkclient', type: 'int', label: 'Client', foreignKey: {relatedTable: 'client', displayField: 'name_complet', valueField: 'id'}}, {name: 'fkadresse', type: 'int', label: 'Adresse', foreignKey: {relatedTable: 'adresse', displayField: 'libelle_kasokoo', valueField: 'id'}}, {name: 'total_cmd', type: 'float', label: 'Total'}, {name: 'frais_livraison', type: 'float', label: 'Frais Livraison'}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['CDF','USD']}, {name: 'libelle', type: 'text', label: 'Libellé'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['achat', { tablename: 'achat', displayname: 'Achats', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false}, {name: 'delivered_at', type: 'text', label: 'Livré le', editable: false}, {name: 'status_payement', type: 'enum', label: 'Statut Paiement', isEnum: true, enumValues: ['NO-PAYE','ACCOMPTE','PAYE']}, {name: 'fkagent', type: 'int', label: 'Agent', foreignKey: {relatedTable: 'agent', displayField: 'name_complet', valueField: 'id'}}, {name: 'total_achat', type: 'float', label: 'Total Achat'}, {name: 'frais_logistique', type: 'float', label: 'Frais Logistique'}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['CDF','USD']}, {name: 'code_cmd', type: 'text', label: 'Code CMD'}, {name: 'code_achat', type: 'text', label: 'Code Achat'}, {name: 'libelle_cmd', type: 'text', label: 'Libellé CMD'}, {name: 'libelle_achat', type: 'text', label: 'Libellé Achat'}, {name: 'status_cmd', type: 'enum', label: 'Statut CMD', isEnum: true, enumValues: ['RECU','STOCKAGE','ATTENTE','VALIDE']}, {name: 'fkfournisseur', type: 'int', label: 'Fournisseur', foreignKey: {relatedTable: 'fournisseur', displayField: 'denomination', valueField: 'id'}} ] }],
            ['adresse', { tablename: 'adresse', displayname: 'Adresses', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fkuser_create', type: 'int', label: 'Créé par', foreignKey: {relatedTable: 'user', displayField: 'username', valueField: 'id'}}, {name: 'fkuser_validate', type: 'int', label: 'Validé par', foreignKey: {relatedTable: 'user', displayField: 'username', valueField: 'id'}}, {name: 'longitude', type: 'float', label: 'Longitude'}, {name: 'latitude', type: 'float', label: 'Latitude'}, {name: 'is_registred', type: 'enum', label: 'Enregistrée', isEnum: true, enumValues: ['TRUE','FALSE']}, {name: 'code_OLC', type: 'text', label: 'Code OLC'}, {name: 'numero_rue', type: 'text', label: 'N° Rue'}, {name: 'description_batiment', type: 'text', label: 'Description Bâtiment'}, {name: 'libelle_client', type: 'text', label: 'Libellé Client'}, {name: 'libelle_kasokoo', type: 'text', label: 'Libellé Kasokoo'}, {name: 'avenue', type: 'text', label: 'Avenue'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['agent', { tablename: 'agent', displayname: 'Agents', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'name_complet', type: 'text', label: 'Nom Complet'}, {name: 'fonction', type: 'enum', label: 'Fonction', isEnum: true, enumValues: ['LIVREUR','ADMIN','LOGISTICIEN']},{name: 'solde_cdf', type: 'float', label: 'Solde CDF'}, {name: 'solde_usd', type: 'float', label: 'Solde USD'}, {name: 'phone', type: 'text', label: 'Téléphone'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['caisse', { tablename: 'caisse', displayname: 'Caisses', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'designation', type: 'text', label: 'Désignation'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['categorie_prod', { tablename: 'categorie_prod', displayname: 'Catégories Produits', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'designation', type: 'text', label: 'Désignation'}, {name: 'description', type: 'text', label: 'Description'}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
            ['compte', { tablename: 'compte', displayname: 'Comptes', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['CDF','USD']}, {name: 'intutile', type: 'text', label: 'Intitulé'}, {name: 'type_compte', type: 'enum', label: 'Type de Compte', isEnum: true, enumValues: ['COMPTE_DE_GESTION','COMPTE_CLIENT','COMPTE_FOURNISSEUR']}, {name: 'fkclient', type: 'int', label: 'Client', foreignKey: {relatedTable: 'client', displayField: 'name_complet', valueField: 'id'}}, {name: 'fkagent', type: 'int', label: 'Agent', foreignKey: {relatedTable: 'agent', displayField: 'name_complet', valueField: 'id'}},{name: 'fkfournisseur', type: 'int', label: 'Fournisseur', foreignKey: {relatedTable: 'fournisseur', displayField: 'denomination', valueField: 'id'}}, {name: 'fkcaisse', type: 'int', label: 'Caisse', foreignKey: {relatedTable: 'caisse', displayField: 'designation', valueField: 'id'}}, {name: 'created_at', type: 'text', label: 'Créé le', editable: false}, {name: 'updated_at', type: 'text', label: 'Modifié le', editable: false} ] }],
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


        const Login = {
            template: '#login-component-template',
            setup() {
                const username = ref('');
                const password = ref('');
                const loading = ref(false);
                const loginError = ref(null);
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
                        if (response.ok && data.user && data.user.access_token) {
                            localStorage.setItem('access_token', data.user.access_token);
                            localStorage.setItem('user_id', data.user.id);
                            localStorage.setItem('fk_agent', data.user.fkagent);
                            Object.assign(authState, { accessToken: data.user.access_token, userId: data.user.id, fkagent: data.user.fkagent, isLoggedIn: true });
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
                const logout = async () => {
                    try {
                        await apiCall('auth/logout', 'GET');
                        window.addNotification('Déconnexion réussie!', 'success');
                    } catch (e) { console.error('Erreur de déconnexion API (jeton peut-être déjà expiré):', e);
                    } finally {
                        localStorage.clear();
                        Object.assign(authState, { isLoggedIn: false, accessToken: null, userId: null, fkagent: null, agentRights: [] });
                        router.push('/login');
                    }
                };
                async function fetchAndStoreAgentRights() {
                    if (!authState.isLoggedIn || !authState.fkagent) {
                        authState.agentRights = []; localStorage.removeItem('agent_rights'); return;
                    }
                    try {
                        const [droitsAgentResponse, droitsResponse] = await Promise.all([apiCall('droitsagent'), apiCall('droits')]);
                        const agentSpecificRights = (droitsAgentResponse.droitsagents || []).filter(d => d.fkagent == authState.fkagent);
                        const droitIds = new Set(agentSpecificRights.map(d => d.fkdroit));
                        const allDroitsMap = new Map((droitsResponse.droitss || []).map(d => [d.id, d.code]));
                        const codes = Array.from(droitIds).map(id => allDroitsMap.get(id)).filter(Boolean);
                        authState.agentRights = codes;
                        localStorage.setItem('agent_rights', JSON.stringify(codes));
                    } catch (e) { console.error('Erreur chargement droits:', e); }
                }
                watch(() => authState.isLoggedIn, (loggedIn) => {
                    document.body.classList.toggle('navbar-visible', loggedIn);
                    if (loggedIn) fetchAndStoreAgentRights();
                    else { authState.agentRights = []; localStorage.removeItem('agent_rights'); }
                }, { immediate: true });
                return { tables, formatTableName, isLoggedIn: computed(() => authState.isLoggedIn), logout, authState };
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

                const hasViewRight = computed(() => (authState.agentRights || []).includes(props.tableName + '.view'));
                const hasEditRight = computed(() => (authState.agentRights || []).includes(props.tableName + '.edit'));
                const hasDeleteRight = computed(() => (authState.agentRights || []).includes(props.tableName + '.delete'));
                const hasCreateRight = computed(() => (authState.agentRights || []).includes(props.tableName + '.create'));

                const currentTableMeta = computed(() => tableMetadata.get(props.tableName) || { tablename: props.tableName, displayname: props.tableName.replace(/_/g, ' '), fields: [] });
                const displayedFields = computed(() => managedFields.value.filter(f => f.visible));

                const setupColumns = () => {
                    const baseFields = currentTableMeta.value.fields;
                    if (!baseFields) { managedFields.value = []; return; }
                    const savedSettings = JSON.parse(localStorage.getItem(`table-settings-${props.tableName}`));
                    let fields = baseFields.map(field => ({ ...field, visible: savedSettings ? (savedSettings.find(s => s.name === field.name) || {visible:true}).visible : (field.editable !== false || field.name === 'id') }));
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
                    if (!hasViewRight.value) { error.value = 'Droits insuffisants.'; loading.value = false; data.value = []; return; }
                    try {
                        const result = await apiCall(props.tableName);
                        const dataKey = props.tableName.replace(/_/g,'') + 's';
                        data.value = (result && result[dataKey]) || [];
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
                            const dataKey = table.replace(/_/g,'') + 's';
                            relatedData[table] = (result && result[dataKey]) || [];
                        } catch (e) {
                            console.error(`Erreur données associées pour ${table}:`, e);
                        }
                    }
                };

                const addRecord = async () => {
                    if (!hasCreateRight.value) return window.addNotification('Droits insuffisants.', 'danger');
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
                    if (!hasDeleteRight.value) return window.addNotification('Droits insuffisants.', 'danger');
                    if (!confirm('Vraiment supprimer?')) return;
                    try {
                        await apiCall(`${props.tableName}/${id}`, 'DELETE');
                        data.value = data.value.filter(item => item.id !== id);
                        window.addNotification('Supprimé.', 'success');
                    } catch (e) { /* handled by apiCall */ }
                };

                const editCell = (item, field) => {
                    if (field.editable === false || !hasEditRight.value) return;
                    editing.value = { id: item.id, field: field.name, value: item[field.name] };
                };

                const saveCell = async (item, fieldName) => {
                    if (editing.value.id === null) return;
                    const originalValue = item[fieldName];
                    const newValue = editing.value.value;
                    if (originalValue == newValue) { cancelEdit(); return; }
                    if (!hasEditRight.value) { cancelEdit(); return; }
                    const itemIndex = data.value.findIndex(d => d.id === item.id);
                    if (itemIndex === -1) { cancelEdit(); return; }
                    const currentItemRef = data.value[itemIndex];
                    const oldItemState = { ...currentItemRef };
                    currentItemRef[fieldName] = newValue;
                    try {
                        await apiCall(`${props.tableName}/${item.id}`, 'PUT', { [fieldName]: newValue });
                        window.addNotification('Mis à jour.', 'success');
                    } catch (e) {
                        Object.assign(currentItemRef, oldItemState);
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
                watch([() => authState.isLoggedIn, () => authState.agentRights], refreshData, {deep: true});

                return { data, loading, error, searchQuery, sortKey, sortOrder, sortBy, filteredData, deleteRecord, addRecord, editing, editCell, saveCell, cancelEdit, newRecord, relatedData, currentTableMeta, refreshData, managedFields, displayedFields, openColumnSettingsModal, saveColumnSettings, moveColumn, hasViewRight, hasEditRight, hasDeleteRight, hasCreateRight };
            },
            directives: { focus: { mounted(el) { nextTick(() => el.focus()); } } }
        };

        const CommandeDetail = {
            template: '#commande-detail-template',
            props: ['id'],
            setup(props) {
                const loading = ref(true);
                const error = ref(null);
                const commande = ref(null);
                const client = ref(null);
                const adresse = ref(null);
                const lignesCommande = ref([]);
                const livraisons = ref([]);
                const produits = ref([]);
                const agents = ref([]);
                const livreurs = ref([]);
                const selectedAgentId = ref(null);
                let assignModalInstance = null;

                const hasViewRight = computed(() => {
                    return authState.isLoggedIn && (authState.agentRights || []).includes('commande.view');
                });

                const googleMapsUrl = computed(() => {
                    if (adresse.value && adresse.value.latitude && adresse.value.longitude) {
                        return `https://www.google.com/maps?q=${adresse.value.latitude},${adresse.value.longitude}`;
                    }
                    return '#';
                });
                
                const getProduitName = (produitId) => {
                    const produit = produits.value.find(p => p.id === produitId);
                    return produit ? produit.designation : `Produit ID: ${produitId}`;
                };

                const getAgentName = (agentId) => {
                    const agent = agents.value.find(a => a.id === agentId);
                    return agent ? agent.name_complet : `Agent ID: ${agentId}`;
                };

                const formatCurrency = (amount, currency) => {
                    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: currency || 'USD' }).format(amount || 0);
                };

                const getStatusClass = (status) => {
                    const classes = {
                        'LIVRE': 'badge bg-success', 'PAYE': 'badge bg-success',
                        'ENCOURS': 'badge bg-warning text-dark', 'ACHEMINEMENT': 'badge bg-warning text-dark', 'ACCOMPTE': 'badge bg-warning text-dark',
                        'NON_LIVRE': 'badge bg-danger', 'REJETE': 'badge bg-danger', 'NO-PAYE': 'badge bg-danger',
                        'ATTENTE': 'badge bg-secondary',
                    };
                    return classes[status] || 'badge bg-light text-dark';
                };

                const openAssignModal = () => {
                    if (assignModalInstance) assignModalInstance.show();
                };

                const assignDelivery = async () => {
                    if (!selectedAgentId.value) {
                        window.addNotification('Veuillez sélectionner un livreur.', 'danger');
                        return;
                    }
                    try {
                        const payload = {
                            fkcommande: props.id,
                            fkagent: selectedAgentId.value,
                            status: 'ENCOURS'
                        };
                        const newLivraison = await apiCall('livraison', 'POST', payload);
                        if (newLivraison) {
                            livraisons.value.push(newLivraison);
                            window.addNotification('Livraison affectée avec succès!', 'success');
                            selectedAgentId.value = null;
                            assignModalInstance.hide();
                        }
                    } catch (e) { /* Error handled by apiCall */ }
                };
                
                const fetchData = async () => {
                    loading.value = true;
                    error.value = null;

                    if (!hasViewRight.value) {
                        loading.value = false;
                        return;
                    }

                    try {
                        const [cmdData, lignesData, livraisonsData, produitsData, agentsData] = await Promise.all([
                            apiCall(`commande/${props.id}`),
                            apiCall('lignecommande'),
                            apiCall('livraison'),
                            apiCall('produit'),
                            apiCall('agent')
                        ]);

                        if (!cmdData) throw new Error("Commande non trouvée.");
                        
                        commande.value = cmdData.commande;
                        lignesCommande.value = (lignesData.lignecommandes || []).filter(l => l.fkcommande == props.id);
                        livraisons.value = (livraisonsData.livraisons || []).filter(l => l.fkcommande == props.id);
                        produits.value = produitsData.produits || [];
                        agents.value = agentsData.agents || [];
                        livreurs.value = (agentsData.agents || []).filter(a => a.fonction === 'LIVREUR');

                        if (commande.value.fkclient && commande.value.fkadresse) {
                            const [clientData, adresseData] = await Promise.all([
                                apiCall(`client/${commande.value.fkclient}`),
                                apiCall(`adresse/${commande.value.fkadresse}`)
                            ]);
                            client.value = clientData.client;
                            adresse.value = adresseData.adresse;
                        }
                    } catch (e) {
                        error.value = `Impossible de charger les détails: ${e.message}`;
                    } finally {
                        loading.value = false;
                    }
                };

                onMounted(() => {
                    if (authState.isLoggedIn) {
                        fetchData();
                        const modalEl = document.getElementById('assignDeliveryModal');
                        if (modalEl) assignModalInstance = new bootstrap.Modal(modalEl);
                    } else {
                        loading.value = false; // Stop loading if not logged in
                    }
                });

                return { loading, error, commande, client, adresse, lignesCommande, livraisons, livreurs, googleMapsUrl, selectedAgentId, hasViewRight, getProduitName, getAgentName, formatCurrency, getStatusClass, openAssignModal, assignDelivery };
            }
        };

        const RechargeClient = {
            template: '#recharge-client-template',
            setup() {
                const phoneNumber = ref('');
                const amount = ref(null);
                const devise = ref('USD');
                const foundUser = ref(null);
                const foundClient = ref(null);
                const searchLoading = ref(false);
                const rechargeLoading = ref(false);
                const searchMessage = ref('');

                const hasRight = computed(() => (authState.agentRights || []).includes('client.recharger'));
                
                const isValidForRecharge = computed(() => {
                    return foundClient.value && amount.value && amount.value > 0 && devise.value;
                });

                const resetForm = () => {
                    phoneNumber.value = '';
                    amount.value = null;
                    devise.value = 'USD';
                    foundUser.value = null;
                    foundClient.value = null;
                    searchMessage.value = '';
                    searchLoading.value = false;
                    rechargeLoading.value = false;
                };
                
                const searchClient = async () => {
                    if (!phoneNumber.value) {
                        searchMessage.value = "Veuillez entrer un numéro de téléphone.";
                        return;
                    }
                    searchLoading.value = true;
                    foundClient.value = null;
                    foundUser.value = null;
                    searchMessage.value = '';

                    try {
                        const response = await apiCall('search', 'POST', {
                            table: "user",
                            where: { phone: phoneNumber.value }
                        });

                        if (response && response.result && response.result.length > 0) {
                            foundUser.value = response.result[0];
                            if (foundUser.value.fkclient) {
                                const clientResponse = await apiCall(`client/${foundUser.value.fkclient}`);
                                if (clientResponse && clientResponse.client) {
                                    foundClient.value = clientResponse.client;
                                    searchMessage.value = `Client trouvé : ${foundClient.value.name_complet}`;
                                } else {
                                    throw new Error("Client associé à l'utilisateur non trouvé.");
                                }
                            } else {
                                throw new Error("Cet utilisateur n'est pas un client.");
                            }
                        } else {
                            throw new Error( (response && response.message) || "Aucun utilisateur trouvé avec ce numéro de téléphone.");
                        }
                    } catch (e) {
                        searchMessage.value = `Erreur de recherche : ${e.message}`;
                    } finally {
                        searchLoading.value = false;
                    }
                };

                const submitRecharge = async () => {
                    if (!isValidForRecharge.value) {
                        window.addNotification("Veuillez remplir tous les champs correctement.", "danger");
                        return;
                    }
                    if (!confirm(`Confirmez-vous la recharge de ${amount.value} ${devise.value} pour ${foundClient.value.name_complet} ?`)) {
                        return;
                    }
                    
                    rechargeLoading.value = true;

                    try {
                        const payload = {
                            client: {
                                id: foundClient.value.id,
                                user_id: foundUser.value.id
                            },
                            idClient:foundClient.value.id,
                            idAgent:authState.fkagent,
                            agent: {
                                id: authState.fkagent,
                                user_id: authState.userId
                            },
                            montant: amount.value,
                            devise: devise.value
                        };
                        
                        const response = await apiCall('transaction/rechargerclient', 'POST', payload);

                        window.addNotification(response.message || 'Recharge effectuée avec succès!', 'success');
                        resetForm();

                    } catch (e) {
                        // apiCall already shows a notification, no need for a second one.
                    } finally {
                        rechargeLoading.value = false;
                    }
                };
                
                onMounted(() => {
                    if (!hasRight.value) {
                        console.warn("Accès refusé à la page de recharge (droit 'client.recharger' manquant).");
                    }
                });

                return { phoneNumber, amount, devise, foundClient, searchLoading, rechargeLoading, searchMessage, hasRight, isValidForRecharge, searchClient, submitRecharge };
            }
        };

        const router = createRouter({ 
            history: createWebHashHistory(), 
            routes: [ 
                { path: '/', component: { template: '<div class="text-center mt-5"><h2>Bienvenue !</h2><p>Veuillez sélectionner une table dans le menu de gauche.</p></div>' } }, 
                { path: '/table/:tableName', component: TableComponent, props: true },
                { path: '/login', component: Login },
                { path: '/commande/:id', component: CommandeDetail, props: true },
                { path: '/recharger-client', component: RechargeClient }
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