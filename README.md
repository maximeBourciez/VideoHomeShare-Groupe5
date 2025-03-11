# 🎬 SAE3.01 - Video Home Share (VHS)

Video Home Share (VHS) est une application web interactive conçue pour rassembler les passionnés de films et de séries dans une communauté chaleureuse et dynamique. Avec l'évolution des plateformes de streaming, l'accès au divertissement n'a jamais été aussi simple, mais cela a souvent conduit à une expérience individuelle, parfois solitaire. 📺

✨ VHS réinvente cette expérience en ajoutant une dimension sociale et collaborative, où chacun peut partager ses découvertes, échanger des avis et interagir autour de ses contenus préférés.

💡 Notre application propose une combinaison unique de fonctionnalités : <br>
- Un forum pour débattre et discuter,<br>
- Des watchlists pour organiser vos films et séries,<br>
- Une option "Watch Together" pour regarder ensemble à distance,<br>
- Des quiz et mini-jeux créés par la communauté pour un aspect ludique.<br><br>

🎭 Ces fonctionnalités font de VHS bien plus qu'un simple site de streaming, mais une véritable plateforme sociale dédiée au septième art.

💬 L’objectif de VHS est de recréer la convivialité des soirées cinéma entre amis, même à distance. Que vous soyez un cinéphile passionné 🎞️ ou un amateur curieux 🍿, VHS offre un espace où vous pouvez découvrir de nouveaux contenus, partager vos recommandations et vivre des moments inoubliables en groupe.

📌 Dans un marché saturé, notre application se démarque par son approche inclusive et interactive, favorisant l'engagement et le sentiment d'appartenance à une communauté unie.
# 🚀 Fonctionnalités principales
## 📌 Watchlists

- Créez, gérez et partagez vos listes de visionnage.  <br>
- Suivez les films et séries que vous avez vus ou prévoyez de voir.  <br>
- Découvrez de nouvelles recommandations grâce à une gestion intelligente.<br>
## 💬 Forum

-  Participez à des discussions sur vos films et séries préférés.<br>
- Répondez aux messages et échangez avec une communauté passionnée.<br>
- Signalez les posts inappropriés pour garantir un environnement sain.<br>
## 🎥 Watch Together

- Regardez des films et séries en temps réel avec vos amis, où qu'ils soient.<br>
- Profitez d'une expérience synchronisée dans une salle virtuelle.<br>
## 🧠 Quiz

- Testez vos connaissances sur les films et séries via des quiz créés par la communauté.<br>
- Créez des quiz pour mettre au défi la communauté.<br>

# Diagramme de classes
![Diagramme de classes](images/diagrammeClasses.svg)
<br>
<br>
# ⚙️ Architecture technique
## 🎨 Frontend

    🏗️ HTML, CSS, SCSS, Bootstrap
    🖥️ JavaScript
    🖌️ Twig pour la gestion des templates

## 🛠️ Backend

    🐘 PHP
    🏛️ Modèle de conception MVC

## 🗄️ Base de données

    🛢️ MySQL

## 🔌 API

    🎞️ The Movie Database (TMDb) pour obtenir les données de films et séries.

## 🔐 Sécurité

    🛡️ OpenSSL pour la protection des données.
    🔑 OAuth2 pour la gestion des connexions sécurisées.

## ☁️ Hébergement et journalisation

    🏠 Hébergé sur un serveur AlwaysData pour la production
    👷 Hébergé sur un serveur fermé au public pour le développement 

<br>
    
# 🛠️ Installation
## 1️⃣ Prérequis

Avant d’installer Video Home Share (VHS), assurez-vous d’avoir les éléments suivants installés sur votre machine :
### 🔧 Logiciels nécessaires

    🐘 PHP (≥ 8.0)
    🛢️ MySQL (≥ 5.7)
    🏗️ Apache ou Nginx (serveur web)
    📦 Composer (gestionnaire de dépendances PHP)
    🌐 Node.js & npm (gestionnaire de paquets pour le frontend)
    🔌 phpMyAdmin (facultatif, pour gérer la base de données via une interface graphique)

