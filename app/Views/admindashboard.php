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
        .tuition-table th:first-child, .tuition-table td:first-child {
            position: sticky;
            left: 0;
            background-color: #f8f9fa;
            z-index: 1;
        }
        .tuition-table .cell-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 38px;
        }
        .pupil-search-results {
            height: 150px;
            width: 100%;
            overflow-y: scroll;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-receipt, #printable-receipt * {
                visibility: visible;
            }
            #printable-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
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
                     <router-link to="/daily-payments" class="nav-link"><i class="bi bi-cash-coin me-2"></i>Paiements</router-link>
                </li>
                 <li v-if="hasPermission('reports.generate')" class="nav-item">
                     <router-link to="/reports" class="nav-link"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Générer Rapports</router-link>
                </li>
                 <li v-if="hasPermission('frais.view')" class="nav-item">
                     <router-link to="/scolar-year-tuition" class="nav-link"><i class="bi bi-wallet2 me-2"></i>Frais Scolaires</router-link>
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
            <div class="row g-4">
                <!-- Payment Zone -->
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-header"><h4 class="mb-0">Effectuer un Paiement</h4></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="paymentYear" class="form-label">Année Scolaire</label>
                                <select id="paymentYear" v-model="selectedYear" class="form-select" :disabled="!years.length">
                                    <option v-for="year in years" :key="year.id" :value="year.id">{{ year.nom }}</option>
                                </select>
                            </div>
                            <hr>
                            <h6><i class="bi bi-search me-2"></i>Rechercher un élève</h6>
                            <div class="row g-2 mb-2">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="Nom" v-model="pupilSearch.nom" @keyup.enter="searchPupils">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" placeholder="Prénom" v-model="pupilSearch.prenom" @keyup.enter="searchPupils">
                                </div>
                            </div>
                            <div class="d-flex gap-2 mb-3">
                                <button class="btn btn-primary w-100" @click="searchPupils" :disabled="searchLoading">
                                    <i class="bi bi-search" :class="{'loading-spinner': searchLoading}"></i> Chercher
                                </button>
                                <router-link to="/table/eleve" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-plus-circle"></i> Nouveau
                                </router-link>
                            </div>

                            <div v-if="searchResults.length > 0" class="pupil-search-results mb-3">
                                <ul class="list-group">
                                    <li v-for="pupil in searchResults" :key="pupil.id" class="list-group-item list-group-item-action" @click="selectPupil(pupil)">
                                        {{ pupil.nom }} {{ pupil.postnom }} {{ pupil.prenom }}
                                    </li>
                                </ul>
                            </div>

                            <div v-if="selectedPupil.id">
                                <hr>
                                <div class="alert alert-success">
                                    <h5 class="alert-heading">{{ selectedPupil.nom }} {{ selectedPupil.postnom }}</h5>
                                    <p class="mb-1">Classe: <strong v-if="pupilClass.nom">{{ pupilClass.nom }}</strong><em v-else>Non inscrit cette année</em></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Type de Frais</label>
                                    <select class="form-select" v-model="newPayment.fk_frais" :disabled="!pupilClass.id || !availableTuitions.length">
                                        <option v-if="!pupilClass.id" disabled value="">Veuillez inscrire l'élève</option>
                                        <option v-else v-for="tuition in availableTuitions" :key="tuition.id" :value="tuition.id">
                                            {{ getRelatedName(tuition.fk_type_frais, 'type_frais') }} ({{ formatCurrency(tuition.montant, tuition.devise) }})
                                        </option>
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-sm-7">
                                        <label class="form-label">Montant</label>
                                        <input type="number" class="form-control" v-model.number="newPayment.montant">
                                    </div>
                                    <div class="col-sm-5">
                                        <label class="form-label">Devise</label>
                                        <select class="form-select" v-model="newPayment.devise">
                                            <option>USD</option>
                                            <option>FC</option>
                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-success w-100" @click="makePayment" :disabled="!newPayment.fk_frais || newPayment.montant <= 0 || paymentLoading">
                                    <i class="bi bi-check-circle" :class="{'loading-spinner': paymentLoading}"></i> Payer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Summary Zone -->
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Journal des Paiements</h4>
                             <div class="d-flex align-items-center gap-2">
                                <input type="date" v-model="paymentDate" class="form-control form-control-sm" style="width: auto;">
                                <button class="btn btn-primary btn-sm" @click="fetchPayments" :disabled="loading" title="Actualiser">
                                    <i class="bi bi-arrow-clockwise" :class="{'loading-spinner': loading}"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="error" class="alert alert-danger">{{ error }}</div>
                            <div v-if="loading" class="text-center p-5"><div class="spinner-border" role="status"></div></div>
                            <div v-else-if="payments.length === 0" class="alert alert-info">Aucun paiement pour le {{ new Date(paymentDate).toLocaleDateString() }}.</div>
                            <div v-else class="table-responsive">
                                <table class="table table-striped table-hover">
                                     <thead class="table-dark">
                                        <tr>
                                            <th>Élève</th>
                                            <th>Classe</th>
                                            <th>Frais Payé</th>
                                            <th class="text-end">Montant</th>
                                            <th>Perçu par</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="payment in payments" :key="payment.id">
                                            <td>{{ getRelatedName(payment.fk_eleve, 'eleve') }}</td>
                                            <td>{{ getRelatedName(payment.fk_classe, 'classe') }}</td>
                                            <td>{{ getFeeTypeNameFromPayment(payment) }}</td>
                                            <td class="text-end">{{ formatCurrency(payment.montant, payment.devise) }}</td>
                                            <td>{{ getRelatedName(payment.fk_user, 'user') }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-secondary" @click="printReceipt(payment)" title="Imprimer le reçu">
                                                    <i class="bi bi-printer"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                     <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Total USD:</th>
                                            <td class="text-end fw-bold">{{ formatCurrency(totalUSD, 'USD') }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="text-end">Total FC:</th>
                                            <td class="text-end fw-bold">{{ formatCurrency(totalFC, 'FC') }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
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

    <script type="text/x-template" id="scolar-year-tuition-template">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Frais par Année Scolaire</h4>
                </div>
                <div class="card-body">
                    <div v-if="error" class="alert alert-danger">{{ error }}</div>
                    <div class="mb-3">
                        <label for="anneeScolaire" class="form-label">Sélectionner une année scolaire :</label>
                        <select id="anneeScolaire" v-model="selectedYear" @change="fetchTuitionData" class="form-select" :disabled="loading">
                            <option v-for="year in years" :key="year.id" :value="year.id">{{ year.nom }}</option>
                        </select>
                    </div>

                    <div v-if="loading" class="text-center p-5">
                        <div class="spinner-border" role="status"><span class="visually-hidden">Chargement...</span></div>
                    </div>

                    <div v-else-if="!loading && selectedYear" class="table-responsive">
                        <table class="table table-bordered table-hover tuition-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Type de Frais</th>
                                    <th v-for="classe in classes" :key="classe.id">{{ classe.nom }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="feeType in feeTypes" :key="feeType.id">
                                    <td class="fw-bold">{{ feeType.nom }}</td>
                                    <td v-for="classe in classes" :key="classe.id">
                                        <div class="cell-content">
                                            <span>{{ getTuitionAmount(feeType.id, classe.id) }}</span>
                                            <button v-if="getTuitionRecord(feeType.id, classe.id)" class="btn btn-link btn-sm p-0" @click="openEditModal(feeType.id, classe.id)" v-if="hasPermission('frais_annee_classe.edit')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button v-else class="btn btn-link btn-sm p-0" @click="openEditModal(feeType.id, classe.id)" v-if="hasPermission('frais_annee_classe.create')">
                                                <i class="bi bi-plus-square"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                     <div v-else class="alert alert-info">Veuillez sélectionner une année scolaire pour afficher les frais.</div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="tuitionEditModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ modalTitle }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" v-if="editingTuition">
                            <div class="mb-3">
                                <label class="form-label">Année Scolaire:</label>
                                <p class="form-control-plaintext fw-bold">{{ editingYearName }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Classe:</label>
                                <p class="form-control-plaintext fw-bold">{{ editingClassName }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type de Frais:</label>
                                <p class="form-control-plaintext fw-bold">{{ editingFeeTypeName }}</p>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label for="tuitionAmount" class="form-label">Montant</label>
                                <input type="number" class="form-control" id="tuitionAmount" v-model.number="editingTuition.montant" placeholder="0.00">
                            </div>
                            <div class="mb-3">
                                <label for="tuitionCurrency" class="form-label">Devise</label>
                                <select class="form-select" id="tuitionCurrency" v-model="editingTuition.devise">
                                    <option>USD</option>
                                    <option>FC</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-danger me-auto" @click="deleteTuition" v-if="editingTuition && editingTuition.id && hasPermission('frais_annee_classe.delete')">
                                <i class="bi bi-trash"></i> Supprimer
                            </button>
                            <button type="button" class="btn btn-primary" @click="saveTuition" :disabled="!editingTuition || editingTuition.montant <= 0">
                                <i class="bi bi-check-circle"></i> Enregistrer
                            </button>
                        </div>
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
        const API_BASE_URL = '<?php echo base_url(); // replaceAll with your actual API base URL ?>'; 
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
          ['user', { tablename: 'user', displayname: 'Utilisateurs', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'username', type: 'text', label: 'Nom d\'utilisateur'}, {name: 'password', type: 'text', label: 'Mot de passe', editable: false}, {name: 'phone', type: 'text', label: 'Téléphone'}, {name: 'access_token', type: 'text', label: 'Token d\'accès', editable: false}, {name: 'email', type: 'text', label: 'Email'}, {name: 'nom_complet', type: 'text', label: 'Nom Complet'}, {name: 'est_actif', type: 'enum', label: 'Actif', isEnum: true, enumValues: ['ACTIF','INACTIF']}, {name: 'date_creation', type: 'text', label: 'Créé le', editable: false} ] }],
          ['eleve', { tablename: 'eleve', displayname: 'Élèves', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom'}, {name: 'postnom', type: 'text', label: 'Postnom'}, {name: 'prenom', type: 'text', label: 'Prénom'}, {name: 'date_naissance', type: 'date', label: 'Date Naissance'}, {name: 'genre', type: 'enum', label: 'Genre', isEnum: true, enumValues: ['M','F']}, {name: 'adresse', type: 'text', label: 'Adresse'}, {name: 'telephone_parent', type: 'text', label: 'Tél. Parent'}, {name: 'date_inscription', type: 'date', label: 'Inscrit le'}, {name: 'est_actif', type: 'enum', label: 'Actif', isEnum: true, enumValues: ['ACTIF','INACTIF']} ] }],
          ['paiement', { tablename: 'paiement', displayname: 'Paiements', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'montant', type: 'decimal', label: 'Montant'}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['FC','USD']}, {name: 'fk_frais', type: 'int', label: 'Frais', foreignKey: {relatedTable: 'frais_annee_classe', displayField: 'id', valueField: 'id'}}, {name: 'fk_eleve', type: 'int', label: 'Élève', foreignKey: {relatedTable: 'eleve', displayField: 'nom', valueField: 'id'}}, {name: 'date_paiement', type: 'date', label: 'Date Paiement'}, {name: 'fk_user', type: 'int', label: 'Utilisateur', foreignKey: {relatedTable: 'user', displayField: 'nom_complet', valueField: 'id'}} ] }],
          ['classe', { tablename: 'classe', displayname: 'Classes', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom'}, {name: 'niveau_numerique', type: 'int', label: 'Niveau Numérique'} ] }],
          ['annee_scolaire', { tablename: 'annee_scolaire', displayname: 'Années Scolaires', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom (ex: 2023-2024)'} ] }],
          ['cours', { tablename: 'cours', displayname: 'Cours', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom'}, {name: 'description', type: 'text', label: 'Description'} ] }],
          ['depense', { tablename: 'depense', displayname: 'Dépenses', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'date_depense', type: 'date', label: 'Date Dépense'}, {name: 'montant', type: 'decimal', label: 'Montant'}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['FC','USD']}, {name: 'motif', type: 'text', label: 'Motif'}, {name: 'fk_user', type: 'int', label: 'Utilisateur', foreignKey: {relatedTable: 'user', displayField: 'nom_complet', valueField: 'id'}} ] }],
          ['eleve_classe_annee', { tablename: 'eleve_classe_annee', displayname: 'Inscriptions', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fk_eleve', type: 'int', label: 'Élève', foreignKey: {relatedTable: 'eleve', displayField: 'nom', valueField: 'id'}}, {name: 'fk_classe', type: 'int', label: 'Classe', foreignKey: {relatedTable: 'classe', displayField: 'nom', valueField: 'id'}}, {name: 'fk_annee', type: 'int', label: 'Année Scolaire', foreignKey: {relatedTable: 'annee_scolaire', displayField: 'nom', valueField: 'id'}} ] }],
          ['frais_annee_classe', { tablename: 'frais_annee_classe', displayname: 'Frais par Classe/Année', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fk_type_frais', type: 'int', label: 'Type de Frais', foreignKey: {relatedTable: 'type_frais', displayField: 'nom', valueField: 'id'}}, {name: 'montant', type: 'decimal', label: 'Montant'}, {name: 'devise', type: 'enum', label: 'Devise', isEnum: true, enumValues: ['FC','USD']}, {name: 'fk_classe', type: 'int', label: 'Classe', foreignKey: {relatedTable: 'classe', displayField: 'nom', valueField: 'id'}}, {name: 'fk_annee', type: 'int', label: 'Année Scolaire', foreignKey: {relatedTable: 'annee_scolaire', displayField: 'nom', valueField: 'id'}} ] }],
          ['module', { tablename: 'module', displayname: 'Modules', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom'}, {name: 'description', type: 'text', label: 'Description'} ] }],
          ['permission', { tablename: 'permission', displayname: 'Permissions', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fk_module', type: 'int', label: 'Module', foreignKey: {relatedTable: 'module', displayField: 'nom', valueField: 'id'}}, {name: 'nom', type: 'text', label: 'Nom'}, {name: 'code', type: 'text', label: 'Code'} ] }],
          ['tranche_frais', { tablename: 'tranche_frais', displayname: 'Tranches de Frais', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom'}, {name: 'pourcentage', type: 'decimal', label: 'Pourcentage'}, {name: 'fk_frais', type: 'int', label: 'Frais', foreignKey: {relatedTable: 'frais_annee_classe', displayField: 'id', valueField: 'id'}}, {name: 'date_limite', type: 'date', label: 'Date Limite'} ] }],
          ['type_frais', { tablename: 'type_frais', displayname: 'Types de Frais', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'nom', type: 'text', label: 'Nom'} ] }],
          ['user_permission', { tablename: 'user_permission', displayname: 'Permissions Utilisateur', fields: [ {name: 'id', type: 'int', label: 'ID', editable: false}, {name: 'fk_user', type: 'int', label: 'Utilisateur', foreignKey: {relatedTable: 'user', displayField: 'nom_complet', valueField: 'id'}}, {name: 'fk_permission', type: 'int', label: 'Permission', foreignKey: {relatedTable: 'permission', displayField: 'nom', valueField: 'id'}} ] }],
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

                        

                        if (userPermissions && allPermissions) {

                            // if there is permissions

                            permissionIds = userPermissions.map(p => p.fk_permission);

                            var codes = [];

                            for (var i = allPermissions.length - 1; i >= 0; i--) {
                                var id = allPermissions[i].id
                                if(permissionIds.indexOf(id)) {
                                    codes.append(allPermissions[i].code)
                                }
                            }

                            localStorage.setItem('user_permissions', JSON.stringify(codes));
                            authState.userPermissions = codes;
                            console.log(codes)
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

                        if (response.ok && result && result.id && result.access_token) {
                            localStorage.setItem('access_token', result.access_token);
                            localStorage.setItem('user_id', result.id);
                            
                            // Fetch permissions after successful login
                            await fetchPermissions(result.id);

                            Object.assign(authState, { 
                                accessToken: result.access_token, 
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
                const formatTableName = (name) => (tableMetadata.get(name) || { displayname: name.replaceAll(/_/g, ' ') }).displayname;
                
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

                const currentTableMeta = computed(() => tableMetadata.get(props.tableName) || { tablename: props.tableName, displayname: props.tableName.replaceAll(/_/g, ' '), fields: [] });
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
                        const result = await apiCall(props.tableName.replaceAll("_",""));
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
                            const result = await apiCall(table.replaceAll("_",""));
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
                        
                        const added = await apiCall(props.tableName.replaceAll("_",""), 'POST', payload);
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
                        await apiCall(`${props.tableName.replaceAll("_","")}/${id}`, 'DELETE');
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
                        await apiCall(`${props.tableName.replaceAll("_","")}/${item.id}`, 'PUT', { [fieldName]: newValue });
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
                const relatedData = reactive({ eleve: [], classe: [], frais_annee_classe: [], user: [], type_frais: [] });
                const paymentDate = ref(new Date().toISOString().slice(0, 10));

                const years = ref([]);
                const selectedYear = ref(null);
                const pupilSearch = reactive({ nom: '', prenom: '' });
                const searchResults = ref([]);
                const searchLoading = ref(false);
                const selectedPupil = ref({});
                const pupilClass = ref({});
                const availableTuitions = ref([]);
                const newPayment = reactive({
                    fk_eleve: null,
                    fk_frais: null,
                    fk_classe: null,
                    montant: null,
                    devise: 'USD'
                });
                const paymentLoading = ref(false);

                const fetchInitialData = async () => {
                    try {
                        const [yearsData, classesData, feeTypesData, usersData] = await Promise.all([
                            apiCall('anneescolaire'),
                            apiCall('classe'),
                            apiCall('typefrais'),
                            apiCall('user'),
                        ]);
                        years.value = yearsData || [];
                        relatedData.classe = classesData || [];
                        relatedData.type_frais = feeTypesData || [];
                        relatedData.user = usersData || [];
                        if (years.value.length > 0) {
                            selectedYear.value = years.value[years.value.length - 1].id;
                        }
                    } catch (e) {
                        error.value = `Erreur de chargement des données: ${e.message}`;
                    }
                };

                const fetchPayments = async () => {
                    loading.value = true;
                    error.value = null;
                    try {
                        const result = await apiCall(`search`, 'POST', {
                            table: 'paiement',
                            where: { date_paiement: paymentDate.value }
                        });
                        payments.value = result || [];
                        await fetchRelatedPaymentData();
                    } catch (e) {
                        error.value = `Erreur: ${e.message}`;
                    } finally {
                        loading.value = false;
                    }
                };

                const fetchRelatedPaymentData = async () => {
                    const pupilIds = [...new Set(payments.value.map(p => p.fk_eleve))];
                    const feeIds = [...new Set(payments.value.map(p => p.fk_frais))];

                    if (pupilIds.length > 0) {
                        const pupilsData = await apiCall('search', 'POST', { table: 'eleve', where_in: { id: pupilIds } });
                        relatedData.eleve = pupilsData || [];
                    }
                    if (feeIds.length > 0) {
                        const feesData = await apiCall('search', 'POST', { table: 'frais_annee_classe', where_in: { id: feeIds } });
                        relatedData.frais_annee_classe = feesData || [];
                    }
                };

                const searchPupils = async () => {
                    if (!pupilSearch.nom && !pupilSearch.prenom) return;
                    searchLoading.value = true;
                    try {
                        const payload = {
                            table: "eleve",
                            like: {},
                            limit: 20
                        };
                        if(pupilSearch.nom) payload.like.nom = pupilSearch.nom;
                        if(pupilSearch.prenom) payload.like.prenom = pupilSearch.prenom;

                        const result = await apiCall('search', 'POST', payload);
                        searchResults.value = result || [];
                    } catch (e) {
                        window.addNotification("Erreur recherche d'élève", 'danger');
                    } finally {
                        searchLoading.value = false;
                    }
                };

                const selectPupil = async (pupil) => {
                    selectedPupil.value = pupil;
                    searchResults.value = [];
                    pupilClass.value = {};
                    availableTuitions.value = [];
                    newPayment.fk_frais = null;
                    newPayment.montant = null;

                    if (!selectedYear.value) {
                        window.addNotification("Veuillez d'abord sélectionner une année scolaire", "danger");
                        return;
                    }
                    
                    try {
                        // Find pupil's class for the selected year
                        const inscriptionData = await apiCall('search', 'POST', {
                            table: 'eleve_classe_annee',
                            where: { fk_eleve: pupil.id, fk_annee: selectedYear.value },
                            limit: 1
                        });

                        if (inscriptionData && inscriptionData.length > 0) {
                            const inscription = inscriptionData[0];
                            newPayment.fk_classe = inscription.fk_classe;
                            pupilClass.value = relatedData.classe.find(c => c.id === inscription.fk_classe) || {};

                            // Fetch available tuitions for that class and year
                            const tuitionsData = await apiCall('search', 'POST', {
                                table: 'frais_annee_classe',
                                where: { fk_classe: inscription.fk_classe, fk_annee: selectedYear.value }
                            });
                            availableTuitions.value = tuitionsData || [];
                        } else {
                            window.addNotification("Cet élève n'est pas inscrit dans une classe pour l'année sélectionnée.", "warning");
                        }
                    } catch(e) {
                        window.addNotification("Erreur lors de la récupération des détails de l'élève.", "danger");
                    }
                };

                const makePayment = async () => {
                    paymentLoading.value = true;
                    try {
                        const payload = {
                            ...newPayment,
                            fk_eleve: selectedPupil.value.id,
                            date_paiement: new Date().toISOString().slice(0, 10),
                            fk_user: authState.userId
                        };
                        await apiCall('paiement', 'POST', payload);
                        window.addNotification('Paiement enregistré!', 'success');
                        
                        // Reset form
                        selectedPupil.value = {};
                        pupilClass.value = {};
                        availableTuitions.value = [];
                        Object.assign(newPayment, { fk_eleve: null, fk_frais: null, fk_classe: null, montant: null, devise: 'USD' });

                        await fetchPayments(); // Refresh list
                    } catch(e) {
                         window.addNotification(`Erreur: ${e.message}`, 'danger');
                    } finally {
                        paymentLoading.value = false;
                    }
                };

                const getRelatedName = (id, table) => {
                    const item = (relatedData[table] || []).find(d => d.id == id);
                    if (!item) return `ID: ${id}`;
                    if (table === 'eleve') return `${item.nom || ''} ${item.postnom || ''} ${item.prenom || ''}`.trim();
                    if (table === 'user') return item.nom_complet || item.username;
                    return item.nom || `ID: ${id}`;
                };

                const getFeeTypeNameFromPayment = (payment) => {
                    const fee = (relatedData.frais_annee_classe || []).find(f => f.id === payment.fk_frais);
                    return fee ? getRelatedName(fee.fk_type_frais, 'type_frais') : 'N/A';
                };
                
                const formatCurrency = (amount, currency) => {
                    if (amount === null || amount === undefined) return '';
                    currency = currency == 'FC'? 'CDF' : currency;
                    return new Intl.NumberFormat('fr-CD', { style: 'currency', currency: currency || 'USD' }).format(amount || 0);
                };

                const printReceipt = (payment) => {
                    const schoolName = "COMPLEXE SCOLAIRE ELIE"; // Replace with your school name
                    const receiptWindow = window.open('', 'PRINT', 'height=600,width=800');

                    const pupilName = getRelatedName(payment.fk_eleve, 'eleve');
                    const className = getRelatedName(payment.fk_classe, 'classe');
                    const feeName = getFeeTypeNameFromPayment(payment);
                    const cashierName = getRelatedName(payment.fk_user, 'user');
                    const amountPaid = formatCurrency(payment.montant, payment.devise);

                    receiptWindow.document.write('<html><head><title>Reçu de Paiement</title>');
                    receiptWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">');
                    receiptWindow.document.write('<style>body { font-family: sans-serif; padding: 20px; } .receipt-container { border: 1px solid #ccc; padding: 20px; max-width: 600px; margin: auto; } h2, h3 { text-align: center; } table { width: 100%; margin-top: 20px; } td { padding: 5px; } .text-end { text-align: right; } .fw-bold { font-weight: bold; } .mt-5 { margin-top: 3rem; }</style>');
                    receiptWindow.document.write('</head><body>');
                    receiptWindow.document.write('<div class="receipt-container">');
                    receiptWindow.document.write(`<h2>${schoolName}</h2>`);
                    receiptWindow.document.write('<h3>Reçu de Paiement</h3>');
                    receiptWindow.document.write(`<p class="text-end">Date: ${new Date(payment.date_paiement).toLocaleDateString()}</p>`);
                    receiptWindow.document.write(`<p>Reçu N°: ${payment.id}</p>`);
                    receiptWindow.document.write('<hr>');
                    receiptWindow.document.write('<table>');
                    receiptWindow.document.write(`<tr><td>Élève:</td><td class="fw-bold">${pupilName}</td></tr>`);
                    receiptWindow.document.write(`<tr><td>Classe:</td><td>${className}</td></tr>`);
                    receiptWindow.document.write(`<tr><td>Motif du paiement:</td><td>${feeName}</td></tr>`);
                    receiptWindow.document.write(`<tr><td class="fw-bold">Montant Payé:</td><td class="fw-bold text-end">${amountPaid}</td></tr>`);
                    receiptWindow.document.write('</table>');
                    receiptWindow.document.write('<hr>');
                    receiptWindow.document.write(`<p>Perçu par: ${cashierName}</p>`);
                    receiptWindow.document.write('<p class="mt-5 text-end">Signature: ___________________</p>');
                    receiptWindow.document.write('</div>');
                    receiptWindow.document.write('</body></html>');
                    receiptWindow.document.close();
                    receiptWindow.focus();
                    
                    setTimeout(() => { // Timeout to ensure content is loaded
                        receiptWindow.print();
                        receiptWindow.close();
                    }, 250);
                };

                const totalUSD = computed(() => payments.value.filter(p => p.devise === 'USD').reduce((sum, p) => sum + parseFloat(p.montant), 0));
                const totalFC = computed(() => payments.value.filter(p => p.devise === 'FC').reduce((sum, p) => sum + parseFloat(p.montant), 0));

                onMounted(() => {
                    fetchInitialData();
                    fetchPayments();
                });

                watch(paymentDate, fetchPayments);

                return { loading, error, payments, fetchPayments, getRelatedName, formatCurrency, totalUSD, totalFC, paymentDate, years, selectedYear, pupilSearch, searchResults, searchPupils, searchLoading, selectPupil, selectedPupil, pupilClass, availableTuitions, newPayment, makePayment, paymentLoading, getFeeTypeNameFromPayment, printReceipt };
            }
        };

        const Reports = {
            template: '#reports-template',
            setup() {
                // Logic for reports can be added here in the future
                return {};
            }
        };

        const ScolarYearTuition = {
            template: '#scolar-year-tuition-template',
            setup() {
                const loading = ref(true);
                const error = ref(null);
                const selectedYear = ref(null);
                const years = ref([]);
                const classes = ref([]);
                const feeTypes = ref([]);
                const tuitionData = ref([]);
                const editingTuition = ref(null);
                let modalInstance = null;

                const hasPermission = (permissionCode) => {
                    if (authState.userId == 1) return true;
                    return (authState.userPermissions || []).includes(permissionCode);
                };

                const formatCurrency = (amount, currency) => {
                    if (amount === null || amount === undefined) return 'N/A';
                    currency = currency == 'FC' ? 'CDF' : currency;
                    return new Intl.NumberFormat('fr-CD', { style: 'currency', currency: currency || 'USD' }).format(amount);
                };
                
                const fetchInitialData = async () => {
                    try {
                        const [yearsData, classesData, feeTypesData] = await Promise.all([
                            apiCall('anneescolaire'),
                            apiCall('classe'),
                            apiCall('typefrais')
                        ]);
                        years.value = yearsData || [];
                        classes.value = (classesData || []).sort((a,b) => a.niveau_numerique - b.niveau_numerique);
                        feeTypes.value = feeTypesData || [];
                        if (years.value.length > 0) {
                            selectedYear.value = years.value[years.value.length - 1].id;
                            await fetchTuitionData();
                        }
                    } catch (e) {
                        error.value = `Erreur de chargement des données initiales: ${e.message}`;
                    } finally {
                        loading.value = false;
                    }
                };

                const fetchTuitionData = async () => {
                    if (!selectedYear.value) return;
                    loading.value = true;
                    error.value = null;
                    try {
                        const payload = { table: 'frais_annee_classe', where: { fk_annee: selectedYear.value } };
                        const result = await apiCall('search', 'POST', payload);
                        tuitionData.value = result || [];
                    } catch (e) {
                        error.value = `Erreur lors de la récupération des frais: ${e.message}`;
                    } finally {
                        loading.value = false;
                    }
                };
                
                const getTuitionRecord = (feeTypeId, classeId) => {
                     return tuitionData.value.find(d => d.fk_type_frais == feeTypeId && d.fk_classe == classeId);
                };

                const getTuitionAmount = (feeTypeId, classeId) => {
                    const record = getTuitionRecord(feeTypeId, classeId);
                    return record ? formatCurrency(record.montant, record.devise) : '--';
                };

                const openEditModal = (feeTypeId, classeId) => {
                    const existingRecord = getTuitionRecord(feeTypeId, classeId);
                    if (existingRecord) {
                        editingTuition.value = { ...existingRecord }; // Edit existing
                    } else {
                        editingTuition.value = { // Create new
                            id: null,
                            fk_type_frais: feeTypeId,
                            fk_classe: classeId,
                            fk_annee: selectedYear.value,
                            montant: null,
                            devise: 'USD'
                        };
                    }
                    modalInstance.show();
                };

                const saveTuition = async () => {
                    const tuition = editingTuition.value;
                    if (!tuition || !tuition.montant || tuition.montant <= 0) {
                        window.addNotification('Le montant doit être un nombre positif.', 'danger');
                        return;
                    }
                    
                    try {
                        if (tuition.id) { // Update
                             if (!hasPermission('frais_annee_classe.edit')) throw new Error('Droits insuffisants pour modifier.');
                            await apiCall(`fraisanneeclasse/${tuition.id}`, 'PUT', { montant: tuition.montant, devise: tuition.devise });
                        } else { // Create
                             if (!hasPermission('frais_annee_classe.create')) throw new Error('Droits insuffisants pour créer.');
                            const payload = { ...tuition };
                            delete payload.id;
                            await apiCall('fraisanneeclasse', 'POST', payload);
                        }
                        window.addNotification('Frais enregistré avec succès!', 'success');
                        modalInstance.hide();
                        await fetchTuitionData(); // Refresh data
                    } catch (e) {
                        window.addNotification(`Erreur: ${e.message}`, 'danger');
                    }
                };

                const deleteTuition = async () => {
                    const tuitionId = editingTuition.value?.id;
                    if (!tuitionId || !hasPermission('frais_annee_classe.delete')) return;
                    
                    if (confirm('Êtes-vous sûr de vouloir supprimer ces frais ?')) {
                        try {
                            await apiCall(`fraisanneeclasse/${tuitionId}`, 'DELETE');
                            window.addNotification('Frais supprimé.', 'success');
                            modalInstance.hide();
                            await fetchTuitionData(); // Refresh data
                        } catch (e) {
                             window.addNotification(`Erreur: ${e.message}`, 'danger');
                        }
                    }
                };

                const modalTitle = computed(() => (editingTuition.value?.id ? 'Modifier' : 'Ajouter') + ' Frais Scolaire');
                const editingYearName = computed(() => years.value.find(y => y.id == selectedYear.value)?.nom || '');
                const editingClassName = computed(() => classes.value.find(c => c.id == editingTuition.value?.fk_classe)?.nom || '');
                const editingFeeTypeName = computed(() => feeTypes.value.find(f => f.id == editingTuition.value?.fk_type_frais)?.nom || '');

                onMounted(() => {
                    const modalEl = document.getElementById('tuitionEditModal');
                    if(modalEl) modalInstance = new bootstrap.Modal(modalEl);
                    fetchInitialData();
                });

                return { loading, error, selectedYear, years, classes, feeTypes, fetchTuitionData, getTuitionAmount, getTuitionRecord, openEditModal, editingTuition, saveTuition, deleteTuition, modalTitle, editingYearName, editingClassName, editingFeeTypeName, hasPermission };
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
                { path: '/scolar-year-tuition', component: ScolarYearTuition },
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
