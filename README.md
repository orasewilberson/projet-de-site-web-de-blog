# PROJET_BLOG_POO

Un mini-système de blog développé en **PHP orienté objet**, organisé en modules (Blog, Auth, Framework) et respectant une architecture propre et extensible.

## Description du projet

Ce projet est un **site de blog** permettant d’afficher des articles classés par **catégories**, avec une structure basée sur :

* PHP orienté objet (POO)
* Un mini-framework maison (routing, rendu HTML, services)
* Un système de migrations et seeds basé sur **Phinx**
* Un système d’authentification simple
* Une architecture modulaire propre

L’objectif est de montrer la maîtrise de la POO, de la structuration d’un projet web, de la séparation des responsabilités et de la logique métier.


## Architecture du projet

```
PROJET_BLOG_POO/
│
├── composer.json
├── phinx.php
├── phinx.yml
│
├── src/
│   ├── Auth/
│   │   ├── db/
│   │   │   ├── migrations/
│   │   │   └── seeds/
│   │   └── ...
│   │
│   ├── Blog/
│   │   ├── db/
│   │   │   ├── migrations/
│   │   │   └── seeds/
│   │   └── ...
│   │
│   ├── Framework/
│   │   ├── App.php
│   │   ├── Renderer/
│   │   │   ├── PHPRenderer.php
│   │   │   ├── TwigRenderer.php
│   │   │   └── RendererInterface.php
│   │   └── ...
│   │
│   └── ...
│
└── public/
    └── index.php
```


## Fonctionnalités principales

### **Blog**

* Affichage des articles
* Affichage des catégories
* Filtrage des articles par catégorie
* Affichage d’un article détaillé
* Gestion des images d’articles

### **Authentification**

* Login
* Gestion des utilisateurs via migrations + seeds

### **Base de données**

* Migrations Phinx
* Seeds pour remplir automatiquement :

  * utilisateurs
  * articles
  * catégories

### 🔹 **Framework maison**

* Système de routing simple
* Moteur de rendu (PHP ou Twig)
* Structure modulaire type MVC


## Technologies utilisées

* **PHP 8+**
* **Phinx** (migrations)
* **Composer**
* **Twig** (optionnel)
* **MySQL / MariaDB**
* **XAMPP** (environnement local)


## Installation et démarrage

### Cloner le projet

```
git clone https://github.com/orasewilberson/projet-de-site-web-de-blog.git
```

### Installer les dépendances

```
composer install
```

### 3️⃣ Configurer la base de données

Modifier `phinx.php` et `phinx.yml` selon votre environnement :

```
host: localhost
user: root
pass: 
name: blog_poo
```

### Lancer les migrations

```
vendor/bin/phinx migrate
```

### Lancer les seeds

```
vendor/bin/phinx seed:run
```

### Démarrer l’application

Placez-vous dans le répertoire du projet :

```
php -S localhost:8000 -t public
```

Accéder au site sur :

[http://localhost:8000/blog]


## Auteur

**Wilberson Orase**
Étudiant passionné de développement web, systèmes distribués et architecture logicielle.


## Objectif du projet

Ce projet a été conçu pour démontrer :

* La maîtrise de la POO en PHP
* La capacité à structurer une application modulable
* L’utilisation de migrations/seeds dans un projet PHP
* L’implémentation d’un mini-framework MVC personnalisé
* Une vision propre et professionnelle du développement backend


## Contact

Pour toute information :
**[orasewilberson@gmail.com](mailto:orasewilberson@gmail.com)**
