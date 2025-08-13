<?php

/**
 * @param array $pathsData Le tableau de chemins et de requêtes SQL.
 * @param string $sourceTable Le nom de la table de départ.
 * @param string $finalTable Le nom de la table d'arrivée.
 * @return array Un tableau contenant toutes les chaînes de requêtes SQL trouvées.
 */
function getSqlQueriesByTables(array $pathsData, string $sourceTable, string $finalTable): array
{
    $results = [];
    foreach ($pathsData as $pathInfo) {
        if ($pathInfo['sourcetable'] === $sourceTable && $pathInfo['finaltable'] === $finalTable) {
            foreach ($pathInfo['paths'] as $path) {
                $results[] = $path['sql_query'];
            }
        }
    }
    return $results;
}

$myPaths = '[
    {
        "sourcetable": "user",
        "finaltable": "client",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "user",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM user INNER JOIN client ON user.fkclient = client.id;"
            }
        ]
    },
    {
        "sourcetable": "user",
        "finaltable": "agent",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "user",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM user INNER JOIN agent ON user.fkagent = agent.id;"
            }
        ]
    },
    {
        "sourcetable": "produit",
        "finaltable": "categorie_prod",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "produit",
                        "source_field": "fkcategorie_prod",
                        "destination_table": "categorie_prod",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM produit INNER JOIN categorie_prod ON produit.fkcategorie_prod = categorie_prod.id;"
            }
        ]
    },
    {
        "sourcetable": "commande",
        "finaltable": "user",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "commande",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM commande INNER JOIN adresse ON commande.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_create = user.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "commande",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM commande INNER JOIN adresse ON commande.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_validate = user.id;"
            }
        ]
    },
    {
        "sourcetable": "commande",
        "finaltable": "client",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "commande",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM commande INNER JOIN client ON commande.fkclient = client.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "commande",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM commande INNER JOIN adresse ON commande.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_create = user.id INNER JOIN client ON user.fkclient = client.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "commande",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM commande INNER JOIN adresse ON commande.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_validate = user.id INNER JOIN client ON user.fkclient = client.id;"
            }
        ]
    },
    {
        "sourcetable": "commande",
        "finaltable": "adresse",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "commande",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM commande INNER JOIN adresse ON commande.fkadresse = adresse.id;"
            }
        ]
    },
    {
        "sourcetable": "commande",
        "finaltable": "agent",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "commande",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM commande INNER JOIN adresse ON commande.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_create = user.id INNER JOIN agent ON user.fkagent = agent.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "commande",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM commande INNER JOIN adresse ON commande.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_validate = user.id INNER JOIN agent ON user.fkagent = agent.id;"
            }
        ]
    },
    {
        "sourcetable": "achat",
        "finaltable": "user",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "achat",
                        "source_field": "fkfournisseur",
                        "destination_table": "fournisseur",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM achat INNER JOIN fournisseur ON achat.fkfournisseur = fournisseur.id INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_create = user.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "achat",
                        "source_field": "fkfournisseur",
                        "destination_table": "fournisseur",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM achat INNER JOIN fournisseur ON achat.fkfournisseur = fournisseur.id INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_validate = user.id;"
            }
        ]
    },
    {
        "sourcetable": "achat",
        "finaltable": "client",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "achat",
                        "source_field": "fkfournisseur",
                        "destination_table": "fournisseur",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM achat INNER JOIN fournisseur ON achat.fkfournisseur = fournisseur.id INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_create = user.id INNER JOIN client ON user.fkclient = client.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "achat",
                        "source_field": "fkfournisseur",
                        "destination_table": "fournisseur",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM achat INNER JOIN fournisseur ON achat.fkfournisseur = fournisseur.id INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_validate = user.id INNER JOIN client ON user.fkclient = client.id;"
            }
        ]
    },
    {
        "sourcetable": "achat",
        "finaltable": "adresse",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "achat",
                        "source_field": "fkfournisseur",
                        "destination_table": "fournisseur",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM achat INNER JOIN fournisseur ON achat.fkfournisseur = fournisseur.id INNER JOIN adresse ON fournisseur.fkadresse = adresse.id;"
            }
        ]
    },
    {
        "sourcetable": "achat",
        "finaltable": "agent",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "achat",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM achat INNER JOIN agent ON achat.fkagent = agent.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "achat",
                        "source_field": "fkfournisseur",
                        "destination_table": "fournisseur",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM achat INNER JOIN fournisseur ON achat.fkfournisseur = fournisseur.id INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_create = user.id INNER JOIN agent ON user.fkagent = agent.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "achat",
                        "source_field": "fkfournisseur",
                        "destination_table": "fournisseur",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM achat INNER JOIN fournisseur ON achat.fkfournisseur = fournisseur.id INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_validate = user.id INNER JOIN agent ON user.fkagent = agent.id;"
            }
        ]
    },
    {
        "sourcetable": "achat",
        "finaltable": "fournisseur",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "achat",
                        "source_field": "fkfournisseur",
                        "destination_table": "fournisseur",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM achat INNER JOIN fournisseur ON achat.fkfournisseur = fournisseur.id;"
            }
        ]
    },
    {
        "sourcetable": "adresse",
        "finaltable": "user",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM adresse INNER JOIN user ON adresse.fkuser_create = user.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM adresse INNER JOIN user ON adresse.fkuser_validate = user.id;"
            }
        ]
    },
    {
        "sourcetable": "adresse",
        "finaltable": "client",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM adresse INNER JOIN user ON adresse.fkuser_create = user.id INNER JOIN client ON user.fkclient = client.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM adresse INNER JOIN user ON adresse.fkuser_validate = user.id INNER JOIN client ON user.fkclient = client.id;"
            }
        ]
    },
    {
        "sourcetable": "adresse",
        "finaltable": "agent",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM adresse INNER JOIN user ON adresse.fkuser_create = user.id INNER JOIN agent ON user.fkagent = agent.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM adresse INNER JOIN user ON adresse.fkuser_validate = user.id INNER JOIN agent ON user.fkagent = agent.id;"
            }
        ]
    },
    {
        "sourcetable": "produit_caracteristique",
        "finaltable": "produit",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "produit_caracteristique",
                        "source_field": "fkproduit",
                        "destination_table": "produit",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM produit_caracteristique INNER JOIN produit ON produit_caracteristique.fkproduit = produit.id;"
            }
        ]
    },
    {
        "sourcetable": "produit_caracteristique",
        "finaltable": "categorie_prod",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "produit_caracteristique",
                        "source_field": "fkproduit",
                        "destination_table": "produit",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "produit",
                        "source_field": "fkcategorie_prod",
                        "destination_table": "categorie_prod",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM produit_caracteristique INNER JOIN produit ON produit_caracteristique.fkproduit = produit.id INNER JOIN categorie_prod ON produit.fkcategorie_prod = categorie_prod.id;"
            }
        ]
    },
    {
        "sourcetable": "fournisseur",
        "finaltable": "adresse",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM fournisseur INNER JOIN adresse ON fournisseur.fkadresse = adresse.id;"
            }
        ]
    },
    {
        "sourcetable": "fournisseur",
        "finaltable": "user",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM fournisseur INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_create = user.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM fournisseur INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_validate = user.id;"
            }
        ]
    },
    {
        "sourcetable": "fournisseur",
        "finaltable": "client",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM fournisseur INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_create = user.id INNER JOIN client ON user.fkclient = client.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM fournisseur INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_validate = user.id INNER JOIN client ON user.fkclient = client.id;"
            }
        ]
    },
    {
        "sourcetable": "fournisseur",
        "finaltable": "agent",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_create",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM fournisseur INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_create = user.id INNER JOIN agent ON user.fkagent = agent.id;"
            },
            {
                
                "join_conditions": [
                    {
                        "source_table": "fournisseur",
                        "source_field": "fkadresse",
                        "destination_table": "adresse",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "adresse",
                        "source_field": "fkuser_validate",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM fournisseur INNER JOIN adresse ON fournisseur.fkadresse = adresse.id INNER JOIN user ON adresse.fkuser_validate = user.id INNER JOIN agent ON user.fkagent = agent.id;"
            }
        ]
    },
    {
        "sourcetable": "user_device",
        "finaltable": "user",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "user_device",
                        "source_field": "fkuser",
                        "destination_table": "user",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM user_device INNER JOIN user ON user_device.fkuser = user.id;"
            }
        ]
    },
    {
        "sourcetable": "user_device",
        "finaltable": "client",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "user_device",
                        "source_field": "fkuser",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkclient",
                        "destination_table": "client",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM user_device INNER JOIN user ON user_device.fkuser = user.id INNER JOIN client ON user.fkclient = client.id;"
            }
        ]
    },
    {
        "sourcetable": "user_device",
        "finaltable": "agent",
        "paths": [
            {
                
                "join_conditions": [
                    {
                        "source_table": "user_device",
                        "source_field": "fkuser",
                        "destination_table": "user",
                        "destination_field": "id"
                    },
                    {
                        "source_table": "user",
                        "source_field": "fkagent",
                        "destination_table": "agent",
                        "destination_field": "id"
                    }
                ],
                "sql_query": "SELECT * FROM user_device INNER JOIN user ON user_device.fkuser = user.id INNER JOIN agent ON user.fkagent = agent.id;"
            }
        ]
    }
]';

// Exemple d'utilisation de la fonction
// Les tables de départ et d'arrivée sont 'user' et 'client'
$queries = getSqlQueriesByTables(json_decode($myPaths, true), 'user', 'client');
print_r($queries);
