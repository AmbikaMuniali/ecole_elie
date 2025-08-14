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

        /* Added new styles for login/register pages */
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5;
        }

    </style>
</head>
<body>

    <div id="app">
        <nav id="navbar-top" v-if="isLoggedIn">
            <h5>Tables de la BDD</h5>
            <ul class="nav">
                <li v-for="table in tables" :key="table" class="nav-item">
                     <router-link v-if="authState.userPermissions.includes(table + '.view')" :to="'/table/' + table" class="nav-link">
                        {{ formatTableName(table) }}
                    </router-link>
                </li>
            </ul>
            <hr>
            <h5>Actions</h5>
            <ul class="nav">
                <li class="nav-item">
                    <router-link v-if="authState.userPermissions.includes('client.recharger')" to="/recharger-client" class="nav-link">
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
        <div class="auth-container">
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
                    <!-- Link to registration page -->
                    <div class="mt-3 text-center">
                        <router-link to="/register">Créer un compte</router-link>
                    </div>
                </div>
            </div>
        </div>
    </script>
    
    <!-- New Template for the Register Component -->
    <script type="text/x-template" id="register-component-template">
        <div class="auth-container">
            <div class="card shadow-lg p-4" style="width: 100%; max-width: 400px;">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Créer un compte</h2>
                    <form @submit.prevent="handleRegistration">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" v-model="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="fullName" v-model="fullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Numéro de téléphone</label>
                            <input type="text" class="form-control" id="phoneNumber" v-model="phoneNumber">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" v-model="password" required>
                        </div>
                        <div v-if="error" class="alert alert-danger">{{ error }}</div>
                        <div v-if="success" class="alert alert-success">{{ success }}</div>
                        <button type="submit" class="btn btn-success w-100 mt-3" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            S'inscrire
                        </button>
                    </form>
                    <div class="mt-3 text-center">
                        <router-link to="/login">Déjà un compte? Connectez-vous</router-link>
                    </div>
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
                <p>Vous n'avez pas les permissions nécessaires (commande.view) pour consulter les détails de cette commande.</p>
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
            <p>Vous n'avez pas les permissions nécessaires (client.recharger) pour accéder à cette page.</p>
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
                        <button class="btn btn-success" @click="submitRecharge" :disabled="rechargeLoading">
                             <span v-if="rechargeLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Recharger
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="rechargeStatusModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Statut de la Recharge</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p v-if="rechargeError" class="alert alert-danger">{{ rechargeError }}</p>
                        <p v-else-if="rechargeSuccess" class="alert alert-success">{{ rechargeSuccess }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </script>


    <script type="module">
        
        const { createApp, ref, reactive, onMounted, computed } = Vue;
        const { createRouter, createWebHashHistory } = VueRouter;

        // Reactive store for authentication state
        const authState = reactive({
            isLoggedIn: false,
            user: null,
            userPermissions: [],
        });

        // Components
        const Login = {
            template: '#login-component-template',
            setup() {
                const username = ref('');
                const password = ref('');
                const loginError = ref('');
                const loading = ref(false);
                
                const handleLogin = async () => {
                    loading.value = true;
                    loginError.value = '';
                    try {
                        const response = await fetch('/auth/login', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ username: username.value, password: password.value })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            authState.isLoggedIn = true;
                            authState.user = data.user;
                            authState.userPermissions = data.userPermissions;
                            document.body.classList.add('navbar-visible');
                            router.push('/');
                        } else {
                            loginError.value = data.message || 'Échec de la connexion.';
                        }
                    } catch (e) {
                        loginError.value = 'Erreur de connexion.';
                    } finally {
                        loading.value = false;
                    }
                };

                return { username, password, handleLogin, loginError, loading };
            }
        };

        const Register = {
            template: '#register-component-template',
            setup() {
                const email = ref('');
                const fullName = ref('');
                const phoneNumber = ref('');
                const password = ref('');
                const error = ref('');
                const success = ref('');
                const loading = ref(false);

                const handleRegistration = async () => {
                    loading.value = true;
                    error.value = '';
                    success.value = '';

                    try {
                        const response = await fetch('/auth/register', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ 
                                email: email.value, 
                                password: password.value,
                                fullName: fullName.value,
                                phoneNumber: phoneNumber.value
                            })
                        });
                        const data = await response.json();

                        if (response.ok) {
                            success.value = 'Inscription réussie! Vous pouvez maintenant vous connecter.';
                            email.value = '';
                            fullName.value = '';
                            phoneNumber.value = '';
                            password.value = '';
                        } else {
                            error.value = data.message || 'Échec de l\'inscription.';
                        }
                    } catch (e) {
                        error.value = 'Erreur d\'inscription.';
                    } finally {
                        loading.value = false;
                    }
                };

                return { email, fullName, phoneNumber, password, handleRegistration, error, success, loading };
            }
        };

        const TableComponent = {
            template: '#table-component-template',
            props: ['tableName'],
            data() { return { 
                loading: false, error: null, data: [], sortKey: '', sortOrder: 'asc', searchQuery: '', 
                editing: { id: null, field: null, value: '' }, 
                newRecord: {},
                currentTableMeta: { tablename: this.tableName, displayname: this.tableName, fields: [] },
                managedFields: [],
                relatedData: {}
             }; },
            directives: { 'focus': { mounted(el) { el.focus(); } } },
            computed: {
                filteredData() {
                    let filtered = this.data;
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(item =>
                            Object.values(item).some(value =>
                                String(value).toLowerCase().includes(query)
                            )
                        );
                    }
                    if (this.sortKey) {
                        filtered = filtered.sort((a, b) => {
                            const aValue = a[this.sortKey];
                            const bValue = b[this.sortKey];
                            let comparison = 0;
                            if (aValue > bValue) comparison = 1;
                            else if (aValue < bValue) comparison = -1;
                            return this.sortOrder === 'asc' ? comparison : -comparison;
                        });
                    }
                    return filtered;
                },
                displayedFields() {
                    return this.managedFields.filter(f => f.visible);
                },
                hasCreateRight() { return authState.userPermissions.includes(this.tableName + '.create'); },
                hasDeleteRight() { return authState.userPermissions.includes(this.tableName + '.delete'); },
            },
            watch: { tableName: 'refreshData' },
            mounted() { this.refreshData(); },
            methods: {
                async refreshData() {
                    this.loading = true;
                    this.error = null;
                    this.data = [];
                    try {
                        const response = await fetch(`/api/meta/${this.tableName}`);
                        const meta = await response.json();
                        this.currentTableMeta = meta;
                        this.managedFields = meta.fields.map(f => ({ ...f, visible: true }));
                        
                        const dataResponse = await fetch(`/api/table/${this.tableName}`);
                        if (!dataResponse.ok) throw new Error('Failed to fetch data.');
                        this.data = await dataResponse.json();
                        
                        await this.fetchRelatedData();

                    } catch (e) {
                        this.error = 'Erreur lors du chargement des données: ' + e.message;
                    } finally {
                        this.loading = false;
                    }
                },
                async fetchRelatedData() {
                    const foreignKeys = this.currentTableMeta.fields.filter(f => f.foreignKey);
                    for (const field of foreignKeys) {
                        const table = field.foreignKey.relatedTable;
                        if (!this.relatedData[table]) {
                            try {
                                const response = await fetch(`/api/table/${table}`);
                                if (response.ok) {
                                    this.relatedData[table] = await response.json();
                                }
                            } catch (e) {
                                console.error(`Erreur lors du chargement des données de la table liée ${table}:`, e);
                            }
                        }
                    }
                },
                sortBy(key) {
                    if (this.sortKey === key) {
                        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortKey = key;
                        this.sortOrder = 'asc';
                    }
                },
                editCell(item, field) {
                    this.editing.id = item.id;
                    this.editing.field = field.name;
                    this.editing.value = item[field.name];
                },
                async saveCell(item, fieldName) {
                    if (!authState.userPermissions.includes(this.tableName + '.update')) {
                        alert('Accès refusé: Vous n\'avez pas les permissions de modification.');
                        this.cancelEdit();
                        return;
                    }
                    const oldValue = item[fieldName];
                    const newValue = this.editing.value;
                    this.cancelEdit();

                    if (oldValue == newValue) return;

                    try {
                        const updateData = { [fieldName]: newValue };
                        const response = await fetch(`${APP_ENDPOINT}/${this.tableName}/${item.id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(updateData)
                        });
                        const result = await response.json();
                        if (response.ok) {
                            item[fieldName] = newValue;
                        } else {
                            alert('Erreur de mise à jour: ' + (result.message || 'Unknown error'));
                            // Restore old value on failure
                            item[fieldName] = oldValue;
                        }
                    } catch (e) {
                        alert('Erreur: ' + e.message);
                         // Restore old value on failure
                        item[fieldName] = oldValue;
                    }
                },
                cancelEdit() {
                    this.editing = { id: null, field: null, value: '' };
                },
                async addRecord() {
                    if (!this.hasCreateRight) {
                        alert('Accès refusé: Vous n\'avez pas les permissions de création.');
                        return;
                    }
                    try {
                         const response = await fetch(`/api/table/${this.tableName}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.newRecord)
                        });
                        const result = await response.json();
                        if (response.ok) {
                            this.data.unshift(result); // Add to top of table
                            this.newRecord = {}; // Reset form
                        } else {
                            alert('Erreur d\'ajout: ' + (result.message || 'Unknown error'));
                        }
                    } catch (e) {
                        alert('Erreur: ' + e.message);
                    }
                },
                async deleteRecord(id) {
                    if (!this.hasDeleteRight) {
                        alert('Accès refusé: Vous n\'avez pas les permissions de suppression.');
                        return;
                    }
                    if (confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement?')) {
                        try {
                            const response = await fetch(`/api/table/${this.tableName}/${id}`, {
                                method: 'DELETE'
                            });
                            const result = await response.json();
                            if (response.ok) {
                                this.data = this.data.filter(item => item.id !== id);
                            } else {
                                alert('Erreur de suppression: ' + (result.message || 'Unknown error'));
                            }
                        } catch (e) {
                            alert('Erreur: ' + e.message);
                        }
                    }
                },
                openColumnSettingsModal() {
                    const modalElement = document.getElementById('columnSettingsModal');
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                },
                moveColumn(index, direction) {
                    const newIndex = index + direction;
                    if (newIndex >= 0 && newIndex < this.managedFields.length) {
                        const item = this.managedFields.splice(index, 1)[0];
                        this.managedFields.splice(newIndex, 0, item);
                    }
                },
                saveColumnSettings() {
                    const modalElement = document.getElementById('columnSettingsModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    modal.hide();
                },
            }
        };
        
        const CommandeDetail = {
            template: '#commande-detail-template',
            props: ['id'],
            data() { return {
                loading: true,
                error: null,
                commande: null,
                lignesCommande: [],
                produits: [],
                livraisons: [],
                agents: [],
                client: null,
                adresse: null,
                livreurs: [],
                selectedAgentId: null,
             }; },
            computed: {
                hasViewRight() { return authState.userPermissions.includes('commande.view'); },
                googleMapsUrl() {
                    if (this.adresse) {
                        return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(this.adresse.avenue + ', ' + this.adresse.ville + ', ' + this.adresse.pays)}`;
                    }
                    return '#';
                },
            },
            watch: { id: 'fetchData' },
            mounted() { this.fetchData(); },
            methods: {
                async fetchData() {
                    this.loading = true;
                    this.error = null;
                    if (!this.hasViewRight) {
                        this.loading = false;
                        return;
                    }

                    try {
                        const [cmdRes, lignesRes, produitsRes, livraisonsRes, agentsRes, clientsRes, adressesRes] = await Promise.all([
                            fetch(`/api/commande/${this.id}`),
                            fetch(`/api/commande/${this.id}/lignes`),
                            fetch(`/api/table/produit`),
                            fetch(`/api/commande/${this.id}/livraisons`),
                            fetch(`/api/table/agent`),
                            fetch(`/api/table/client`),
                            fetch(`/api/table/adresse`),
                        ]);

                        if (!cmdRes.ok) throw new Error('Commande non trouvée.');
                        
                        this.commande = await cmdRes.json();
                        this.lignesCommande = await lignesRes.json();
                        this.produits = await produitsRes.json();
                        this.livraisons = await livraisonsRes.json();
                        this.agents = await agentsRes.json();
                        const clients = await clientsRes.json();
                        const adresses = await adressesRes.json();
                        
                        this.client = clients.find(c => c.id === this.commande.fkclient);
                        this.adresse = adresses.find(a => a.id === this.commande.fkadresse);
                        this.livreurs = this.agents.filter(a => a.role_name === 'livreur');

                    } catch (e) {
                        this.error = e.message;
                    } finally {
                        this.loading = false;
                    }
                },
                getProduitName(id) {
                    const produit = this.produits.find(p => p.id === id);
                    return produit ? produit.designation : 'N/A';
                },
                getAgentName(id) {
                    const agent = this.agents.find(a => a.id === id);
                    return agent ? agent.name_complet : 'N/A';
                },
                getStatusClass(status) {
                    switch (status) {
                        case 'pending': return 'badge bg-secondary';
                        case 'confirmed': return 'badge bg-info';
                        case 'in_progress': return 'badge bg-warning text-dark';
                        case 'completed': return 'badge bg-success';
                        case 'delivered': return 'badge bg-success';
                        case 'cancelled': return 'badge bg-danger';
                        default: return 'badge bg-light text-dark';
                    }
                },
                formatCurrency(value, currency) {
                    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: currency }).format(value);
                },
                openAssignModal() {
                    this.selectedAgentId = null;
                    const modalElement = document.getElementById('assignDeliveryModal');
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                },
                async assignDelivery() {
                     if (!this.selectedAgentId) return;

                     try {
                        const response = await fetch(`/api/commande/${this.id}/assign-delivery`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ agentId: this.selectedAgentId })
                        });
                        const result = await response.json();
                        if (response.ok) {
                            alert('Livraison affectée avec succès!');
                            const modalElement = document.getElementById('assignDeliveryModal');
                            const modal = bootstrap.Modal.getInstance(modalElement);
                            modal.hide();
                            this.fetchData(); // Refresh data to show new delivery
                        } else {
                            alert('Erreur: ' + (result.message || 'Une erreur est survenue.'));
                        }
                    } catch (e) {
                        alert('Erreur: ' + e.message);
                    }
                },
            }
        };

        const RechargeClient = {
            template: '#recharge-client-template',
            data() { return {
                phoneNumber: '',
                amount: 0,
                devise: 'CDF',
                foundClient: null,
                searchLoading: false,
                rechargeLoading: false,
                searchMessage: '',
                rechargeError: '',
                rechargeSuccess: '',
            }; },
            computed: {
                hasRight() {
                    return authState.userPermissions.includes('client.recharger');
                },
            },
            methods: {
                async searchClient() {
                    this.searchLoading = true;
                    this.searchMessage = '';
                    this.foundClient = null;

                    try {
                        const response = await fetch('/api/search-client', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ phoneNumber: this.phoneNumber })
                        });
                        const data = await response.json();
                        
                        if (response.ok) {
                            this.foundClient = data;
                            this.searchMessage = `Client trouvé : ${data.name_complet}`;
                        } else {
                            this.searchMessage = data.message || 'Client non trouvé.';
                        }
                    } catch (error) {
                        this.searchMessage = 'Erreur de recherche: ' + error.message;
                    } finally {
                        this.searchLoading = false;
                    }
                },
                async submitRecharge() {
                    if (!this.foundClient || this.amount <= 0 || !this.devise) {
                        this.rechargeError = 'Veuillez remplir tous les champs correctement.';
                        this.showModal();
                        return;
                    }
                    this.rechargeLoading = true;
                    this.rechargeError = '';
                    this.rechargeSuccess = '';

                    try {
                        const response = await fetch('/api/recharge', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                clientId: this.foundClient.id,
                                amount: this.amount,
                                devise: this.devise
                            })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.rechargeSuccess = 'Recharge effectuée avec succès!';
                            // Reset form
                            this.phoneNumber = '';
                            this.amount = 0;
                            this.devise = 'CDF';
                            this.foundClient = null;
                            this.searchMessage = '';
                        } else {
                            this.rechargeError = data.message || 'Une erreur est survenue.';
                        }
                    } catch (error) {
                        this.rechargeError = 'Erreur: ' + error.message;
                    } finally {
                        this.rechargeLoading = false;
                        this.showModal();
                    }
                },
                showModal() {
                    const modalElement = document.getElementById('rechargeStatusModal');
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                },
            }
        };

        const App = {
            data() { return { tables: ['produit', 'client', 'commande', 'agent'] }; },
            computed: {
                isLoggedIn() { return authState.isLoggedIn; },
                authReady() { return !!authState.user; }
            },
            methods: {
                formatTableName(name) { return name.charAt(0).toUpperCase() + name.slice(1); },
                logout() {
                    authState.isLoggedIn = false;
                    authState.user = null;
                    authState.userPermissions = [];
                    document.body.classList.remove('navbar-visible');
                    router.push('/login');
                },
            },
            watch: {
                isLoggedIn(newVal) {
                    if (newVal) {
                        document.body.classList.add('navbar-visible');
                    } else {
                        document.body.classList.remove('navbar-visible');
                    }
                }
            }
        };

        const router = createRouter({ 
            history: createWebHashHistory(), 
            routes: [ 
                { path: '/', component: { template: '<div class="text-center mt-5"><h2>Bienvenue !</h2><p>Veuillez sélectionner une table ou une action dans le menu.</p></div>' } }, 
                { path: '/table/:tableName', component: TableComponent, props: true },
                { path: '/commande/:id', component: CommandeDetail, props: true },
                { path: '/recharger-client', component: RechargeClient },
                { path: '/login', component: Login },
                { path: '/register', component: Register } // New route for registration
            ] 
        });

        router.beforeEach((to, from, next) => {
            const publicPaths = ['/login', '/register'];
            if (!authState.isLoggedIn && !publicPaths.includes(to.path)) {
                next('/login');
            } else if (authState.isLoggedIn && publicPaths.includes(to.path)) {
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
