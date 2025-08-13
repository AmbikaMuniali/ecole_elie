<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gérer les Droits des Agents</title>
  <style>
    body {
  font-family: Arial, sans-serif;
  margin: 20px;
  background-color: #f4f4f4;
}
.container {
  background-color: #fff;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  max-width: 800px;
  margin: auto;
}
h1, h2 {
  color: #333;
  text-align: center;
}
.form-group {
  margin-bottom: 20px;
}
label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
  color: #555;
}
select, input[type="submit"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
}
select {
  background-color: #f9f9f9;
}
.module-group {
  margin-bottom: 25px;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  padding: 15px;
  background-color: #fdfdfd;
}
.module-group h3 {
  margin-top: 0;
  color: #007bff;
  border-bottom: 1px solid #eee;
  padding-bottom: 10px;
  margin-bottom: 15px;
}
.droits-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 10px;
}
.droits-list label {
  font-weight: normal;
  cursor: pointer;
}
.droits-list input[type="checkbox"] {
  width: auto;
  margin-right: 8px;
}
.droits-list div {
  display: flex;
  align-items: baseline; /* Changed from center to baseline */
}
.btn-submit {
  background-color: #28a745;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s ease;
}
.btn-submit:hover {
  background-color: #218838;
}
.message {
  margin-top: 20px;
  padding: 10px;
  border-radius: 4px;
  text-align: center;
}
.message.success {
  background-color: #d4edda;
  color: #155724;
  border-color: #c3e6cb;
}
.message.error {
  background-color: #f8d7da;
  color: #721c24;
  border-color: #f5c6cb;
}
  </style>
