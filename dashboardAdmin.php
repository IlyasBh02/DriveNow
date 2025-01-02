<?php 
require "connection.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DriveNow - Car Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Modale */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5); /* Fond semi-transparent */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Main Wrapper -->
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-blue-600 text-white">
            <div class="container mx-auto flex justify-between items-center p-4">
                <h1 class="text-2xl font-bold">DriveNow</h1>
                <nav class="space-x-4">
                    <a href="index.html" class="hover:underline">Home</a>
                    <a href="dashboard.html" class="hover:underline">Admin Dashboard</a>
                    <a href="login.html" class="hover:underline">Login</a>
                    <a href="register.html" class="hover:underline">Sign Up</a>
                </nav>
            </div>
        </header>

        <main class="flex">
            <aside class="hidden md:flex flex-col shadow-sm w-56 bg-gray-800 h-screen">
                <div class="flex gap-5 items-center pl-3 py-2 border-b-2 border-green-600">
                    <img src="./images/hire.png" class="w-8 h-8" alt="">
                    <a href="./SousAdmin/UsersDash.php" class="w-20 text-white">users</a>
                </div>
                <div class="flex gap-5 items-center pl-3 py-2 border-b-2 border-red-600">
                    <img src="./images/travel-bag.png" class="w-8 h-8" alt="">
                    <a href="./SousAdmin/VisualiserReserv.php" class="w-20 text-white">Reservation</a>
                </div>
            </aside>
            <section class="w-screen md:w-[calc(100%-224px)]">
                <div class="bg-gradient-to-r from-[#2f88da] to-[#07075a] px-5 py-3 flex justify-between w-full rounded-bl-lg rounded-br-lg">
                    <h1 class="text-white font-bold">Table des réservations</h1>
                    <div>
                        <button class="bg-green-400 text-white px-2 py-1 rounded-md" id="openCategoryModal">Ajouter une catégorie</button>
                        <button class="bg-green-400 text-white px-2 py-1 rounded-md" id="openVehiculeModal">Ajouter un véhicule</button>
                    </div>
                </div>

                <!-- Table de données des réservations -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left table-auto">
                        <thead>
                            <tr>
                                <th class="px-2 md:px-6 py-3">#</th>
                                <th class="px-2 md:px-6 py-3">userID</th>
                                <th class="px-2 md:px-6 py-3">vehiculeId</th>
                                <th class="px-2 md:px-6 py-3">Date début</th>
                                <th class="px-2 md:px-6 py-3">Date fin</th>
                                <th class="px-2 md:px-6 py-3">lieuId</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reservation)): ?>
                                <?php foreach ($reservation as $res): ?>
                                    <tr class="border-b">
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['id']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['userId']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['vehiculeId']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['date_debut']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['date_fin']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['lieuId']; ?></td>
                                        <td class="px-2 md:px-6 py-3 flex space-x-2">
                                            <a class="bg-blue-400 text-white p-3 rounded-lg" href="">Modifier</a>
                                            <a class="bg-red-400 text-white p-3 rounded-lg" href="">Supprimer</a>
                                            
                                            <!-- <a class="bg-blue-400 text-white p-3 rounded-lg" href="./FormUpdatereservation.php?reservation_id=<?php echo $res['idreservation']; ?>">Modifier</a>
                                            <a class="bg-red-400 text-white p-3 rounded-lg" href="./deletereservation.php?reservation_id=<?php echo $res['idreservation']; ?>">Supprimer</a> -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-3">Aucune réservation trouvée.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <!----------------------------------- Modale for adding category ---------------------->
        <div id="categoryModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeCategoryModal">&times;</span>
                <h2>Ajouter une catégorie</h2>
                <form action="AddCategory.php" method="POST">
                    <label for="categoryName">Nom de la catégorie:</label>
                    <input type="text" name="categoryName" id="categoryName" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Ajouter</button>
                </form>
            </div>
        </div>

        <!------------------------------------ Modale for adding vehicule -------------------------->
        <!-- Modale Ajouter véhicule -->
        <div id="vehiculeModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeVehiculeModal">&times;</span>
                <h2>Ajouter un véhicule</h2>
                <form action="AddVehicule.php" method="POST">
                    <label for="categorieId">Catégorie du véhicule:</label>
                    <select name="categorieId" id="categorieId" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                        <option value="" disabled selected>Sélectionner une catégorie</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label for="model">Modèle du véhicule:</label>
                    <input type="text" name="vehiculeModel" id="vehiculeModel" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    
                    <label for="mark">Mark:</label>
                    <input type="text" name="mark" id="mark" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    
                    <label for="prix">Prix:</label>
                    <input type="text" name="prix" id="prix" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    
                    <label for="disponabilite">Disponabilite:</label>
                    <input type="text" name="disponabilite" id="disponabilite" class="w-full p-2 border border-gray-300 rounded mb-4" required>

                    <label for="color">Disponabilite:</label>
                    <input type="text" name="disponabilite" id="disponabilite" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    
                    <label for="disponabilite">Color:</label>
                    <input type="text" name="color" id="color" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    
                    <label for="porte">Porte:</label>
                    <input type="text" name="porte" id="porte" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    
                    <label for="transmition">Transmition:</label>
                    <input type="text" name="transmition" id="transmition" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    
                    <label for="personne">Personne:</label>
                    <input type="text" name="personne" id="personne" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    
                    <label for="image">image:</label>
                    <input type="text" name="image" id="image" class="w-full p-2 border border-gray-300 rounded mb-4" required>

                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Ajouter</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        // Ouverture des modales
        const categoryModal = document.getElementById("categoryModal");
        const vehiculeModal = document.getElementById("vehiculeModal");

        document.getElementById("openCategoryModal").onclick = function() {
            categoryModal.style.display = "block";
        }

        document.getElementById("openVehiculeModal").onclick = function() {
            vehiculeModal.style.display = "block";
        }

        // Fermeture des modales
        document.getElementById("closeCategoryModal").onclick = function() {
            categoryModal.style.display = "none";
        }

        document.getElementById("closeVehiculeModal").onclick = function() {
            vehiculeModal.style.display = "none";
        }

        // Fermer la modale en cliquant en dehors de celle-ci
        window.onclick = function(event) {
            if (event.target === categoryModal) {
                categoryModal.style.display = "none";
            }
            if (event.target === vehiculeModal) {
                vehiculeModal.style.display = "none";
            }
        }
    </script>
</body>
</html>
