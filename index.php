<?php
class Connection {
    private $host = "localhost";
    private $dbName = "DriveNow";
    private $userName = "root";
    private $userPass = "";
    private $db;

    public function __construct() {
        $this->db = $this->getConnection();
    }
    
    public function getConnection() {
        try {
            if (!$this->db) {
                $this->db = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->dbName,
                    $this->userName,
                    $this->userPass
                );
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return $this->db;
        } catch (PDOException $e) {
            die("La connexion a échoué: " . $e->getMessage());
        }
    }
    
    public function closeConnection() {
        $this->db = null;
    }
}

class Vehicle {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllVehicles() {
        $stmt = $this->db->prepare('SELECT v.*, c.nom as category_name, l.lieuName 
                                   FROM vehicule v 
                                   JOIN category c ON v.categorieId = c.id 
                                   LEFT JOIN lieu l ON l.id = 1');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function searchVehicles($search) {
        $stmt = $this->db->prepare("SELECT v.*, c.nom as category_name, l.lieuName 
                                   FROM vehicule v 
                                   JOIN category c ON v.categorieId = c.id 
                                   LEFT JOIN lieu l ON l.id = 1 
                                   WHERE v.model LIKE :search OR v.mark LIKE :search");
        $search = "%$search%";
        $stmt->bindParam(':search', $search);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class Display {
    public function showVehicles($vehicles) {
        if (empty($vehicles)) {
            echo '<p class="text-center text-xl text-gray-600">Aucun véhicule trouvé.</p>';
            return;
        }
        foreach ($vehicles as $vehicle) {
            echo '
            <div class="relative bg-white rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 ease-in-out">
                <img src="' . htmlspecialchars($vehicle['image']) . '" alt="' . htmlspecialchars($vehicle['mark'] . ' ' . $vehicle['model']) . '" 
                     class="w-full h-48 object-cover rounded-t-lg">
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <h2 class="text-xl font-medium text-gray-800">' . htmlspecialchars($vehicle['mark'] . ' ' . $vehicle['model']) . '</h2>
                        <p class="text-sm text-gray-500">' . htmlspecialchars($vehicle['category_name']) . '</p>
                    </div>
                    
                    <div class="space-y-4 border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Prix par jour</span>
                            <span class="text-sm font-medium text-gray-800">' . $vehicle['prix'] . ' €</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Couleur</span>
                                <span class="text-sm text-gray-800">' . htmlspecialchars($vehicle['color']) . '</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Portes</span>
                                <span class="text-sm text-gray-800">' . $vehicle['porte'] . '</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Transmission</span>
                                <span class="text-sm text-gray-800">' . htmlspecialchars($vehicle['transmition']) . '</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Places</span>
                                <span class="text-sm text-gray-800">' . $vehicle['personne'] . '</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Disponibilité</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . 
                            ($vehicle['disponibilite'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . '">
                                ' . ($vehicle['disponibilite'] ? 'Disponible' : 'Indisponible') . '
                            </span>
                        </div>
                    </div>
                    
                    <div class="pt-4">
                        <a href="./Client/ReservationPage.php?id=' . $vehicle['id'] . '" 
                           class="block w-full text-center px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200"' .
                           (!$vehicle['disponibilite'] ? ' disabled' : '') . '>
                            Réserver maintenant
                        </a>
                    </div>
                </div>
            </div>';
        }
    }
}

$conn = new Connection();
$db = $conn->getConnection();

$search = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : "";

$vehicleObj = new Vehicle($db);
$vehicles = $search ? $vehicleObj->searchVehicles($search) : $vehicleObj->getAllVehicles();

$display = new Display();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>DriveNow - Location de Véhicules</title>
</head>
<body class="bg-gray-50">
    <header class="bg-gray-800 text-white px-4 py-3">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">DriveNow</h1>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#vehicles">Véhicules</a>
                    <a href="#contact">Contact</a>
                    <!-- <a href="./Client/HistoryReservation.php">Mes Réservations</a> -->
                    <a href="Logout.php">Déconnexion</a>
                </div>
                <form method="POST" action="" class="hidden md:flex">
                    <input
                        name="searchTerm"
                        type="text"
                        class="w-64 p-2 rounded-l-lg text-black"
                        placeholder="Rechercher un véhicule"
                    >
                    <button class="bg-blue-600 px-4 py-2 rounded-r-lg hover:bg-blue-700">
                        Rechercher
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-12">Nos Véhicules</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php $display->showVehicles($vehicles); ?>
        </div>
    </main>



    <section class="py-16">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-12">Future Activities</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden transform transition-all hover:scale-105 hover:shadow-xl hover:translate-y-2 duration-300 ease-in-out">
                <img src="./images/img7.jpg" alt="Surfing" class="w-full h-48 object-cover rounded-t-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Surfing</h3>
                    <p class="text-gray-600 text-sm">Explore the waves with exciting surfing experiences on stunning beaches.</p>
                </div>
            </div>
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden transform transition-all hover:scale-105 hover:shadow-xl hover:translate-y-2 duration-300 ease-in-out">
                <img src="./images/img4.jpg" alt="Skydiving" class="w-full h-48 object-cover rounded-t-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Skydiving</h3>
                    <p class="text-gray-600 text-sm">Feel the thrill of freefalling from thousands of feet in the sky.</p>
                </div>
            </div>
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden transform transition-all hover:scale-105 hover:shadow-xl hover:translate-y-2 duration-300 ease-in-out">
                <img src="./images/img7.jpg" alt="Party" class="w-full h-48 object-cover rounded-t-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Party</h3>
                    <p class="text-gray-600 text-sm">Join exciting party events with vibrant music and dancing.</p>
                </div>
            </div>
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden transform transition-all hover:scale-105 hover:shadow-xl hover:translate-y-2 duration-300 ease-in-out">
                <img src="./images/img7.jpg" alt="Dessert Tasting" class="w-full h-48 object-cover rounded-t-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Dessert Tasting</h3>
                    <p class="text-gray-600 text-sm">Indulge in an exclusive tasting experience of gourmet desserts.</p>
                </div>
            </div>
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden transform transition-all hover:scale-105 hover:shadow-xl hover:translate-y-2 duration-300 ease-in-out">
                <img src="./images/img7.jpg" alt="Vegetarian Cooking" class="w-full h-48 object-cover rounded-t-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Vegetarian Cooking</h3>
                    <p class="text-gray-600 text-sm">Learn to prepare delicious and healthy vegetarian dishes.</p>
                </div>
            </div>
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden transform transition-all hover:scale-105 hover:shadow-xl hover:translate-y-2 duration-300 ease-in-out">
                <img src="./images/img7.jpg" alt="Yoga Classes" class="w-full h-48 object-cover rounded-t-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Yoga Classes</h3>
                    <p class="text-gray-600 text-sm">Relax and rejuvenate with expert-led yoga sessions for all levels.</p>
                </div>
            </div>
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden transform transition-all hover:scale-105 hover:shadow-xl hover:translate-y-2 duration-300 ease-in-out">
                <img src="./images/img7.jpg" alt="Hiking Adventures" class="w-full h-48 object-cover rounded-t-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Hiking Adventures</h3>
                    <p class="text-gray-600 text-sm">Discover breathtaking landscapes on guided hiking tours.</p>
                </div>
            </div>
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden transform transition-all hover:scale-105 hover:shadow-xl hover:translate-y-2 duration-300 ease-in-out">
                <img src="./images/img7.jpg" alt="Beach Volleyball" class="w-full h-48 object-cover rounded-t-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Beach Volleyball</h3>
                    <p class="text-gray-600 text-sm">Join a fun-filled game of volleyball at the beach with friends.</p>
                </div>
            </div>
        </div>
    </div>
</section>  
<section id="reviews-contact" class="py-16 m-2 rounded-lg bg-red-100" style="background-image: url('./images/voyag.jpg');">
    <div class="container mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Ce que disent nos clients</h2>
            <div class="space-y-6">
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <p class="text-gray-600 italic">"Great website! Easy to use, quick booking, and great prices. I will definitely use it again!"</p>
                    <div class="mt-4">
                        <h4 class="text-lg font-bold text-gray-800">Marie Dupont</h4>
                        <p class="text-sm text-gray-500">⭐⭐⭐⭐⭐</p>
                    </div>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <p class="text-gray-600 italic">"The booking process was okay, but I had some trouble with the date selection. Otherwise, everything worked fine."</p>
                    <div class="mt-4">
                        <h4 class="text-lg font-bold text-gray-800">Jean Martin</h4>
                        <p class="text-sm text-gray-500">⭐⭐⭐⭐⭐</p>
                    </div>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <p class="text-gray-600 italic">"The site was hard to navigate on mobile, and I had trouble with the reservation details. Needs improvement."</p>
                    <div class="mt-4">
                        <h4 class="text-lg font-bold text-gray-800">Sophie Leblanc</h4>
                        <p class="text-sm text-gray-500">⭐⭐⭐⭐⭐</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form Section -->
        <div class="bg-gray-800 text-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold mb-6">Contactez-nous</h2>
            <p class="text-gray-300 mb-8">
                Vous avez des questions ou souhaitez réserver une table ? Envoyez-nous un message, et nous vous répondrons rapidement.
            </p>
            <form action="process_contact.php" method="POST">
                <div class="mb-6">
                    <label for="name" class="block text-white mb-2">Nom complet</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        placeholder="Email :"
                        class="w-full px-4 py-2 bg-gray-300 text-black rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                        required>
                </div>
                <div class="mb-6">
                    <label for="email" class="block text-white mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        placeholder="Email :"
                        class="w-full px-4 py-2 bg-gray-300 text-black rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                        required>
                </div>
                <div class="mb-6">
                    <label for="message" class="block text-white mb-2">Message</label>
                    <textarea 
                        id="message" 
                        name="message" 
                        placeholder="Email :"
                        rows="4" 
                        class="w-full px-4 py-2 bg-gray-300 text-black rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                        required></textarea>
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="w-[50%] py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-primary-dark transition-colors">
                        Envoyer
                    </button>
                    </div>
            </form>
        </div>
    </div>
</section>
<!-- About Us Section -->
<section id="about-us" class="py-16 px-16 bg-gray-100">
    <div class="container mx-auto text-center">
        <!-- Introduction -->
        <h2 class="text-3xl font-bold text-gray-800 mb-6">À propos de notre site</h2>
        <p class="text-lg text-gray-600 leading-relaxed mb-12">
            Bienvenue sur notre site de voyages ! Nous avons créé cette plateforme pour offrir une expérience simplifiée, moderne et agréable, permettant à nos utilisateurs de réserver des activités et des offres touristiques en toute facilité. Que ce soit pour un week-end aventure ou des vacances relaxantes, notre objectif est de rendre chaque réservation aussi simple que possible.
        </p>

        <!-- Mission Section -->
        <div class="mb-16">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Notre Mission</h3>
            <p class="text-gray-600 text-lg leading-relaxed">
                Nous nous engageons à fournir une plateforme où vous pouvez découvrir des activités passionnantes et réserver facilement vos séjours, excursions, et événements touristiques. Notre mission est de vous permettre de planifier des voyages inoubliables avec une expérience de réservation fluide et sans tracas.
            </p>
        </div>

        <!-- Team Section -->
        <div class="mb-16 p-2 rounded-lg bg-black text-gray-300">
            <h3 class="text-2xl font-bold text-white mb-6">Notre Équipe</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="text-center">
                    <img src="./images/chefs-01.jpg" alt="Responsable Marketing" class="w-44 h-44 mx-auto rounded-full mb-4">
                    <h4 class="text-xl font-bold text-white">Sophie, Responsable Marketing</h4>
                    <p class="text-gray-300">Sophie est en charge de la gestion des offres et des partenariats touristiques.</p>
                </div>
                <div class="text-center">
                    <img src="./images/chefs-02.jpg" alt="Développeur Web" class="w-44 h-44 mx-auto rounded-full mb-4">
                    <h4 class="text-xl font-bold text-white">Marc, Développeur Web</h4>
                    <p class="text-gray-300">Marc travaille sur le développement de la plateforme et l'optimisation de l'expérience utilisateur.</p>
                </div>
                <div class="text-center">
                    <img src="./images/chefs-03.jpg" alt="Support Client" class="w-44 h-44 mx-auto rounded-full mb-4">
                    <h4 class="text-xl font-bold text-white">Claire, Support Client</h4>
                    <p class="text-gray-300">Claire s'assure que chaque client ait une expérience exceptionnelle avec notre support personnalisé.</p>
                </div>
            </div>
        </div>

        <!-- Image Section -->
        <div class="mb-16">
            <img src="./images/voyag.jpg" alt="Réservation de voyages" class="w-full h-96 object-cover rounded-lg shadow-lg">
        </div>

        <!-- Final Statement -->
        <div>
            <p class="text-lg text-gray-600 leading-relaxed">
                Notre site est conçu pour vous aider à planifier des vacances parfaites et à réserver des activités touristiques selon vos préférences. Que vous soyez à la recherche d'une aventure, d'une escapade tranquille ou d'un événement spécial, nous sommes là pour vous guider tout au long du processus de réservation. Explorez nos offres et préparez-vous à partir à la découverte du monde !
            </p>
        </div>
    </div>
</section>


<!-- Footer -->
<footer class="bg-gray-800 text-white p-6 text-center">
    <p>&copy; 2024 Notre Agence - Tous droits réservés</p>
</footer>   

</body>
</html>

<?php $conn->closeConnection(); ?>