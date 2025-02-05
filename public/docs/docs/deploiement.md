# 🚀 Déploiement sur Heroku

Ce guide vous explique comment déployer votre projet Symfony sur Heroku.

---

## 🔹 Prérequis

- **Compte Heroku** : Inscrivez-vous sur [Heroku](https://signup.heroku.com/).
- **Heroku CLI** : Téléchargez et installez la CLI Heroku : [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli).
- **Composer** et **Git** doivent être installés sur votre machine.
- **Base de données** : MySQL.

---

## 🔹 Étapes de Déploiement

### 1️⃣ Initialiser le projet

1. Connectez-vous à Heroku depuis la ligne de commande :
```bash
heroku login
```
2.	Accédez à la racine de votre projet Symfony :
```bash
cd knowledge-learning
```
3.	Créez une application Heroku :
```bash
heroku create nom-de-votre-application
```

### 2️⃣ Configuration de la base de données

1.	Ajoutez l’add-on JawsDB :
```bash
heroku addons:create jawsdb
```
2.	Configurez la variable d’environnement DATABASE_URL dans Heroku :
```bash
heroku config:set DATABASE_URL=$(heroku config:get DATABASE_URL)
```
### 3️⃣ Configurer Symfony pour le déploiement
1.	Installez le bundle Heroku pour Symfony :
```bash
composer require symfony/heroku-bundle
```

2.	Ajoutez une variable d’environnement pour le APP_ENV :
```bash
heroku config:set APP_ENV=prod
```

3.	Configurez la clé secrète de Symfony :
```bash
heroku config:set APP_SECRET=your_secret_key
```

4.	Si vous utilisez Stripe, ajoutez vos clés Stripe :
```bash
heroku config:set STRIPE_SECRET_KEY=sk_test_votre_cle
heroku config:set STRIPE_PUBLIC_KEY=pk_test_votre_cle
```


### 4️⃣ Préparer le projet pour Heroku
1.	Ajoutez un fichier Procfile à la racine du projet avec le contenu suivant :
```
web: heroku-php-apache2 public/
```
2.	Assurez-vous que votre fichier .env contient la ligne suivante pour lire les variables d’environnement de Heroku :
```env 
APP_ENV=prod
DATABASE_URL=${DATABASE_URL}
```

### 5️⃣ Déployer le projet
1.	Initialisez Git si ce n’est pas déjà fait :
```bash 
git init
git add .
git commit -m "Initial commit"
```

2.	Ajoutez Heroku comme remote Git :
```bash 
heroku git:remote -a nom-de-votre-application
```

3.	Déployez votre projet sur Heroku :
```bash 
git push heroku main
```

### 6️⃣ Migrer la base de données
1.	Exécutez les migrations sur la base de données Heroku :
```bash 
heroku run php bin/console doctrine:migrations:migrate
```

### 7️⃣ Tester le déploiement
1.	Ouvrez votre application dans un navigateur :
```bash
heroku open
```