</head>
<body>
  <div class="container">
    <h1>Gérer les Droits des Agents</h1>

    <div class="form-group">
      <label for="agentSelect">Sélectionner un Agent :</label>
      <select id="agentSelect"></select>
    </div>

    <form id="droitsAgentForm">
      <h2>Droits disponibles</h2>
      <div id="droitsContainer">
        <p>Chargement des droits...</p>
      </div>
      <div class="form-group">
        <input type="submit" value="Enregistrer les Droits" class="btn-submit">
      </div>
    </form>
    <div id="message" class="message" style="display:none;"></div>
  </div>

  <script>
    const baseUrl = "<?=base_url()?>/";

    document.addEventListener('DOMContentLoaded', () => {
      fetchAgents();
      fetchDroitsAndModules();

      // Add event listener for agent selection change
      document.getElementById('agentSelect').addEventListener('change', (event) => {
        const selectedAgentId = event.target.value;
        loadAgentSpecificRights(selectedAgentId);
      });
    });

    async function fetchAgents() {
  try {
    const response = await fetch(`${baseUrl}agent`);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    const agents = data.agents;
    const agentSelect = document.getElementById('agentSelect');

    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = '--- Sélectionner un agent ---';
    agentSelect.appendChild(defaultOption);

    if (agents && agents.length > 0) {
      for (const agent of agents) {
        // Fetch the user associated with this agent
        const userResponse = await fetch(`${baseUrl}/search`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            where: {
              fkagent: agent.id
            },
            table: 'user'
          })
        });

        if (userResponse.ok) {
          const userData = await userResponse.json();
          const user = userData.result && userData.result.length > 0 ? userData.result[0] : null;

          const option = document.createElement('option');
          option.value = agent.id;

          // Display the username if found, otherwise use the full name from the agent table
          if (user && user.username) {
            option.textContent = user.username;
          } else {
            option.textContent = agent.name_complet && agent.name_complet.trim() !== '' ? agent.name_complet : 'Nom non disponible';
          }
          agentSelect.appendChild(option);
        } else {
          console.error(`Failed to fetch user for agent ID ${agent.id}`);
          const option = document.createElement('option');
          option.value = agent.id;
          option.textContent = agent.name_complet && agent.name_complet.trim() !== '' ? agent.name_complet : 'Erreur de chargement du nom';
          agentSelect.appendChild(option);
        }
      }
    } else {
      agentSelect.innerHTML += '<option value="">Aucun agent trouvé</option>';
    }
  } catch (error) {
    console.error('Erreur lors de la récupération des agents:', error);
    const agentSelect = document.getElementById('agentSelect');
    agentSelect.innerHTML = '<option value="">Erreur de chargement des agents</option>';
  }
}

    async function fetchDroitsAndModules() {
      try {
        const droitsResponse = await fetch(`${baseUrl}droits`);
        const modulesResponse = await fetch(`${baseUrl}module`);

        if (!droitsResponse.ok) {
          throw new Error(`HTTP error! status: ${droitsResponse.status} for droits`);
        }
        if (!modulesResponse.ok) {
          throw new Error(`HTTP error! status: ${modulesResponse.status} for modules`);
        }

        const droitsData = await droitsResponse.json();
        const modulesData = await modulesResponse.json();

        const droits = droitsData.droitss; // API returns 'droitss' key
        const modules = modulesData.modules; // API returns 'modules' key

        const droitsContainer = document.getElementById('droitsContainer');
        droitsContainer.innerHTML = ''; // Clear loading message

        if (modules && modules.length > 0) {
          modules.forEach(module => {
            const moduleGroup = document.createElement('div');
            moduleGroup.classList.add('module-group');
            moduleGroup.innerHTML = `<h3>${module.name}</h3><div class="droits-list"></div>`;
            const droitsListDiv = moduleGroup.querySelector('.droits-list');

            const relatedDroits = droits.filter(droit => droit.fkmodule === module.id);

            if (relatedDroits.length > 0) {
              relatedDroits.forEach(droit => {
                const checkboxContainer = document.createElement('div');
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'droits';
                checkbox.value = droit.id;
                checkbox.id = `droit-${droit.id}`; // Add an ID for the label

                const label = document.createElement('label');
                label.htmlFor = `droit-${droit.id}`;
                label.textContent = `${droit.name} (${droit.code})`;

                checkboxContainer.appendChild(checkbox);
                checkboxContainer.appendChild(label);
                droitsListDiv.appendChild(checkboxContainer);

                // Add event listener to the checkbox for console log
                checkbox.addEventListener('change', (event) => {
                  const agentId = document.getElementById('agentSelect').value;
                  const droitId = event.target.value;
                  console.log(`Agent ID: ${agentId}, Droit ID: ${droitId}, Checked: ${event.target.checked}`);
                });
              });
            } else {
              droitsListDiv.innerHTML = '<p>Aucun droit pour ce module.</p>';
            }
            droitsContainer.appendChild(moduleGroup);
          });
        } else {
          droitsContainer.innerHTML = '<p>Aucun module trouvé.</p>';
        }

      } catch (error) {
        console.error('Erreur lors de la récupération des droits et modules:', error);
        document.getElementById('droitsContainer').innerHTML = '<p>Erreur de chargement des droits.</p>';
      }
    }

    async function loadAgentSpecificRights(agentId) {
      // First, uncheck all checkboxes
      document.querySelectorAll('input[name="droits"]').forEach(checkbox => {
        checkbox.checked = false;
      });

      if (!agentId) {
        console.log('No agent selected, clearing droits checkboxes.');
        return;
      }

      try {
        // Fetch all droitsagent entries
        const response = await fetch(`${baseUrl}droitsagent`);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status} for droitsagent`);
        }
        const data = await response.json();
        const allDroitsAgents = data.droitsagents; // API returns 'droitsagents' key for all

        if (allDroitsAgents && allDroitsAgents.length > 0) {
          // Filter for the selected agent's rights
          const agentDroits = allDroitsAgents.filter(da => da.fkagent == agentId);

          agentDroits.forEach(droitAgent => {
            const checkbox = document.getElementById(`droit-${droitAgent.fkdroit}`);
            if (checkbox) {
              checkbox.checked = true;
            }
          });
        } else {
          console.log('No existing rights found for any agent.');
        }

      } catch (error) {
        console.error('Erreur lors de la récupération des droits existants de l\'agent:', error);
        showMessage(`Erreur lors du chargement des droits de l'agent: ${error.message}`, 'error');
      }
    }


    document.getElementById('droitsAgentForm').addEventListener('submit', async (event) => {
      event.preventDefault();

      const agentId = document.getElementById('agentSelect').value;
      const selectedDroits = Array.from(document.querySelectorAll('input[name="droits"]:checked')).map(cb => parseInt(cb.value));
      const messageDiv = document.getElementById('message');

      if (!agentId) {
        showMessage('Veuillez sélectionner un agent.', 'error');
        return;
      }

      // Fetch current rights for the agent to determine additions/removals
      try {
        const response = await fetch(`${baseUrl}droitsagent`);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status} for droitsagent`);
        }
        const data = await response.json();
        const allDroitsAgents = data.droitsagents;
        const currentAgentDroits = allDroitsAgents.filter(da => da.fkagent == agentId);
        const currentDroitIds = new Set(currentAgentDroits.map(da => da.fkdroit));

        // Rights to add
        const rightsToAdd = selectedDroits.filter(droitId => !currentDroitIds.has(droitId));

        // Rights to remove (optional, depends on desired behavior: delete or update status)
        // For simplicity, we'll implement deletion if a right is unchecked
        const rightsToRemove = currentAgentDroits.filter(da => !selectedDroits.includes(da.fkdroit));


        let successCount = 0;
        let errorOccurred = false;

        // Add new rights
        for (const droitId of rightsToAdd) {
          try {
            const addResponse = await fetch(`${baseUrl}droitsagent`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                fkagent: parseInt(agentId),
                fkdroit: parseInt(droitId),
                status: 'ACTIF'
              })
            });

            if (!addResponse.ok) {
              const errorData = await addResponse.json();
              throw new Error(`Failed to add droit ${droitId}: ${errorData.message || addResponse.statusText}`);
            }
            successCount++;
          } catch (error) {
            console.error('Erreur lors de l\'ajout du droit:', error);
            errorOccurred = true;
          }
        }

        // Remove unchecked rights (by deleting the droits_agent entry)
        // This assumes your API supports DELETE on a droitsagent entry by its own ID.
        // If not, you might need a custom endpoint for bulk deletion or status update.
        for (const droitToRemove of rightsToRemove) {
          try {
            // Assuming you have an endpoint like DELETE /droitsagent/[id] where [id] is the droits_agent entry's ID
            const deleteResponse = await fetch(`${baseUrl}droitsagent/${droitToRemove.id}`, {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
              }
            });

            if (!deleteResponse.ok) {
              const errorData = await deleteResponse.json();
              throw new Error(`Failed to remove droit ${droitToRemove.fkdroit}: ${errorData.message || deleteResponse.statusText}`);
            }
            successCount++;
          } catch (error) {
            console.error('Erreur lors de la suppression du droit:', error);
            errorOccurred = true;
          }
        }

        if (!errorOccurred) {
          showMessage('Droits de l\'agent mis à jour avec succès!', 'success');
        } else {
          showMessage('Certains droits n\'ont pas pu être mis à jour. Vérifiez la console pour plus de détails.', 'error');
        }

      } catch (error) {
        console.error('Erreur lors de l\'enregistrement des droits de l\'agent:', error);
        showMessage(`Erreur lors de l'enregistrement des droits: ${error.message}`, 'error');
      }
    });


    function showMessage(msg, type) {
      const messageDiv = document.getElementById('message');
      messageDiv.textContent = msg;
      messageDiv.className = `message ${type}`;
      messageDiv.style.display = 'block';
      setTimeout(() => {
        messageDiv.style.display = 'none';
      }, 5000);
    }
  </script>
</body>
</html>