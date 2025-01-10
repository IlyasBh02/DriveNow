<?php 
session_start();
require "Database.php";
require "Auth.php";
$connection = new connection();
$conn = $connection->getConnection();

try {
    $sql = "SELECT * FROM reservation";
    $stmt = $conn->query($sql);
    $reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des réservations : " . $e->getMessage());
}
//upload image
function uploadImage($file, $uploadsDir = './uploads', $maxSize = 2 * 1024 * 1024, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']) {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $photoTmpName = $file['tmp_name'];
        $photoName = basename($file['name']);
        $photoSize = $file['size'];
        $photoType = mime_content_type($photoTmpName);

        if (!in_array($photoType, $allowedTypes)) {
            return ['success' => false, 'message' => "Type de fichier non supporté. Veuillez utiliser JPEG, PNG ou GIF."];
        }

        if ($photoSize > $maxSize) {
            return ['success' => false, 'message' => "Le fichier est trop volumineux. Limite de " . ($maxSize / (1024 * 1024)) . " Mo."];
        }

        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        $photoPath = $uploadsDir . uniqid() . '-' . $photoName;

        if (move_uploaded_file($photoTmpName, $photoPath)) {
            return ['success' => true, 'filePath' => $photoPath];
        } else {
            return ['success' => false, 'message' => "Erreur lors de l'upload de l'image."];
        }
    }
    return ['success' => false, 'message' => "Aucun fichier sélectionné ou erreur lors de l'upload."];
}

// Handle Category Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoryName'])) {
    try {
        $categoryName = $_POST['categoryName'];
        $stmt = $conn->prepare("INSERT INTO category (name) VALUES (?)");
        $stmt->execute([$categoryName]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=category");
        exit();
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de l'ajout de la catégorie: " . $e->getMessage();
    }
}

// Handle Vehicle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehiculeModel'])) {
    try {
        $uploadResult = uploadImage($_FILES['image']);
        if (!$uploadResult['success']) {
            $errorMessage = $uploadResult['message'];
        } else {
            $imagePath = $uploadResult['filePath'];
            
            $stmt = $conn->prepare("INSERT INTO vehicules (categorieId, model, mark, prix, disponabilite, color, porte, transmition, personne, image) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $_POST['categorieId'],
                htmlspecialchars($_POST['vehiculeModel']),
                htmlspecialchars($_POST['mark']),
                floatval($_POST['prix']),
                htmlspecialchars($_POST['disponabilite']),
                htmlspecialchars($_POST['color']),
                intval($_POST['porte']),
                htmlspecialchars($_POST['transmition']),
                intval($_POST['personne']),
                $imagePath
            ]);
            
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=vehicle");
            exit();
        }
    } catch(PDOException $e) {
        $errorMessage = "Erreur lors de l'ajout du véhicule: " . $e->getMessage();
    }
}

// Fetch category
try {
    $stmt = $conn->query("SELECT * FROM category");
    $category = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $errorMessage = "Erreur lors de la récupération des catégories: " . $e->getMessage();
    $category = [];
}

