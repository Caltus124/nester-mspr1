<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des machines</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            margin-left: 270px;
            margin-top: 50px;
        }
        .machine-container {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            width: 80%;
            display: flex;
            justify-content: space-around;
            align-items: center;
            transition: transform 0.3s ease; /* Ajout d'une transition pour l'effet de survol */
            font-weight: bold;

        }
        .machine-container:hover {
            transform: scale(1.1); /* Agrandir le conteneur lors du survol */
            cursor: pointer;
        }
        .machine-info {
            margin-bottom: 10px;
        }
        .machine-info span {
            font-weight: bold;
        }
        .machine-details {
            flex-grow: 1; /* Pour que le div occupe tout l'espace disponible */
        }
        .machine-image {
            margin-right: 20px;
            width: 50px;
            height: 50px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            margin-top: 50px;
        }
        .text {
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis; /* Couper le texte qui dépasse */
            white-space: nowrap; /* Empêcher le texte de passer à la ligne */
        }
        a{
            text-decoration: none;
            color: black;
        }
        .green-dot{
            color: green;
            font-size: 4em;
        }
        .red-dot{
            color: red;
            font-size: 4em;
        }
    </style>
</head>
<body>
    <h1>Agnets liste</h1>
    <div id="machineList"></div>

    <script>
        // Fonction pour charger les données de la base de données via AJAX
        function loadMachineList() {
            $.ajax({
                url: './AJAX/load_machines.php',
                type: 'GET',
                success: function(data) {
                    $('#machineList').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors du chargement des données:', status, error);
                }
            });
        }

        // Charger les données initiales lors du chargement de la page
        $(document).ready(function() {
            loadMachineList();
        });
    </script>
</body>
</html>
