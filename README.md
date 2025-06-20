📦 Application de Gestion de Parking
🚗 Description
Une application web permettant la gestion intelligente d'un parking avec un système de réservation en ligne, des tableaux de bord interactifs pour les utilisateurs et les administrateurs, ainsi qu'une interface responsive adaptée à tous les appareils.

✨ Fonctionnalités
👤 Côté Utilisateur
🔐 Authentification sécurisée

📊 Tableau de bord personnel

📍 Visualisation en temps réel des places disponibles

📅 Gestion de ses propres réservations

📱 Interface responsive (mobile, tablette, desktop)

🛠️ Côté Administrateur
🧭 Tableau de bord avec statistiques globales

📈 Graphique en anneau de la répartition des types de places (Chart.js)

📌 Suivi en temps réel :

Nombre total d’utilisateurs actifs

Nombre de réservations effectuées

Répartition par types de places (PMR, voiture électrique, etc.)

🧰 Technologies Utilisées
🔙 Backend
PHP (API REST ou scripts PHP classiques)

🔝 Frontend
JavaScript Vanilla

Chart.js pour les visualisations graphiques

HTML5 & CSS3 pour la structure et le style

📂 Structure du Projet
arduino
Copier le code
app-gestion-parking/
├── frontend/
│   ├── controllers/
│   │   ├── DashboardController.js
│   │   └── DashboardAdminController.js
│   └── assets/
│       └── js/
│           └── navbar.js
├── api/
│   └── ... (fichiers PHP pour les endpoints)
├── config/
│   └── ... (fichiers de configuration et connexions DB)
└── index.php (ou fichier d'entrée principal)
⚙️ Installation & Configuration
1. Cloner le projet
bash
Copier le code
git clone https://github.com/Math-Pav/app-gestion-parking.git
2. Configuration du serveur web
Utiliser Apache ou Nginx

Pointer le DocumentRoot vers le dossier /app-gestion-parking

3. Base de données
Créer la base de données correspondante (voir fichier .sql si disponible)

Configurer les identifiants de connexion dans le fichier adéquat (ex. config/db.php)

✅ À faire / Suggestions d'amélioration
Ajout de tests automatisés

Interface d'inscription utilisateur

Gestion avancée des rôles

Export des statistiques (CSV, PDF)

👨‍💻 Auteur
Développé par Math-Pav
📎 GitHub : @Math-Pav