### 📚 Dépendances utilisées

  - Backend (PHP) :
        Twig (moteur de template)
        PDO (connexion à la base de données)
        OpenSSL (sécurisation des données)
        OAuth2 Client (authentification Google & GitHub)

  - Frontend (JavaScript & CSS) :
        Bootstrap (framework CSS)
        SCSS (préprocesseur CSS)
        
### 🔑 Clé api Tmdb

Afin de permettre la récupération des films pour leur affichage, il est nécessaire de récupérer une clé API TMDB, qui est gratuite. Pour la récupérer, veuillez vous rendre sur : https://www.themoviedb.org/settings/api

## 2️⃣ Cloner le projet

Ouvrez un terminal et exécutez :
```bash
git clone https://github.com/maximeBourciez/VideoHomeShare-Groupe5
cd VideoHomeShare-Groupe5
```

## 3️⃣ Installer les dépendances
### 📦 Backend (PHP)
```bash
composer install
```
### 🎨 Frontend (JS, CSS, Bootstrap)
```bash
npm install
npm run build  # Compile les fichiers frontend
```
### 4️⃣ Configurer la base de données

  - Dupliquer le fichier config/template_constantes.yaml et le renommer en constantes.yaml
  - Modifier les accès à la base de données dans constantes.yaml :
        
        DB_HOST=localhost
        DB_NAME=vhs_database
        DB_USER=root
        DB_PASSWORD=VotreMotDePasse


### 📂 Importer la structure de la base avec :

#### 📥 Récupération de la base de données

    Envoyez un mail à videohomeshare5@gmail.com pour obtenir le fichier SQL contenant la structure et les données.

#### 🛢️ Importation de la base de données

- Ouvrez phpMyAdmin ou un terminal MySQL.
- Créez une base de données :
    
    ```sql 
    CREATE DATABASE vhs_database;
    ```
    
- Importez le fichier .sql reçu par mail dans phpMyAdmin ou via le terminal :
    ```bash
    mysql -u root -p vhs_database < vhs_database.sql
    ```

## 5️⃣ Lancer l'application
🔥 Démarrer le serveur
```php
php -S localhost:8000 -t public
```

# 📁 Arborescence

À la racine du dépôt se trouvent :

- index.php : Point d’entrée principal de l’application.
- include.php : Centralise les inclusions et l’initialisation des dépendances.
- README.md : Description générale du projet.
- Doxyfile : Fichier de configuration pour générer la documentation avec Doxygen.
- .gitignore : Exclut les fichiers sensibles du dépôt Git.
- composer.json / composer.lock : Gestion des dépendances PHP via Composer.
- package.json / package-lock.json : Gestion des dépendances JavaScript via npm.

Le projet est organisé en plusieurs dossiers :

- config/ → Contient les constantes et l’initialisation de Twig.
- controllers/ → Contient les contrôleurs de l’application.
- css/ → Contient les feuilles de style CSS.
- docs/ → Contient la documentation générée avec Doxygen.
- images/ → Contient les ressources graphiques.
- js/ → Contient les scripts JavaScript.
- modeles/ → Contient les modèles gérant la logique métier.
- scss/ → Contient les fichiers SCSS pour la personnalisation de Bootstrap.
- templates/ → Contient les vues de l’application sous forme de templates Twig.

# 📖 Documentation

Pour assurer une meilleure maintenabilité du projet, nous avons généré une documentation technique avec Doxygen, disponible à ce lien :

https://maximebourciez.github.io/VideoHomeShare-Groupe5/

Elle permet de comprendre l’architecture de notre projet, le rôle des classes et des méthodes, ainsi que les interactions entre les différents composants.
### 📌 Contenu :

    📂 Structure du projet : Explication du modèle MVC utilisé.
    📝 Documentation des classes : Description détaillée des modèles, contrôleurs et DAO.
    📊 Diagrammes UML : Visualisation des relations entre les entités du projet.
