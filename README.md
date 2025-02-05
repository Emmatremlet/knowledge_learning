# Knowledge Learning

Knowledge Learning est une application web permettant aux utilisateurs d'acheter des leçons ou des cursus, de les valider, et d'obtenir des certifications. Le projet suit une architecture 3-tiers avec une implémentation orientée objet et respecte le modèle MVC.

## Fonctionnalités principales

- **Gestion des utilisateurs** :
  - Inscription et connexion des utilisateurs.
  - Différenciation des rôles (user, admin).
  - Accès restreint aux contenus achetés.

- **Gestion des contenus** :
  - Les utilisateurs peuvent acheter des leçons ou des cursus.
  - Validation des leçons par les utilisateurs.
  - Certification automatique lorsque toutes les leçons d'un cursus sont validées.

- **Paiement** :
  - Intégration de Stripe pour les paiements en mode sandbox.

- **Administration** :
  - Gestion des leçons, des cursus et des utilisateurs par les administrateurs.

## Prérequis

- **PHP** >= 8.1
- **Composer**
- **Symfony CLI**
- **MySQL** ou un autre SGBD compatible avec Doctrine
- **Node.js** et **npm** (pour la gestion des assets avec Webpack Encore)
- **Stripe CLI** (pour les tests de paiement)

## Installation

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/Emmatremlet/knowledge_learning.git
   cd knowledge-learning
   ```

2. Installez les dépendances backend :
   ```bash
   composer install
   ```
  
3. Configurez le fichier `.env` :
   - Modifiez les paramètres de connexion à la base de données.
     ```env
    DATABASE_URL="mysql://db_user:db_password@127.0.0.1:8000knowledge_learning"
    MAILER_DSN="smtp://localhost"
     ```
   - Ajoutez vos clés Stripe :
     ```env
     STRIPE_SECRET_KEY=sk_test_votrecle
     STRIPE_PUBLIC_KEY=pk_test_votrecle
     STRIPE_WEBHOOK_SECRET=whsec_votrecle
     ```

4. Créez la base de données et appliquez les migrations :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. Load fixtures:
   ```bash
   php bin/console doctrine:fixtures:load
   ```

6. Lancez le serveur Symfony :
   ```bash
   symfony server:start
   ```

7. Lancez Stripe CLI pour les webhooks :
   ```bash
   stripe listen --forward-to http://127.0.0.1:8000/webhook
   ```

## Utilisation

- Accédez à l'application via [http://127.0.0.1:8000](http://127.0.0.1:8000).
- Inscrivez-vous en tant qu'utilisateur ou connectez-vous avec un compte existant.
- Naviguez dans les thèmes, achetez des leçons ou des cursus, et accédez à vos contenus.
- Les administrateurs peuvent accéder à un tableau de bord pour gérer les contenus.

## Tests

- **Tests unitaires** :
  ```bash
  php bin/phpunit
  ```
- **Vérification des routes** :
  ```bash
  php bin/console debug:router
  ```

## Annexes

- Diagramme UML des entités disponible dans le dossier : `diagramme_UML/knowledge-learning_UML.png`.

## Auteur

- **Nom** : Emma Tremlet
- **Email** : emmatremlet20@gmail.com