# SAE3.01 - Video Home Share (VHS)

Video Home Share (VHS) est une application web interactive conçue pour rassembler les passionnés de films et de séries dans une communauté chaleureuse et dynamique. Avec l'évolution des plateformes de streaming, l'accès au divertissement n'a jamais été aussi simple, mais cela a souvent conduit à une expérience individuelle, parfois solitaire. VHS réinvente cette expérience en ajoutant une dimension sociale et collaborative, où chacun peut partager ses découvertes, échanger des avis et interagir autour de ses contenus préférés.

Notre application propose une combinaison unique de fonctionnalités pour enrichir l'expérience utilisateur : un forum pour débattre et discuter, des watchlists pour organiser vos films et séries, et une option "Watch Together" pour regarder ensemble à distance. En plus de cela, VHS intègre des quiz et des mini-jeux créés par la communauté pour un aspect ludique qui renforce les liens entre les utilisateurs. Ces fonctionnalités font de VHS bien plus qu'un simple site de streaming, mais une véritable plateforme sociale dédiée au septième art.

L’objectif de VHS est de recréer la convivialité des soirées cinéma entre amis, même à distance. Que vous soyez un cinéphile passionné ou un amateur curieux, VHS offre un espace où vous pouvez découvrir de nouveaux contenus, partager vos recommandations et vivre des moments inoubliables en groupe. Dans un marché saturé, notre application se démarque par son approche inclusive et interactive, favorisant l'engagement et le sentiment d'appartenance à une communauté unie.

---

## Fonctionnalités principales
### Watchlists
- Créez, gérez et partagez vos listes de visionnage.
- Suivez les films et séries que vous avez vus ou prévoyez de voir.
- Découvrez de nouvelles recommandations grâce à une gestion intelligente.

### Forum
- Participez à des discussions sur vos films et séries préférés.
- Répondez aux messages et échangez avec une communauté passionnée.
- Signalez les posts inappropriés pour garantir un environnement sain.

### Watch Together
- Regardez des films et séries en temps réel avec vos amis, où qu'ils soient.
- Profitez d'une expérience synchronisée dans une salle virtuelle.

### Quiz
- Testez vos connaissances sur les films et séries via des quiz créés par la communauté.
- Créez des quiz pour mettre au défi la communauté

<br>
<br>

## Diagramme de classes
![Diagramme de classes](images/diagrammeClasses.png)
<br>
<br>

## Architecture technique
### Frontend
- **HTML**, **CSS**, **SCSS**, **Bootstrap**
- **JavaScript**
- **Twig** pour la gestion des templates

### Backend
- **PHP**
- Modèle de conception **MVC**

### Base de données
- **MySQL**

### API
- **The Movie Database (TMDb)** pour obtenir les données de films et séries.

### Sécurité
- **OpenSSL** pour la protection des données.
- **OAuth2** pour la gestion des connexions sécurisées.

### Hébergement et journalisation
- Hébergé via **phpMyAdmin** sur serveur local fermé au public.
<br>
<br>




