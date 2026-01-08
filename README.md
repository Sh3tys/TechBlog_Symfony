# TechBlog Gaming

Plateforme de blog sur les jeux de survie, réalisée avec Symfony.

---

## Fonctionnalités

### Pour tous

-   Accueil et liste des articles
-   Lecture d’articles complets
-   Formulaire de contact
-   Pages légales (Mentions légales, CGU, Confidentialité)
-   Page "Qui sommes-nous"

### Utilisateurs

-   Inscription / connexion
-   Profil : consulter et modifier email/pseudo, changer mot de passe, supprimer compte

### Administrateurs

-   Gestion complète des articles (CRUD)
-   Publication / brouillon
-   Articles liés à l’auteur connecté

---

## Technologies

-   Symfony 7.3
-   PHP 8.3
-   MySQL 8.0
-   Twig et Bootstrap 5.3
-   Doctrine ORM

---

## Installation

### Prérequis

-   PHP 8.3+, Composer, MySQL 8+
-   Extensions PHP : pdo_mysql, intl, opcache

### Étapes

```bash
git clone https://github.com/Sh3tys/TechBlog_Symfony.git

cd techblog-gaming

composer install

# Configurer la base dans .env
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

#Lancer le serveur de développement
php -S 127.0.0.1:8000 -t public
```

Accéder ensuite à http://127.0.0.1:8000

Structure importante du Projet :

```arduino
./config/
├── bundles.php
├── packages
│   ├── asset_mapper.yaml
│   ├── cache.yaml
│   ├── csrf.yaml
│   ├── debug.yaml
│   ├── doctrine.yaml
│   ├── doctrine_migrations.yaml
│   ├── framework.yaml
│   ├── mailer.yaml
│   ├── messenger.yaml
│   ├── monolog.yaml
│   ├── notifier.yaml
│   ├── property_info.yaml
│   ├── routing.yaml
│   ├── security.yaml
│   ├── translation.yaml
│   ├── twig.yaml
│   ├── ux_turbo.yaml
│   ├── validator.yaml
│   └── web_profiler.yaml
├── preload.php
├── routes
│   ├── framework.yaml
│   ├── security.yaml
│   └── web_profiler.yaml
├── routes.yaml
└── services.yaml

./public/index.php

./src/
├── Controller
│   ├── AccueilController.php
│   ├── ArticleController.php
│   ├── ContactController.php
│   ├── PageController.php
│   ├── ProfilController.php
│   ├── RegistrationController.php
│   └── SecurityController.php
├── Entity
│   ├── Article.php
│   ├── MessageContact.php
│   └── Utilisateur.php
├── Form
│   ├── ArticleType.php
│   ├── ChangePasswordType.php
│   ├── ContactType.php
│   ├── ProfilType.php
│   └── RegistrationFormType.php
├── Kernel.php
├── Repository
│   ├── ArticleRepository.php
│   ├── MessageContactRepository.php
│   └── UtilisateurRepository.php
└── Security
    └── EmailVerifier.php

./templates/
├── accueil
│   └── index.html.twig
├── article
│   ├── admin.html.twig
│   ├── edit.html.twig
│   ├── index.html.twig
│   ├── new.html.twig
│   └── show.html.twig
├── base.html.twig
├── contact
│   └── index.html.twig
├── emails
│   └── contact.html.twig
├── page
│   ├── cgu.html.twig
│   ├── confidentialite.html.twig
│   ├── mentions_legales.html.twig
│   └── qui_sommes_nous.html.twig
├── profil
│   ├── change_password.html.twig
│   ├── edit.html.twig
│   └── index.html.twig
├── registration
│   ├── confirmation_email.html.twig
│   └── register.html.twig
└── security
    └── login.html.twig
```

Protection CSRF sur tous les formulaires

Contrôle d’accès ROLE_USER / ROLE_ADMIN

Commandes utiles

```bash
php bin/console make:entity
php bin/console make:controller
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console cache:clear
```

Licence MIT

© 2026 TechBlog Gaming. Tous droits réservés.
