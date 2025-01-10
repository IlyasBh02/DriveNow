<?php
session_start();
require "Vehicule.php"; // Assuming you have a Vehicule class to handle vehicle-related actions
$ClsConn = new connection();
$conn = $ClsConn->getConnection();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superAdmin')) {
    echo "Access denied!";
    exit();
}

// Fetch all categories
$sql = "SELECT id, nom FROM DriveNow.category";
$stmt = $conn->prepare($sql);
if ($stmt->execute()) {
    $categories = $stmt->fetchAll();
} else {
    echo "Error in fetching categories.";
}

// Handle form submission
if (isset($_POST['submit'])) {
    $categorieId = $_POST['categorieId'];
    $model = $_POST['model'];
    $mark = $_POST['mark'];
    $prix = $_POST['prix'];
    $disponibilite = $_POST['disponibilite'] ? 1 : 0;  // Convert to boolean
    $color = $_POST['color'];
    $porte = $_POST['porte'];
    $transmition = $_POST['transmition'];
    $personne = $_POST['personne'];
    $image = $_POST['image']; // Assuming the image URL or base64 string

    // Assuming you have a Vehicule class to handle the vehicle insert logic
    $Vehiculeinstance = new Vehicule();
    $addVehicule = $Vehiculeinstance->addVehicule($categorieId, $model, $mark, $prix, $disponibilite, $color, $porte, $transmition, $personne, $image);

    if ($addVehicule) {
        header("Location: ./main.php");
    } else {
        echo "Error in the insert";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <main class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
            <!-- Form -->
            <form id="vehicleForm" action="" method="POST" class="mt-8 space-y-6">
                <div class="space-y-4">
                    <!-- Model -->
                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                            Model
                        </label>
                        <input type="text" id="model" name="model"
                            class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    </div>

                    <!-- Mark -->
                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                            Mark
                        </label>
                        <input type="text" id="mark" name="mark"
                            class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                            Price
                        </label>
                        <input type="text" id="prix" name="prix"
                            class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    </div>

                    <!-- Availability -->
                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                            Availability
                        </label>
                        <select name="disponibilite" class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                            <option value="1">Available</option>
                            <option value="0">Not Available</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                            Category
                        </label>
                        <select name="categorieId" id="categorieId"
                            class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo $category['nom']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Color -->
                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                            Color
                        </label>
                        <input type="text" id="color" name="color"
                            class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    </div>

                    <!-- Number of doors -->
                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                            Number of doors
                        </label>
                        <input type="number" id="porte" name="porte"
                            class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    </div>

                    <!-- Transmission -->
                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                            Transmission
                        </label>
                        <input type="text" id="transmition" name="transmition"
                            class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    </div>

                    <!-- Seats -->
                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                            Number of seats
                        </label>
                        <input type="number" id="personne" name="personne"
                            class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    </div>

                    <!-- Image URL -->
                    <div>
                        <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                            Image URL
                        </label>
                        <input type="text" id="image" name="image"
                            class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" name="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Add Vehicle
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>