// Fetch reservation
try {
    $stmt = $conn->query("SELECT * FROM reservation");
    $reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $errorMessage = "Erreur lors de la récupération des réservations: " . $e->getMessage();
    $reservation = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DriveNow - Car Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
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
                    <button onclick="window.location.href='logout.php';" class="px-4 py-2 bg-red-600 rounded-md hover:bg-red-700 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                    </button>
                </nav>
            </div>
        </header>

        <main class="flex">
            <!-- Sidebar -->
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

            <!-- Main Content -->
            <section class="w-screen md:w-[calc(100%-224px)]">
                <!-- Header Bar -->
                <div class="bg-gradient-to-r from-[#2f88da] to-[#07075a] px-5 py-3 flex justify-between w-full rounded-bl-lg rounded-br-lg">
                    <h1 class="text-white font-bold">Table des réservations</h1>
                    <div>
                        <button class="bg-green-400 text-white px-2 py-1 rounded-md" id="openCategoryModal">Ajouter une catégorie</button>
                        <button class="bg-green-400 text-white px-2 py-1 rounded-md" id="openVehiculeModal">Ajouter un véhicule</button>
                    </div>
                </div>

                <!-- Error Messages -->
                <!-- <?php if (isset($errorMessage)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo $errorMessage; ?></span>
                    </div>
                <?php endif; ?> -->

                <!-- Success Messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">
                            <?php echo $_GET['success'] === 'category' ? 'Catégorie ajoutée avec succès!' : 'Véhicule ajouté avec succès!'; ?>
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Reservation Table -->
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
                                <th class="px-2 md:px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reservation)): ?>
                                <?php foreach ($reservation as $res): ?>
                                    <tr class="border-b">
                                        <td class="px-2 md:px-6 py-3"><?php echo($res['id']); ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo($res['userId']); ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo($res['vehiculeId']); ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo($res['date_debut']); ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo($res['date_fin']); ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo($res['lieuId']); ?></td>
                                        <td class="px-2 md:px-6 py-3 flex space-x-2">
                                            <a href="edit_reservation.php?id=<?php echo $res['id']; ?>" 
                                               class="bg-blue-400 text-white p-3 rounded-lg">Modifier</a>
                                            <a href="delete_reservation.php?id=<?php echo $res['id']; ?>" 
                                               class="bg-red-400 text-white p-3 rounded-lg"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation?');">Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-3">Aucune réservation trouvée.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <!-- Category Modal -->
        <div id="categoryModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeCategoryModal">&times;</span>
                <h2 class="text-xl font-bold mb-4">Ajouter une catégorie</h2>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="space-y-4">
                    <div>
                        <label for="categoryName" class="block text-sm font-medium text-gray-700">Nom de la catégorie:</label>
                        <input type="text" name="categoryName" id="categoryName" 
                               class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Ajouter
                    </button>
                </form>
            </div>
        </div>

        <!--------------------- Modal of Vehicle ------ -->
        <div id="vehiculeModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeVehiculeModal">&times;</span>
                <h2 class="text-xl font-bold mb-4">Ajouter un véhicule</h2>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label for="categorieId" class="block text-sm font-medium text-gray-700">Catégorie:</label>
                        <select name="categorieId" id="categorieId" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                            <option value="" disabled selected>Sélectionner une catégorie</option>
                            <?php foreach ($category as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                    <?php echo htmlspecialchars($category['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="vehiculeModel" class="block text-sm font-medium text-gray-700">Modèle:</label>
                        <input type="text" name="vehiculeModel" id="vehiculeModel" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    </div>

                    <div>
                        <label for="mark" class="block text-sm font-medium text-gray-700">Marque:</label>
                        <input type="text" name="mark" id="mark" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    </div>

                    <div>
                        <label for="prix" class="block text-sm font-medium text-gray-700">Prix:</label>
                        <input type="number" name="prix" id="prix" step="0.01" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    </div>

                    <div>
                        <label for="disponabilite" class="block text-sm font-medium text-gray-700">Disponibilité:</label>
                        <select name="disponabilite" id="disponabilite" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                            <option value="disponible">Disponible</option>
                            <option value="indisponible">Indisponible</option>
                        </select>
                    </div>

                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700">Couleur:</label>
                        <input type="text" name="color" id="color" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    </div>

                    <div>
                        <label for="porte" class="block text-sm font-medium text-gray-700">Nombre de portes:</label>
                        <select name="porte" id="porte" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                            <option value="" disabled selected>Sélectionner</option>
                            <option value="2">2</option>
                            <option value="4">4</option>
                        </select>
                    </div>

                    <div>
                        <label for="transmition" class="block text-sm font-medium text-gray-700">Transmission:</label>
                        <input type="text" name="transmition" id="transmition" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                    </div>

                    <div>
                        <label for="personne" class="block text-sm font-medium text-gray-700">Capacité de personnes:</label>
                        <select name="personne" id="personne" class="w-full p-2 border border-gray-300 rounded mb-4" required>
                            <option value="" disabled selected>Sélectionner</option>
                            <option value="2">2</option>
                            <option value="5">5</option>
                        </select>
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">Image:</label>
                        <input type="file" id="image" name="image" accept="image/*" required
                               class="m-1 p-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <button name="submit" type="submit" class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Ajouter le véhicule
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const categoryModal = document.getElementById("categoryModal");
        const vehiculeModal = document.getElementById("vehiculeModal");
        const openCategoryModal = document.getElementById("openCategoryModal");
        const openVehiculeModal = document.getElementById("openVehiculeModal");
        const closeCategoryModal = document.getElementById("closeCategoryModal");
        const closeVehiculeModal = document.getElementById("closeVehiculeModal");

        openCategoryModal.onclick = function() {
            categoryModal.style.display = "block";
        }

        openVehiculeModal.onclick = function() {
            vehiculeModal.style.display = "block";
        }

        closeCategoryModal.onclick = function() {
            categoryModal.style.display = "none";
        }

        closeVehiculeModal.onclick = function() {
            vehiculeModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target === categoryModal) {
                categoryModal.style.display = "none";
            }
            if (event.target === vehiculeModal) {
                vehiculeModal.style.display = "none";
            }
        }

        // Auto-hide success messages after 5 seconds
        // const successAlert = document.querySelector('[role="alert"]');
        // if (successAlert) {
        //     setTimeout(() => {
        //         successAlert.style.display = 'none';
        //     }, 5000);
        // }
    </script>
</body>
</html>
```