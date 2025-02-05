# 🔌 API REST

## 🔹 Endpoints Disponibles

### 📌 Utilisateurs

#### Récupérer les leçons et certifications d'un utilisateur
**GET** `/my-lessons`

- **Description** : Affiche les leçons et certifications de l'utilisateur connecté.
- **Controller** : `UserController`

---

### 📌 Authentification

#### Connexion de l'utilisateur
**GET** `/login`

- **Description** : Permet à l'utilisateur de se connecter.
- **Controller** : `SecurityController`

#### Déconnexion de l'utilisateur
**GET** `/logout`

- **Description** : Déconnecte l'utilisateur connecté.
- **Controller** : `SecurityController`

---

### 📌 Paiements (Stripe)

#### Créer une session de paiement Stripe
**POST** `/checkout`

- **Description** : Crée une session Stripe pour les articles dans le panier.
- **Controller** : `StripeController`

#### Succès du paiement
**GET** `/checkout/success`

- **Description** : Affiche un message de succès après un paiement réussi.
- **Controller** : `StripeController`

#### Annuler un paiement
**GET** `/checkout/cancel`

- **Description** : Redirige vers le panier après l'annulation d'un paiement.
- **Controller** : `StripeController`

#### Gestion des Webhooks Stripe
**POST** `/webhook`

- **Description** : Traite les événements Stripe via des webhooks (paiements, etc.).
- **Controller** : `StripeController`

---

### 📌 Thèmes

#### Lister les thèmes
**GET** `/theme`

- **Description** : Liste tous les thèmes disponibles.
- **Controller** : `ThemeController`

#### Détails d'un thème
**GET** `/theme/{id}`

- **Description** : Affiche les détails d'un thème spécifique.
- **Paramètres** :
  - `{id}` : Identifiant du thème.
- **Controller** : `ThemeController`

#### Ajouter un thème
**POST** `/dashboard/theme`

- **Description** : Permet d'ajouter un nouveau thème.
- **Controller** : `ThemeController`

#### Modifier un thème
**GET** `/theme/edit/{id}`

- **Description** : Permet de modifier un thème existant.
- **Paramètres** :
  - `{id}` : Identifiant du thème.
- **Controller** : `ThemeController`

#### Supprimer un thème
**DELETE** `/theme/delete/{id}`

- **Description** : Supprime un thème.
- **Paramètres** :
  - `{id}` : Identifiant du thème.
- **Controller** : `ThemeController`

---
### 📌 Cursus

#### Lister tous les cursus
**GET** `/dashboard/cursus`

- **Description** : Retourne la liste de tous les cursus disponibles.
- **Controller** : `CursusController`

#### Détails d'un cursus
**GET** `/cursus/{id}`

- **Paramètres** :
  - `{id}` : Identifiant du cursus.
- **Description** : Affiche les détails d'un cursus spécifique.
- **Controller** : `CursusController`

#### Ajouter un cursus
**POST** `/dashboard/cursus`

- **Description** : Permet d'ajouter un nouveau cursus.
- **Controller** : `CursusController`

#### Modifier un cursus
**GET** `/cursus/edit/{id}`

- **Paramètres** :
  - `{id}` : Identifiant du cursus.
- **Description** : Permet de modifier un cursus existant.
- **Controller** : `CursusController`

#### Supprimer un cursus
**DELETE** `/cursus/delete/{id}`

- **Paramètres** :
  - `{id}` : Identifiant du cursus.
- **Description** : Supprime un cursus existant.
- **Controller** : `CursusController`

---

### 📌 Leçons

#### Lister toutes les leçons
**GET** `/lesson`

- **Description** : Retourne la liste de toutes les leçons disponibles.
- **Controller** : `LessonController`

#### Détails d'une leçon
**GET** `/lesson/{id}`

- **Paramètres** :
  - `{id}` : Identifiant d'une leçon.
- **Description** : Affiche les détails d'une leçon spécifique.
- **Controller** : `LessonController`

#### Ajouter une leçon
**POST** `/dashboard/lesson`

- **Description** : Permet d'ajouter une nouvelle leçon.
- **Controller** : `LessonController`

#### Modifier un thème
**GET** `/lesson/edit/{id}`

- **Paramètres** :
  - `{id}` : Identifiant d'une leçon.
- **Description** : Permet de modifier une leçon existante.
- **Controller** : `LessonController`

#### Supprimer un thème
**DELETE** `/lesson/delete/{id}`

- **Paramètres** :
  - `{id}` : Identifiant d'une leçon.
- **Description** : Supprime une leçon existante.
- **Controller** : `LessonController`

---

### 📌 Gestion des Achats

#### Afficher le panier
**GET** `/cart`

- **Description** : Affiche les articles en attente dans le panier de l'utilisateur.
- **Controller** : `PurchaseController`

#### Ajouter un article au panier
**POST** `/cart/add/{type}/{id}`

- **Description** : Ajoute une leçon ou un cursus au panier.
- **Paramètres** :
  - `{type}` : Type d'article (`lesson` ou `cursus`).
  - `{id}` : Identifiant de l'article.
- **Controller** : `PurchaseController`

#### Supprimer un article du panier
**DELETE** `/cart/remove/{id}`

- **Description** : Supprime un article du panier.
- **Paramètres** :
  - `{id}` : Identifiant de l'article.
- **Controller** : `PurchaseController`

---


