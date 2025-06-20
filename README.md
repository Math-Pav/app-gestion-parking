# Application de Gestion de Parking

## Description
Une application web permettant la gestion d'un parking avec système de réservation, tableaux de bord utilisateur et administrateur.

## Fonctionnalités

### Pour les Utilisateurs
- Système d'authentification
- Tableau de bord personnel
- Visualisation des places disponibles
- Gestion des réservations
- Interface responsive

### Pour les Administrateurs
- Tableau de bord avec statistiques
- Visualisation graphique des types de places (graphique en anneau)
- Suivi en temps réel :
    - Nombre total d'utilisateurs actifs
    - Nombre total de réservations
    - Répartition des types de places

## Technologies Utilisées

### Backend
- PHP

### Frontend
- JavaScript (Vanilla)
- Chart.js pour les visualisations
- CSS pour le style

## Installation

1. Cloner le projet :
```bash
git clone https://github.com/Math-Pav/app-gestion-parking.git
```

2. Configuration du serveur web
Utilisez Apache ou Nginx.

Pointez le DocumentRoot vers le répertoire suivant :
```bash
/var/www/html/app-gestion-parking
```
Base de données :
Configurer les accès dans les fichiers de configuration : .env

### Structure du projet
```bash
app-gestion-parking/
├── frontend/
│   ├── controllers/
│   │   ├── DashboardController.js
│   │   └── DashboardAdminController.js
│   └── assets/
│       └── js/
│           └── navbar.js
├── api/
└── ...
```

### Auteur
Développé par Math-Pav
