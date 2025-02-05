# 📥 Installation

## 🔹 Prérequis
- PHP 8.2+
- Composer
- Symfony CLI
- MySQL ou PostgreSQL

## 🔹 Étapes d'installation

1. Cloner le dépôt et installer les dépendances : 
```bash
git clone https://github.com/Emmatremlet/knowledge_learning.git
cd knowledge-learning
composer install
```

2. Configurer le fichier `.env` :
```env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:8000knowledge_learning"
MAILER_DSN="smtp://localhost"
STRIPE_SECRET_KEY=sk_test_votrecle
STRIPE_PUBLIC_KEY=pk_test_votrecle
STRIPE_WEBHOOK_SECRET=whsec_votrecle
```

3. Créez la base de données, appliquez les migrations et load les fixtures
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

4. Lancez le serveur
```bash
symfony server:start
```

5. Lancez Stripe CLI pour les webhooks :
```bash
stripe listen --forward-to http://127.0.0.1:8000/webhook
```

6. Se connecter en tant qu'administrateur : 
```
Email : admin@example.com
Password : password123
```
