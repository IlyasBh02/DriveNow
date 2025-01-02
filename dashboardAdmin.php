<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DriveNow - Car Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 1s ease-in-out;
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
                    <h1 class="text-white font-bold">Table des reservation</h1>
                    <a class="bg-green-400 text-white px-2 py-1 rounded-md" href="./FormAddreservation.php">Ajouter une voiture</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left table-auto">
                        <thead>
                            <tr>
                                <th class="px-2 md:px-6 py-3">#</th>
                                <th class="px-2 md:px-6 py-3">Titre</th>
                                <th class="px-2 md:px-6 py-3">Description</th>
                                <th class="px-2 md:px-6 py-3">Prix</th>
                                <th class="px-2 md:px-6 py-3">Date début</th>
                                <th class="px-2 md:px-6 py-3">Date fin</th>
                                <th class="px-2 md:px-6 py-3">Type</th>
                                <!-- <th class="px-2 md:px-6 py-3">Places disponibles</th> -->
                                <th class="px-2 md:px-6 py-3">resion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reservation)): ?>
                                <?php foreach ($reservation as $res): ?>
                                    <tr class="border-b">
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['idreservation']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['titre']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['description']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['prix']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['date_debut']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['date_fin']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['type']; ?></td>
                                        <td class="px-2 md:px-6 py-3"><?php echo $res['places_disponibles']; ?></td>
                                        <td class="px-2 md:px-6 py-3 flex space-x-2">
                                            <a class="bg-blue-400 text-white p-3 rounded-lg" href="./FormUpdatereservation.php?reservation_id=<?php echo $res['idreservation']; ?>">Modifier</a>
                                            <a class="bg-red-400 text-white p-3 rounded-lg" href="./deletereservation.php?reservation_id=<?php echo $res['idreservation']; ?>">Supprimer</a>
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
    </body>
    </html>
    
</body>
</html>