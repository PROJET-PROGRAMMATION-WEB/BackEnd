# 1. Structure de la Base de Données
## Nous avons cree plusieurs tables avec leurs relations :

### Table Users
Stocke les informations des utilisateurs et stylistes
Champs : nom, email, mot de passe, rôle (user/stylist/admin), mesures, téléphone, adresse
Sécurité intégrée avec JWT pour l'authentification

### Table Stylists
Profils détaillés des stylistes
Champs : biographie, spécialité, disponibilité, note moyenne
Liée à la table Users (un styliste est aussi un utilisateur)

### Table Products
Catalogue des créations
Champs : nom, description, prix, catégories, délai de livraison, photos, matériaux
Système de notation et statistiques (vues, notes moyennes)

### Table Orders
Gestion des commandes
Champs : statut, prix total, mesures, détails de personnalisation, date de livraison prévue
Liens avec utilisateur, styliste et produit

### Table Payments
Suivi des paiements
Champs : montant, statut, méthode de paiement, ID de transaction
Prêt pour l'intégration avec les systèmes de paiement mobile

### Table Reviews
Système d'avis et commentaires
Champs : note, commentaire, photos, statut de vérification


# 2. Fonctionnalités Implémentées
Authentification JWT sécurisée
Relations entre les modèles pour une gestion efficace des données
Gestion des rôles (utilisateur/styliste/admin)
Stockage des mesures en format JSON
Système de notation et d'avis
Gestion des disponibilités des stylistes
Suivi des commandes avec différents statuts


# 3. Comment Tester le Projet
Configuration de l'environnement

##### Dans le dossier backend
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

Configuration de la base de données Modifiez le fichier .env avec vos informations de base de données :

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=votre_base_de_donnees
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

## Migration de la base de données
php artisan migrate

## Lancer le serveur
php artisan serve


# 4. Points d'API Disponibles (à implémenter dans les contrôleurs)
Authentication

POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
GET /api/auth/profile

Stylistes
GET /api/stylists
GET /api/stylists/{id}
GET /api/stylists/{id}/products
GET /api/stylists/{id}/availability

Produits

GET /api/products
GET /api/products/{id}
GET /api/products/category/{category}
POST /api/products (pour les stylistes)

Commandes

POST /api/orders
GET /api/orders/{id}
PUT /api/orders/{id}/status

# 5. Prochaines Étapes
Créer les contrôleurs pour gérer les requêtes API
Implémenter la logique métier pour chaque fonctionnalité
Ajouter la validation des données
Mettre en place les middlewares pour la sécurité
Intégrer les systèmes de paiement mobile
Configurer le système de notifications

# 6. Comment Cela Résout Votre Projet
Pour les Stylistes :
Système complet de gestion de profil
Catalogue de produits avec photos et détails
Gestion des commandes et disponibilités
Système de notation pour construire leur réputation
Pour les Utilisateurs :
Inscription et gestion de profil sécurisées
Stockage des mesures personnalisées
Système de commande complet
Possibilité de laisser des avis
Sécurité :
Authentification JWT robuste
Protection des données sensibles
Gestion des rôles et permissions
Évolutivité :
Structure modulaire facilitant les futures extensions
Relations bien définies entre les modèles
Support pour les fonctionnalités avancées (notifications, paiements)

