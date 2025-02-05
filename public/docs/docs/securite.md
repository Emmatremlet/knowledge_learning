# 🔐 Sécurité et Authentification

## 🔹 Gestion des Utilisateurs et des Rôles

### 📌 Connexion et Déconnexion

#### Connexion d'un utilisateur
**GET** `/login`

- **Description** : Permet à l'utilisateur de se connecter.
- **Controller** : `SecurityController`

#### Déconnexion d'un utilisateur
**GET** `/logout`

- **Description** : Permet à l'utilisateur de se déconnecter.
- **Controller** : `SecurityController`

---

### 📌 Paiements sécurisés avec Stripe

#### Créer une session de paiement Stripe
**POST** `/checkout`

- **Description** : Crée une session Stripe pour les articles du panier.
- **Controller** : `StripeController`

#### Gestion des webhooks Stripe
**POST** `/webhook`

- **Description** : Traite les événements Stripe (paiements, annulations, etc.).
- **Controller** : `StripeController`

---

## 🔹 Sécurité des Routes

### 🔹 Protection CSRF
Toutes les actions sensibles (ajout, modification, suppression) sont protégées par des jetons CSRF.

### 🔹 Vérification des Rôles
- **`ROLE_USER`** : Accès de base pour les utilisateurs authentifiés.
- **`ROLE_ADMIN`** : Accès aux fonctionnalités administratives.

---

## 🔹 Bonnes Pratiques

1. Activer un pare-feu pour protéger les routes sensibles.
2. Utiliser HTTPS en production pour sécuriser les échanges.
3. Ne jamais exposer les variables sensibles (`.env`) publiquement.