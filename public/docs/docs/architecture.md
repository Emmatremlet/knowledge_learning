# 🏗️ Architecture du Projet

Ce document décrit l'architecture du projet **Knowledge Learning**. Le projet est structuré selon les standards de Symfony et suit une organisation modulaire pour faciliter la maintenance et le développement.

---

## 📁 Arborescence des fichiers

```plaintext
.
├── .git/                      # Fichiers de versionnement Git
├── assets/                    # Ressources front-end (CSS, JS) et images
├── bin/                       # Commandes exécutables de Symfony
├── config/                    # Configuration de l'application
├── diagramme_UML/             # Fichier permettant de créer le diagramme UML du projet et le diagramme UML en png
├── migrations/                # Fichiers de migration de la base de données
├── public/                    # Dossier accessible publiquement (point d'entrée web)
│   ├── docs/                  # Documentation générée avec MkDocs
│   │   ├── site/              # Différentes pages de la documentation
│   │   ├── docs/              # Fichiers statiques (CSS, images)
│   │   └──mkdocs.yml          # Configuration de la documentation MkDocs
│   └── index.php              # Point d'entrée principal de Symfony
├── src/                       # Code source de l'application
│   ├── Controller/            # Contrôleurs pour gérer les routes
│   ├── DataFixtures/          # Chargement des données initiales
│   ├── Entity/                # Entités mappées à la base de données
│   ├── EventSubscriber/       # Abonnés aux événements Symfony
│   ├── Form/                  # Formulaires de l'application
│   ├── Repository/            # Requêtes personnalisées pour les entités
│   ├── Security/              # Logiciels de sécurité (authentification)
│   ├── Service/               # Services métiers réutilisables
│   └── Kernel.php             # Fichier principal de configuration Symfony
├── templates/                 # Templates Twig pour les vues
├── tests/                     # Tests unitaires et fonctionnels
├── translations/              # Fichiers de traduction pour l'internationalisation
├── var/                       # Fichiers temporaires et caches
├── vendor/                    # Dépendances gérées par Composer
├── .env                       # Variables d'environnement
├── .env.dev                   # Variables pour l'environnement de développement
├── .env.test                  # Variables pour l'environnement de test
├── .gitignore                 # Fichiers ignorés par Git
├── composer.json              # Dépendances PHP et configuration de Composer
├── composer.lock              # Version exacte des dépendances
├── documentation.pdf          # Documentation exportée en PDF
├── Procfile                   # Configuration pour déployer sur Heroku
├── php.ini                    # Configuration PHP locale
├── phpunit.xml.dist           # Configuration pour les tests PHPUnit
├── symfony.lock               # Liste verrouillée des versions des dépendances
```

## 📄 Entités

Les entités définissent les objets métiers qui représentent les tables de la base de données. Voici une liste des principales entités du projet :

- **BaseEntity** :Fournit des propriétés et des méthodes communes pour la gestion des métadonnées des entités.
- **Certification** : Représente les certifications d'un utilisateur.
- **User** : Représente les utilisateurs.
- **Cursus** : Représente les cursus disponibles.
- **Lesson** : Représente les leçons d'un cursus.
- **Purchase** : Représente les achats des utilisateurs.
- **Theme** : Représente les thèmes liés aux cursus.

Chaque entité est décrite en détail dans la section ci-dessous :
### Détails des Entités
#### **BaseEntity**
```php
namespace App\Entity;

class BaseEntity
{
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private string $createdBy;
    private string $updatedBy;
}
```
#### **User**
```php
namespace App\Entity;

class User
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private array $roles;
    private bool $isVerified;
    private Collection $purchases;
    private Collection $certifications;
}
```
#### **Certification**
```php
namespace App\Entity;

class Certification
{
    private int $id;
    private User $user;
    private ?Cursus $cursus = null;
    private ?bool $isValidated = null;
    private ?\DateTimeImmutable $validatedAt = null;
}
```
#### **Cursus**
```php
namespace App\Entity;

class Cursus
{
    private int $id;
    private string $name;
    private string $description;
    private float $price;
    private Theme $theme;
    private Collection $lessons;
}
```
#### **Lesson**
```php
namespace App\Entity;

class Lesson
{
    private int $id;
    private string $name;
    private string $description;
    private string $videoUrl;
    private float $price;
    private Cursus $cursus;
    private bool $isValidated = false;
}
```
#### **Purchase**
```php
namespace App\Entity;

class Purchase
{
    private int $id;
    private User $user;
    private ?Lesson $lesson = null;
    private ?Cursus $cursus = null;
    private ?\DateTimeImmutable $purchaseDate = null;
    private ?string $status = 'pending';
    private float $totalPrice;}
```
#### **Theme**
```php
namespace App\Entity;

class Theme
{
    private int $id;
    private string $name;
    private string $description;
    private Collection $cursuses;
}
```