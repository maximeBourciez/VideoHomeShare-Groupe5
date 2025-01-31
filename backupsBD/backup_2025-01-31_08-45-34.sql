-- Sauvegarde de la base mbourciez_pro - 2025-01-31 08:45:34

SET FOREIGN_KEY_CHECKS = 0;

-- Structure de la table `vhs_commentercollection` --
CREATE TABLE `vhs_commentercollection` (
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` decimal(2,0) NOT NULL,
  `avis` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estPositif` tinyint(1) NOT NULL,
  `idCollectionTmdb` decimal(10,0) NOT NULL,
  `dateCommentaire` datetime NOT NULL,
  PRIMARY KEY (`idUtilisateur`,`idCollectionTmdb`),
  CONSTRAINT `vhs_commentercollection_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`),
  CONSTRAINT `vhs_commentercollection_chk_1` CHECK (((`note` >= 1) and (`note` <= 10)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_commentercontenu` --
CREATE TABLE `vhs_commentercontenu` (
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` decimal(2,0) NOT NULL,
  `avis` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estPositif` tinyint(1) NOT NULL,
  `idContenuTmdb` decimal(10,0) NOT NULL,
  `dateCommentaire` datetime NOT NULL,
  PRIMARY KEY (`idUtilisateur`,`idContenuTmdb`),
  CONSTRAINT `vhs_commentercontenu_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`),
  CONSTRAINT `vhs_commentercontenu_chk_1` CHECK (((`note` >= 1) and (`note` <= 10)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_commenterserie` --
CREATE TABLE `vhs_commenterserie` (
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` decimal(2,0) NOT NULL,
  `avis` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estPositif` tinyint(1) NOT NULL,
  `idSerieTmdb` decimal(10,0) NOT NULL,
  `dateCommentaire` datetime NOT NULL,
  PRIMARY KEY (`idUtilisateur`,`idSerieTmdb`),
  CONSTRAINT `vhs_commenterserie_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`),
  CONSTRAINT `vhs_commenterserie_chk_1` CHECK (((`note` >= 1) and (`note` <= 10)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_concerner` --
CREATE TABLE `vhs_concerner` (
  `iContenuTmdb` decimal(10,0) NOT NULL,
  `idSalle` int NOT NULL,
  `rang` decimal(2,0) NOT NULL,
  PRIMARY KEY (`iContenuTmdb`,`idSalle`),
  KEY `idSalle` (`idSalle`),
  CONSTRAINT `vhs_concerner_ibfk_1` FOREIGN KEY (`idSalle`) REFERENCES `vhs_salle` (`idSalle`) ON DELETE CASCADE,
  CONSTRAINT `vhs_concerner_chk_1` CHECK ((`rang` >= 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_contenircollection` --
CREATE TABLE `vhs_contenircollection` (
  `idCollectionTmdb` decimal(10,0) NOT NULL,
  `idWatchlist` int NOT NULL,
  `rang` decimal(2,0) NOT NULL,
  PRIMARY KEY (`idCollectionTmdb`,`idWatchlist`),
  KEY `idWatchlist` (`idWatchlist`),
  CONSTRAINT `vhs_contenircollection_ibfk_1` FOREIGN KEY (`idWatchlist`) REFERENCES `vhs_watchlist` (`idWatchlist`) ON DELETE CASCADE,
  CONSTRAINT `CHK_rang_contenirCollection` CHECK ((`rang` >= 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_contenircontenu` --
CREATE TABLE `vhs_contenircontenu` (
  `idContenuTmdb` decimal(10,0) NOT NULL,
  `idWatchlist` int NOT NULL,
  `rang` decimal(2,0) NOT NULL,
  PRIMARY KEY (`idContenuTmdb`,`idWatchlist`),
  KEY `idWatchlist` (`idWatchlist`),
  CONSTRAINT `vhs_contenircontenu_ibfk_1` FOREIGN KEY (`idWatchlist`) REFERENCES `vhs_watchlist` (`idWatchlist`) ON DELETE CASCADE,
  CONSTRAINT `CHK_rang_contenirContenu` CHECK ((`rang` >= 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_fil` --
CREATE TABLE `vhs_fil` (
  `idFil` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dateC` date NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idFil`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_jouer` --
CREATE TABLE `vhs_jouer` (
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idQuizz` int NOT NULL,
  `score` decimal(2,0) NOT NULL,
  PRIMARY KEY (`idUtilisateur`,`idQuizz`),
  KEY `idQuizz` (`idQuizz`),
  CONSTRAINT `vhs_jouer_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`) ON DELETE CASCADE,
  CONSTRAINT `vhs_jouer_ibfk_2` FOREIGN KEY (`idQuizz`) REFERENCES `vhs_quizz` (`idQuizz`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_message` --
CREATE TABLE `vhs_message` (
  `idMessage` int NOT NULL AUTO_INCREMENT,
  `valeur` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dateC` date NOT NULL,
  `idMessageParent` int DEFAULT NULL,
  `idFil` int NOT NULL,
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idMessage`),
  KEY `idMessageParent` (`idMessageParent`),
  KEY `idFil` (`idFil`),
  KEY `idUtilisateur` (`idUtilisateur`),
  CONSTRAINT `vhs_message_ibfk_1` FOREIGN KEY (`idMessageParent`) REFERENCES `vhs_message` (`idMessage`) ON DELETE CASCADE,
  CONSTRAINT `vhs_message_ibfk_2` FOREIGN KEY (`idFil`) REFERENCES `vhs_fil` (`idFil`) ON DELETE CASCADE,
  CONSTRAINT `vhs_message_ibfk_3` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_notification` --
CREATE TABLE `vhs_notification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contenu` text COLLATE utf8mb4_unicode_ci,
  `dateC` datetime NOT NULL,
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`,`idUtilisateur`),
  KEY `idUtilisateur` (`idUtilisateur`),
  CONSTRAINT `vhs_notification_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_parlerdetheme` --
CREATE TABLE `vhs_parlerdetheme` (
  `idFil` int NOT NULL,
  `idTheme` int NOT NULL,
  PRIMARY KEY (`idFil`,`idTheme`),
  KEY `idTheme` (`idTheme`),
  CONSTRAINT `vhs_parlerdetheme_ibfk_1` FOREIGN KEY (`idFil`) REFERENCES `vhs_fil` (`idFil`) ON DELETE CASCADE,
  CONSTRAINT `vhs_parlerdetheme_ibfk_2` FOREIGN KEY (`idTheme`) REFERENCES `vhs_theme` (`idTheme`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_partager` --
CREATE TABLE `vhs_partager` (
  `idWatchlist` int NOT NULL,
  `idUtilisateurC` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idUtilisateurP` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idWatchlist`,`idUtilisateurC`,`idUtilisateurP`),
  KEY `idUtilisateurC` (`idUtilisateurC`),
  KEY `idUtilisateurP` (`idUtilisateurP`),
  CONSTRAINT `vhs_partager_ibfk_1` FOREIGN KEY (`idWatchlist`) REFERENCES `vhs_watchlist` (`idWatchlist`) ON DELETE CASCADE,
  CONSTRAINT `vhs_partager_ibfk_2` FOREIGN KEY (`idUtilisateurC`) REFERENCES `vhs_utilisateur` (`idUtilisateur`),
  CONSTRAINT `vhs_partager_ibfk_3` FOREIGN KEY (`idUtilisateurP`) REFERENCES `vhs_utilisateur` (`idUtilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_portersurcollection` --
CREATE TABLE `vhs_portersurcollection` (
  `idQuizz` int NOT NULL,
  `idCollectionTmdb` decimal(10,0) NOT NULL,
  PRIMARY KEY (`idQuizz`,`idCollectionTmdb`),
  CONSTRAINT `vhs_portersurcollection_ibfk_1` FOREIGN KEY (`idQuizz`) REFERENCES `vhs_quizz` (`idQuizz`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_portersurcontenu` --
CREATE TABLE `vhs_portersurcontenu` (
  `idQuizz` int NOT NULL,
  `idContenuTmdb` decimal(10,0) NOT NULL,
  PRIMARY KEY (`idQuizz`,`idContenuTmdb`),
  CONSTRAINT `vhs_portersurcontenu_ibfk_1` FOREIGN KEY (`idQuizz`) REFERENCES `vhs_quizz` (`idQuizz`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_portersurtheme` --
CREATE TABLE `vhs_portersurtheme` (
  `idQuizz` int NOT NULL,
  `idTheme` int NOT NULL,
  PRIMARY KEY (`idQuizz`,`idTheme`),
  KEY `idTheme` (`idTheme`),
  CONSTRAINT `vhs_portersurtheme_ibfk_1` FOREIGN KEY (`idQuizz`) REFERENCES `vhs_quizz` (`idQuizz`) ON DELETE CASCADE,
  CONSTRAINT `vhs_portersurtheme_ibfk_2` FOREIGN KEY (`idTheme`) REFERENCES `vhs_theme` (`idTheme`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_question` --
CREATE TABLE `vhs_question` (
  `idQuestion` int NOT NULL AUTO_INCREMENT,
  `valeur` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rang` decimal(2,0) NOT NULL,
  `urlImage` varchar(2083) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idQuizz` int NOT NULL,
  PRIMARY KEY (`idQuestion`),
  KEY `idQuizz` (`idQuizz`),
  CONSTRAINT `vhs_question_ibfk_1` FOREIGN KEY (`idQuizz`) REFERENCES `vhs_quizz` (`idQuizz`) ON DELETE CASCADE,
  CONSTRAINT `vhs_question_chk_1` CHECK ((`rang` >= 1))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_quizz` --
CREATE TABLE `vhs_quizz` (
  `idQuizz` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `difficulte` decimal(1,0) NOT NULL,
  `dateC` date NOT NULL,
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idQuizz`),
  KEY `vhs_quizz_idUtilisateur` (`idUtilisateur`),
  CONSTRAINT `vhs_quizz_idUtilisateur` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`),
  CONSTRAINT `vhs_quizz_chk_1` CHECK (((`difficulte` >= 1) and (`difficulte` <= 4)))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_reagir` --
CREATE TABLE `vhs_reagir` (
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idMessage` int NOT NULL,
  `reaction` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idUtilisateur`,`idMessage`),
  KEY `idMessage` (`idMessage`),
  CONSTRAINT `vhs_reagir_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`),
  CONSTRAINT `vhs_reagir_ibfk_2` FOREIGN KEY (`idMessage`) REFERENCES `vhs_message` (`idMessage`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_reponse` --
CREATE TABLE `vhs_reponse` (
  `idReponse` int NOT NULL AUTO_INCREMENT,
  `valeur` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rang` decimal(1,0) NOT NULL,
  `estVraie` tinyint(1) NOT NULL,
  `idQuestion` int NOT NULL,
  PRIMARY KEY (`idReponse`),
  KEY `idQuestion` (`idQuestion`),
  CONSTRAINT `vhs_reponse_ibfk_1` FOREIGN KEY (`idQuestion`) REFERENCES `vhs_question` (`idQuestion`) ON DELETE CASCADE,
  CONSTRAINT `CHK_rang_reponse` CHECK (((`rang` >= 1) and (`rang` <= 4)))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_salle` --
CREATE TABLE `vhs_salle` (
  `idSalle` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estPublique` tinyint(1) NOT NULL,
  `code` decimal(4,0) DEFAULT NULL,
  `rangCourant` decimal(2,0) NOT NULL,
  `genre` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nbpersonne` decimal(2,0) NOT NULL,
  PRIMARY KEY (`idSalle`),
  CONSTRAINT `CHK_code` CHECK (((`estPublique` = false) or (`code` is not null))),
  CONSTRAINT `vhs_salle_chk_1` CHECK ((`nbpersonne` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_se_trouver` --
CREATE TABLE `vhs_se_trouver` (
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idSalle` int NOT NULL,
  `muet` tinyint(1) NOT NULL,
  `banni` tinyint(1) NOT NULL,
  `exclu` tinyint(1) NOT NULL,
  `role` enum('Hote','Administrateur') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idUtilisateur`,`idSalle`),
  KEY `idSalle` (`idSalle`),
  CONSTRAINT `vhs_se_trouver_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`) ON DELETE CASCADE,
  CONSTRAINT `vhs_se_trouver_ibfk_2` FOREIGN KEY (`idSalle`) REFERENCES `vhs_salle` (`idSalle`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_signalement` --
CREATE TABLE `vhs_signalement` (
  `idSignalement` int NOT NULL AUTO_INCREMENT,
  `raison` enum('Autre','Spam','Contenu inapproprié','Contenu trompant','Discrimination ou harcèlement') COLLATE utf8mb4_unicode_ci NOT NULL,
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idMessage` int NOT NULL,
  PRIMARY KEY (`idSignalement`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `idMessage` (`idMessage`),
  CONSTRAINT `vhs_signalement_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`) ON DELETE CASCADE,
  CONSTRAINT `vhs_signalement_ibfk_2` FOREIGN KEY (`idMessage`) REFERENCES `vhs_message` (`idMessage`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_theme` --
CREATE TABLE `vhs_theme` (
  `idTheme` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idTheme`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_utilisateur` --
CREATE TABLE `vhs_utilisateur` (
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pseudo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vraiNom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail` varchar(320) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mdp` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Moderateur','Utilisateur') COLLATE utf8mb4_unicode_ci NOT NULL,
  `urlImageProfil` varchar(2093) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urlImageBanniere` varchar(2093) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateI` datetime DEFAULT NULL,
  `estValider` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idUtilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `vhs_watchlist` --
CREATE TABLE `vhs_watchlist` (
  `idWatchlist` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dateC` date NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estPublique` tinyint(1) DEFAULT NULL,
  `idUtilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idWatchlist`),
  KEY `idUtilisateur` (`idUtilisateur`),
  CONSTRAINT `vhs_watchlist_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `vhs_utilisateur` (`idUtilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;

-- Données de la table `vhs_commentercollection` --
INSERT INTO `vhs_commentercollection` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idCollectionTmdb`, `dateCommentaire`) VALUES ('coucou', '', '4', 'Une saga épique qui réunit les plus grands super-héros de l\'univers Marvel.', '1', '1', '2025-01-07 10:57:10');
INSERT INTO `vhs_commentercollection` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idCollectionTmdb`, `dateCommentaire`) VALUES ('DameHalcyon', '', '5', 'Une saga incontournable pour tous les fans de super-héros.', '1', '1', '2025-01-07 11:20:46');
INSERT INTO `vhs_commentercollection` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idCollectionTmdb`, `dateCommentaire`) VALUES ('FranzouGame', '', '5', 'Une saga qui a marqué l\'histoire du cinéma.', '1', '1', '2025-01-09 14:52:34');
INSERT INTO `vhs_commentercollection` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idCollectionTmdb`, `dateCommentaire`) VALUES ('Kong', '', '4', 'Une saga qui mêle action, humour et émotion avec brio.', '1', '1', '2025-01-09 15:25:07');
INSERT INTO `vhs_commentercollection` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idCollectionTmdb`, `dateCommentaire`) VALUES ('Lifzerr', '', '5', 'Une saga qui a su réinventer le genre des super-héros.', '1', '1', '2025-01-11 12:29:02');

-- Données de la table `vhs_commentercontenu` --
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('coucou', 'Un bon moment', '4', 'Un film globalement satisfaisant, malgré quelques longueurs.', '1', '550', '2025-01-07 10:57:10');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('coucou', 'Je n\'ai pas aimé', '1', 'Malgré une belle esthétique, Blade Runner 2049 traîne en longueur et manque d\'émotion. Le scénario est confus, et les personnages peinent à égaler l\'impact du film original.', '0', '335984', '2024-12-04 16:28:01');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('coucou', 'Un peu décevant', '2', 'L\'amour est au cœur de l\'intrigue, mais cela manque de profondeur.', '0', '959604', '2025-01-07 11:20:46');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Excellent film', '5', 'Une belle réalisation avec un scénario captivant.', '1', '120', '2025-01-09 14:52:34');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Correct', '3', 'Une expérience mitigée, mais cela reste appréciable.', '1', '122', '2025-01-09 15:25:07');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Un chef-d\'œuvre', '5', 'Un excellent film à tous les niveaux.', '1', '410', '2025-01-11 12:29:02');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Très bon', '4', 'Une histoire passionnante et des acteurs convaincants.', '1', '457', '2024-12-19 10:54:04');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Bonne surprise', '5', 'Un film qui dépasse les attentes, avec une ambiance remarquable.', '1', '549', '2024-12-19 10:52:47');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Captivant', '5', 'Un récit intense et une mise en scène soignée.', '1', '550', '2024-12-18 21:03:22');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Une pépite', '5', 'Un film marquant et très bien réalisé.', '1', '554', '2024-12-19 09:18:18');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'À voir', '5', 'Un film vraiment captivant et visuellement impressionnant.', '1', '745', '2024-12-19 10:53:17');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Décevant', '1', 'Une intrigue peu convaincante et des personnages fades.', '0', '965', '2024-12-19 10:56:32');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Sans intérêt', '1', 'Rien de mémorable, une grande déception.', '0', '5465', '2024-12-19 09:17:47');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Une grosse déception', '1', 'Le scénario ne tient pas la route et l\'ensemble est ennuyeux.', '0', '11324', '2024-12-18 17:20:03');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Mauvais film', '1', 'Un film à oublier rapidement.', '0', '45213', '2024-12-19 09:13:27');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Kraven', '4', 'je suis un chasseur lalalala', '1', '539972', '0000-00-00 00:00:00');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'INCROYABLE', '5', 'Pour de vrai sonic 1 et 2 nul mais le 3 alors', '1', '939243', '0000-00-00 00:00:00');
INSERT INTO `vhs_commentercontenu` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idContenuTmdb`, `dateCommentaire`) VALUES ('Lifzerr', 'J\'adore', '5', 'MEILLEUR FILM DU MONDE !!!!', '1', '335984', '2024-12-04 16:28:01');

-- Données de la table `vhs_commenterserie` --
INSERT INTO `vhs_commenterserie` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idSerieTmdb`, `dateCommentaire`) VALUES ('coucou', 'Squid', '4', 'Game', '1', '93405', '2025-01-23 10:50:45');
INSERT INTO `vhs_commenterserie` (`idUtilisateur`, `titre`, `note`, `avis`, `estPositif`, `idSerieTmdb`, `dateCommentaire`) VALUES ('FranzouGame', 'Tu es chinois ?', '5', 'Squid Gameee 🔥', '1', '93405', '2025-01-23 12:09:33');

-- Données de la table `vhs_concerner` --
INSERT INTO `vhs_concerner` (`iContenuTmdb`, `idSalle`, `rang`) VALUES ('120', '1', '3');
INSERT INTO `vhs_concerner` (`iContenuTmdb`, `idSalle`, `rang`) VALUES ('122', '1', '4');
INSERT INTO `vhs_concerner` (`iContenuTmdb`, `idSalle`, `rang`) VALUES ('410', '1', '5');
INSERT INTO `vhs_concerner` (`iContenuTmdb`, `idSalle`, `rang`) VALUES ('457', '1', '6');
INSERT INTO `vhs_concerner` (`iContenuTmdb`, `idSalle`, `rang`) VALUES ('549', '1', '7');
INSERT INTO `vhs_concerner` (`iContenuTmdb`, `idSalle`, `rang`) VALUES ('550', '1', '1');
INSERT INTO `vhs_concerner` (`iContenuTmdb`, `idSalle`, `rang`) VALUES ('554', '1', '8');
INSERT INTO `vhs_concerner` (`iContenuTmdb`, `idSalle`, `rang`) VALUES ('335984', '1', '9');
INSERT INTO `vhs_concerner` (`iContenuTmdb`, `idSalle`, `rang`) VALUES ('959604', '1', '2');

-- Données de la table `vhs_contenircollection` --
INSERT INTO `vhs_contenircollection` (`idCollectionTmdb`, `idWatchlist`, `rang`) VALUES ('1', '1', '1');

-- Données de la table `vhs_contenircontenu` --
INSERT INTO `vhs_contenircontenu` (`idContenuTmdb`, `idWatchlist`, `rang`) VALUES ('120', '1', '3');
INSERT INTO `vhs_contenircontenu` (`idContenuTmdb`, `idWatchlist`, `rang`) VALUES ('122', '1', '4');
INSERT INTO `vhs_contenircontenu` (`idContenuTmdb`, `idWatchlist`, `rang`) VALUES ('410', '1', '5');
INSERT INTO `vhs_contenircontenu` (`idContenuTmdb`, `idWatchlist`, `rang`) VALUES ('457', '1', '6');
INSERT INTO `vhs_contenircontenu` (`idContenuTmdb`, `idWatchlist`, `rang`) VALUES ('549', '1', '7');
INSERT INTO `vhs_contenircontenu` (`idContenuTmdb`, `idWatchlist`, `rang`) VALUES ('550', '1', '1');
INSERT INTO `vhs_contenircontenu` (`idContenuTmdb`, `idWatchlist`, `rang`) VALUES ('554', '1', '8');
INSERT INTO `vhs_contenircontenu` (`idContenuTmdb`, `idWatchlist`, `rang`) VALUES ('335984', '1', '9');
INSERT INTO `vhs_contenircontenu` (`idContenuTmdb`, `idWatchlist`, `rang`) VALUES ('959604', '1', '2');

-- Données de la table `vhs_fil` --
INSERT INTO `vhs_fil` (`idFil`, `titre`, `dateC`, `description`) VALUES ('1', 'Les meilleures séries de Science-Fiction', '2024-12-17', 'Séries SF qui nous fascinent.');
INSERT INTO `vhs_fil` (`idFil`, `titre`, `dateC`, `description`) VALUES ('2', 'Les drames qui nous ont fait pleurer', '2024-12-17', 'Drames qui nous émeuvent aux larmes.');
INSERT INTO `vhs_fil` (`idFil`, `titre`, `dateC`, `description`) VALUES ('3', 'Les films d\'action à ne pas manquer', '2024-12-17', 'Films d\'action palpitants à voir.');
INSERT INTO `vhs_fil` (`idFil`, `titre`, `dateC`, `description`) VALUES ('4', 'Les dessins animés de notre enfance', '2024-12-17', 'Dessins animés qui nous font sourire.');
INSERT INTO `vhs_fil` (`idFil`, `titre`, `dateC`, `description`) VALUES ('5', 'Les aventures épiques à la télévision', '2024-12-17', 'Séries d\'aventures captivantes.');
INSERT INTO `vhs_fil` (`idFil`, `titre`, `dateC`, `description`) VALUES ('6', 'Les films fantastiques qui nous transportent', '2024-12-17', 'Films fantastiques à découvrir.');
INSERT INTO `vhs_fil` (`idFil`, `titre`, `dateC`, `description`) VALUES ('7', 'Les thrillers qui nous tiennent en haleine', '2024-12-17', 'Thrillers qui nous font sursauter.');
INSERT INTO `vhs_fil` (`idFil`, `titre`, `dateC`, `description`) VALUES ('8', 'Les mystères à résoudre dans les séries', '2024-12-17', 'Séries mystérieuses à déchiffrer.');

-- Données de la table `vhs_jouer` --
INSERT INTO `vhs_jouer` (`idUtilisateur`, `idQuizz`, `score`) VALUES ('coucou', '1', '10');

-- Données de la table `vhs_message` --
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('1', 'La série \"The Expanse\" est incroyable!', '2024-12-17', NULL, '1', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('2', 'Je recommande \"Stranger Things\" pour les fans de SF.', '2024-12-17', NULL, '1', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('3', 'Avez-vous vu \"Black Mirror\"? C\'est fascinant!', '2024-12-17', NULL, '1', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('4', 'Les effets spéciaux de \"The Mandalorian\" sont époustouflants.', '2024-12-17', NULL, '1', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('5', '\"This Is Us\" est un drame qui touche le cœur.', '2024-12-17', NULL, '2', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('6', 'Les personnages de \"The Crown\" sont si bien développés.', '2024-12-17', NULL, '2', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('7', 'Je pleure à chaque épisode de \"The Handmaid\'s Tale\".', '2024-12-17', NULL, '2', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('8', 'Les films de Christopher Nolan sont des chefs-d\'œuvre.', '2024-12-17', NULL, '3', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('9', 'N\'oubliez pas \"Die Hard\", un classique de l\'action!', '2024-12-17', NULL, '3', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('10', 'Les films de James Bond sont toujours divertissants.', '2024-12-17', NULL, '3', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('11', 'Nostalgie avec \"Les Mystérieuses Cités d\'Or\".', '2024-12-17', NULL, '4', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('12', 'Les dessins animés de Pixar sont intemporels.', '2024-12-17', NULL, '4', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('13', 'J\'adore \"Avatar: The Last Airbender\", un chef-d\'œuvre!', '2024-12-17', NULL, '4', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('14', '\"Game of Thrones\" est une aventure épique.', '2024-12-17', NULL, '5', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('15', 'Les rebondissements de \"Lost\" sont inoubliables.', '2024-12-17', NULL, '5', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('16', 'Les aventures de \"Indiana Jones\" sont inoubliables.', '2024-12-17', NULL, '5', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('17', 'Le dernier épisode de \"The Witcher\" était incroyable!', '2024-12-17', NULL, '6', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('18', 'Les effets spéciaux de \"Avatar\" sont à couper le souffle.', '2024-12-17', NULL, '6', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('19', 'Les films fantastiques de Studio Ghibli sont magiques.', '2024-12-17', NULL, '6', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('20', 'Le dernier épisode de \"Breaking Bad\" était un thriller incroyable!', '2024-12-17', NULL, '7', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('21', 'J\'adore les thrillers psychologiques comme \"Mindhunter\".', '2024-12-17', NULL, '7', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('22', 'Les films de Hitchcock sont des classiques du thriller.', '2024-12-17', NULL, '7', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('23', 'Les séries policières comme \"Sherlock\" sont captivantes.', '2024-12-17', NULL, '8', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('24', 'J\'adore les énigmes de \"True Detective\".', '2024-12-17', NULL, '8', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('25', 'Les mystères de \"The OA\" sont fascinants.', '2024-12-17', NULL, '8', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('26', 'La saison 2 de \"The Mandalorian\" était incroyable!', '2024-12-17', NULL, '1', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('27', 'Je suis fan de \"Firefly\", une série culte!', '2024-12-17', NULL, '1', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('28', 'Les voyages dans le temps dans \"Dark\" sont fascinants.', '2024-12-17', NULL, '1', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('29', 'Les personnages de \"Stranger Things\" sont attachants.', '2024-12-17', NULL, '1', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('30', 'Les drames de Netflix sont souvent très bien écrits.', '2024-12-17', NULL, '2', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('31', 'Je n\'arrive pas à croire la fin de \"This Is Us\".', '2024-12-17', NULL, '2', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('32', 'Les histoires de \"The Handmaid\'s Tale\" sont puissantes.', '2024-12-17', NULL, '2', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('33', 'Les films de super-héros sont toujours divertissants.', '2024-12-17', NULL, '3', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('34', 'Les scènes d\'action dans \"Mad Max\" sont épiques!', '2024-12-17', NULL, '3', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('35', 'Les films d\'action des années 80 sont inoubliables.', '2024-12-17', NULL, '3', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('36', 'Les dessins animés de notre enfance ont une magie unique.', '2024-12-17', NULL, '4', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('37', 'Les histoires de \"Dragon Ball\" sont intemporelles.', '2024-12-17', NULL, '4', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('38', 'Les films d\'animation de Studio Ghibli sont magnifiques.', '2024-12-17', NULL, '4', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('39', 'Les intrigues de \"Game of Thrones\" sont captivantes.', '2024-12-17', NULL, '5', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('40', 'Les batailles dans \"The Witcher\" sont impressionnantes.', '2024-12-17', NULL, '5', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('41', 'Les aventures de \"The Boys\" sont hilarantes!', '2024-12-17', NULL, '5', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('42', 'Les films de fantasy comme \"Le Seigneur des Anneaux\" sont épiques.', '2024-12-17', NULL, '6', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('43', 'Les histoires de \"Harry Potter\" sont magiques.', '2024-12-17', NULL, '6', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('44', 'Les films de Tim Burton ont un style unique.', '2024-12-17', NULL, '6', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('45', 'Les thrillers psychologiques me tiennent en haleine.', '2024-12-17', NULL, '7', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('46', 'Les films d\'horreur comme \"Get Out\" sont dérangeants.', '2024-12-17', NULL, '7', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('47', 'Les mystères de \"Sherlock\" sont brillants.', '2024-12-17', NULL, '8', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('48', 'Les énigmes de \"The Killing\" sont captivantes.', '2024-12-17', NULL, '8', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('49', 'Les séries de science-fiction comme \"Altered Carbon\" sont fascinantes.', '2024-12-17', NULL, '1', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('50', 'Les drames historiques comme \"Chernobyl\" sont puissants.', '2024-12-17', NULL, '2', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('51', 'Les films d\'animation de Pixar sont toujours touchants.', '2024-12-17', NULL, '4', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('52', 'Les rebondissements de \"Westworld\" sont incroyables.', '2024-12-17', NULL, '1', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('53', 'Les histoires de \"The Umbrella Academy\" sont uniques.', '2024-12-17', NULL, '1', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('54', 'Les films de science-fiction des années 90 sont nostalgiques.', '2024-12-17', NULL, '1', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('55', 'Les drames comme \"Big Little Lies\" sont captivants.', '2024-12-17', NULL, '2', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('56', 'Les personnages de \"The Crown\" sont fascinants.', '2024-12-17', NULL, '2', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('57', 'Les films d\'action de Jason Statham sont toujours divertissants.', '2024-12-17', NULL, '3', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('58', 'Les dessins animés de Disney sont intemporels.', '2024-12-17', NULL, '4', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('59', 'Les aventures de \"Avatar\" sont visuellement époustouflantes.', '2024-12-17', NULL, '6', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('60', 'Les thrillers comme \"Prisoners\" sont intenses.', '2024-12-17', NULL, '7', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('61', 'Les mystères de \"The Sinner\" sont captivants.', '2024-12-17', NULL, '8', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('62', 'Je trouve que Breaking Bad est la meilleure série de tous les temps', '2024-12-17', NULL, '7', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('63', 'Totalement d\'accord! La transformation de Walter White est incroyable', '2024-12-17', '62', '7', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('64', 'Le jeu de Bryan Cranston est vraiment exceptionnel', '2024-12-17', '63', '7', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('65', 'Stranger Things devient de plus en plus captivant', '2024-12-17', NULL, '1', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('66', 'La saison 4 était vraiment intense', '2024-12-17', '65', '1', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('67', 'Le personnage d\'Eleven est de mieux en mieux développé', '2024-12-17', '66', '1', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('68', 'Avatar est visuellement époustouflant', '2024-12-17', NULL, '6', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('69', 'Les effets spéciaux sont révolutionnaires', '2024-12-17', '68', '6', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('70', 'La 3D était vraiment immersive', '2024-12-17', '69', '6', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('71', 'Les batailles dans Game of Thrones sont spectaculaires', '2024-12-17', NULL, '5', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('72', 'La bataille des bâtards est mon moment préféré', '2024-12-17', '71', '5', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('73', 'La mise en scène était incroyable', '2024-12-17', '72', '5', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('74', 'Baby Yoda est le meilleur personnage de The Mandalorian', '2024-12-17', NULL, '1', 'FranzouGame');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('75', 'Sa relation avec Mando est touchante', '2024-12-17', '74', '1', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('76', 'Chaque scène avec lui est mémorable', '2024-12-17', '75', '1', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('80', 'Netflix produit vraiment des séries de qualité', '2024-12-17', NULL, '2', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('81', 'Black Mirror est leur meilleure série', '2024-12-17', '80', '2', 'Lifzerr');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('82', 'Chaque épisode est une nouvelle surprise', '2024-12-17', '81', '2', 'Kong');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('83', 'Les scènes de combat dans The Witcher sont impressionnantes', '2024-12-17', NULL, '5', 'DameHalcyon');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('84', 'Henry Cavill joue parfaitement Geralt', '2024-12-17', '83', '5', 'coucou');
INSERT INTO `vhs_message` (`idMessage`, `valeur`, `dateC`, `idMessageParent`, `idFil`, `idUtilisateur`) VALUES ('85', 'La chorégraphie des combats est incroyable', '2024-12-17', '84', '5', 'coucou');

-- Données de la table `vhs_notification` --
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('5', 'sylvaintr a répondu à un de vos messages dans le fil : TEst', '2025-01-06 16:44:20', 'coucou');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('6', 'sylvaintr a répondu à un de vos messages dans le fil : aaaa', '2025-01-07 15:03:10', 'Lifzerr');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('7', 'sylvaintr a réagi à un de vos messages dans le fil : ffea,fopa,o$p', '2025-01-07 15:04:22', 'coucou');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('8', 'sylvaintr a réagi à un de vos messages dans le fil : aaaa', '2025-01-07 15:04:58', 'Lifzerr');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('9', 'sylvaintr a réagi à un de vos messages dans le fil : aaaa', '2025-01-07 15:05:02', 'Lifzerr');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('10', 'sylvaintr a réagi à un de vos messages dans le fil : aaaa', '2025-01-07 15:05:04', 'Lifzerr');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('11', 'sylvaintr a réagi à un de vos messages dans le fil : aaaa', '2025-01-07 15:05:05', 'Lifzerr');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('12', 'sylvaintr a réagi à un de vos messages dans le fil : aaaa', '2025-01-07 15:05:07', 'coucou');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('13', 'sylvaintr a réagi à un de vos messages dans le fil : aaaa', '2025-01-07 15:05:09', 'coucou');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('14', 'sylvaintr a réagi à un de vos messages dans le fil : aaaa', '2025-01-07 15:05:10', 'coucou');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('15', 'sylvaintr a réagi à un de vos messages dans le fil : aaaa', '2025-01-07 15:05:11', 'coucou');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('16', 'sylvaintr a réagi à un de vos messages dans le fil : aaaa', '2025-01-07 15:05:12', 'coucou');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('17', 'FranzouGame a réagi à un de vos messages dans le fil : Les thrillers qui nous tiennen', '2025-01-13 10:06:03', 'DameHalcyon');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('18', 'FranzouGame a réagi à un de vos messages dans le fil : Les thrillers qui nous tiennen', '2025-01-13 10:06:05', 'DameHalcyon');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('19', 'FranzouGame a réagi à un de vos messages dans le fil : Les thrillers qui nous tiennen', '2025-01-13 10:06:06', 'DameHalcyon');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('20', 'FranzouGame a réagi à un de vos messages dans le fil : Les thrillers qui nous tiennen', '2025-01-13 10:06:07', 'FranzouGame');
INSERT INTO `vhs_notification` (`id`, `contenu`, `dateC`, `idUtilisateur`) VALUES ('21', 'FranzouGame a réagi à un de vos messages dans le fil : Les thrillers qui nous tiennen', '2025-01-13 10:06:08', 'FranzouGame');

-- Données de la table `vhs_parlerdetheme` --
INSERT INTO `vhs_parlerdetheme` (`idFil`, `idTheme`) VALUES ('1', '19');
INSERT INTO `vhs_parlerdetheme` (`idFil`, `idTheme`) VALUES ('2', '21');
INSERT INTO `vhs_parlerdetheme` (`idFil`, `idTheme`) VALUES ('3', '27');

-- Données de la table `vhs_partager` --
INSERT INTO `vhs_partager` (`idWatchlist`, `idUtilisateurC`, `idUtilisateurP`) VALUES ('1', 'coucou', 'DameHalcyon');
INSERT INTO `vhs_partager` (`idWatchlist`, `idUtilisateurC`, `idUtilisateurP`) VALUES ('1', 'coucou', 'FranzouGame');
INSERT INTO `vhs_partager` (`idWatchlist`, `idUtilisateurC`, `idUtilisateurP`) VALUES ('1', 'coucou', 'Kong');
INSERT INTO `vhs_partager` (`idWatchlist`, `idUtilisateurC`, `idUtilisateurP`) VALUES ('1', 'coucou', 'Lifzerr');

-- Données de la table `vhs_portersurcollection` --
INSERT INTO `vhs_portersurcollection` (`idQuizz`, `idCollectionTmdb`) VALUES ('1', '1');

-- Données de la table `vhs_portersurcontenu` --
INSERT INTO `vhs_portersurcontenu` (`idQuizz`, `idContenuTmdb`) VALUES ('1', '120');
INSERT INTO `vhs_portersurcontenu` (`idQuizz`, `idContenuTmdb`) VALUES ('1', '122');
INSERT INTO `vhs_portersurcontenu` (`idQuizz`, `idContenuTmdb`) VALUES ('1', '410');
INSERT INTO `vhs_portersurcontenu` (`idQuizz`, `idContenuTmdb`) VALUES ('1', '457');
INSERT INTO `vhs_portersurcontenu` (`idQuizz`, `idContenuTmdb`) VALUES ('1', '549');
INSERT INTO `vhs_portersurcontenu` (`idQuizz`, `idContenuTmdb`) VALUES ('1', '550');
INSERT INTO `vhs_portersurcontenu` (`idQuizz`, `idContenuTmdb`) VALUES ('1', '554');
INSERT INTO `vhs_portersurcontenu` (`idQuizz`, `idContenuTmdb`) VALUES ('1', '335984');
INSERT INTO `vhs_portersurcontenu` (`idQuizz`, `idContenuTmdb`) VALUES ('1', '959604');

-- Données de la table `vhs_portersurtheme` --
INSERT INTO `vhs_portersurtheme` (`idQuizz`, `idTheme`) VALUES ('1', '19');
INSERT INTO `vhs_portersurtheme` (`idQuizz`, `idTheme`) VALUES ('3', '19');
INSERT INTO `vhs_portersurtheme` (`idQuizz`, `idTheme`) VALUES ('2', '20');
INSERT INTO `vhs_portersurtheme` (`idQuizz`, `idTheme`) VALUES ('1', '21');
INSERT INTO `vhs_portersurtheme` (`idQuizz`, `idTheme`) VALUES ('3', '21');
INSERT INTO `vhs_portersurtheme` (`idQuizz`, `idTheme`) VALUES ('2', '22');

-- Données de la table `vhs_question` --
INSERT INTO `vhs_question` (`idQuestion`, `valeur`, `rang`, `urlImage`, `idQuizz`) VALUES ('1', 'Quelle est la couleur du bouclier de Captain America ?', '1', NULL, '1');

-- Données de la table `vhs_quizz` --
INSERT INTO `vhs_quizz` (`idQuizz`, `titre`, `description`, `difficulte`, `dateC`, `idUtilisateur`) VALUES ('1', 'Test de connaissance Harry Potter', 'Ce quizz teste tes connaissances sur la saga', '2', '2024-12-04', 'coucou');
INSERT INTO `vhs_quizz` (`idQuizz`, `titre`, `description`, `difficulte`, `dateC`, `idUtilisateur`) VALUES ('2', 'Test de connaissance de la saga Arcane', 'Connais-tu bien la saga Arcane ? Découvre-le avec ce quizz !', '2', '2024-11-06', 'FranzouGame');
INSERT INTO `vhs_quizz` (`idQuizz`, `titre`, `description`, `difficulte`, `dateC`, `idUtilisateur`) VALUES ('3', 'Sorciers des films', 'Quizz sur les sorciers de plusieurs films', '3', '2024-10-09', 'Lifzerr');

-- Données de la table `vhs_reagir` --
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '1', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '2', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '3', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '4', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '5', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '6', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '7', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '8', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '9', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '10', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '11', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '12', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '13', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '14', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '15', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '16', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '17', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '18', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '19', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('coucou', '20', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '1', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '2', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '3', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '4', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '5', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '6', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '7', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '8', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '9', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '10', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '11', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '12', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '13', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '14', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '15', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '16', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '17', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '18', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '19', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('DameHalcyon', '20', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '1', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '2', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '3', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '4', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '5', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '6', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '7', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '8', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '9', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '10', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '11', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '12', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '13', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '14', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '15', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '16', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '17', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '18', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '19', '0');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '20', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('FranzouGame', '21', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '1', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '2', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '3', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '4', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '5', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '6', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '7', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '8', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '9', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '10', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '11', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '12', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '13', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '14', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '15', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '16', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '17', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '18', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '19', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Kong', '20', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '1', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '2', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '3', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '4', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '5', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '6', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '7', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '8', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '9', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '10', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '11', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '12', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '13', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '14', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '15', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '16', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '17', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '18', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '19', '1');
INSERT INTO `vhs_reagir` (`idUtilisateur`, `idMessage`, `reaction`) VALUES ('Lifzerr', '20', '1');

-- Données de la table `vhs_reponse` --
INSERT INTO `vhs_reponse` (`idReponse`, `valeur`, `rang`, `estVraie`, `idQuestion`) VALUES ('1', 'Bleu, blanc, rouge', '1', '1', '1');
INSERT INTO `vhs_reponse` (`idReponse`, `valeur`, `rang`, `estVraie`, `idQuestion`) VALUES ('2', 'Vert', '2', '0', '1');

-- Données de la table `vhs_salle` --
INSERT INTO `vhs_salle` (`idSalle`, `nom`, `estPublique`, `code`, `rangCourant`, `genre`, `nbpersonne`) VALUES ('1', 'Salle Publique', '1', '1234', '1', 'Comédie', '10');
INSERT INTO `vhs_salle` (`idSalle`, `nom`, `estPublique`, `code`, `rangCourant`, `genre`, `nbpersonne`) VALUES ('2', 'Salle Privée', '0', '5678', '1', 'Drame', '5');

-- Données de la table `vhs_se_trouver` --
INSERT INTO `vhs_se_trouver` (`idUtilisateur`, `idSalle`, `muet`, `banni`, `exclu`, `role`) VALUES ('coucou', '1', '0', '0', '0', 'Hote');
INSERT INTO `vhs_se_trouver` (`idUtilisateur`, `idSalle`, `muet`, `banni`, `exclu`, `role`) VALUES ('DameHalcyon', '1', '0', '0', '0', 'Administrateur');
INSERT INTO `vhs_se_trouver` (`idUtilisateur`, `idSalle`, `muet`, `banni`, `exclu`, `role`) VALUES ('FranzouGame', '1', '0', '0', '0', 'Hote');
INSERT INTO `vhs_se_trouver` (`idUtilisateur`, `idSalle`, `muet`, `banni`, `exclu`, `role`) VALUES ('Kong', '1', '0', '0', '0', 'Hote');
INSERT INTO `vhs_se_trouver` (`idUtilisateur`, `idSalle`, `muet`, `banni`, `exclu`, `role`) VALUES ('Lifzerr', '1', '0', '0', '0', 'Hote');

-- Données de la table `vhs_signalement` --
INSERT INTO `vhs_signalement` (`idSignalement`, `raison`, `idUtilisateur`, `idMessage`) VALUES ('6', 'Spam', 'coucou', '38');
INSERT INTO `vhs_signalement` (`idSignalement`, `raison`, `idUtilisateur`, `idMessage`) VALUES ('7', 'Contenu inapproprié', 'DameHalcyon', '38');
INSERT INTO `vhs_signalement` (`idSignalement`, `raison`, `idUtilisateur`, `idMessage`) VALUES ('8', 'Contenu inapproprié', 'coucou', '14');

-- Données de la table `vhs_theme` --
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('19', 'Science-Fiction');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('20', 'Drame');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('21', 'Action');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('22', 'Animation');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('23', 'Aventure');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('24', 'Fantastique');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('25', 'Thriller');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('26', 'Mystère');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('27', 'Histoire');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('28', 'Documentaire');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('29', 'Comédie');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('30', 'Crime');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('31', 'Romance');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('32', 'Musique');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('33', 'Guerre');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('34', 'Familial');
INSERT INTO `vhs_theme` (`idTheme`, `nom`) VALUES ('35', 'Horreur');

-- Données de la table `vhs_utilisateur` --
INSERT INTO `vhs_utilisateur` (`idUtilisateur`, `pseudo`, `vraiNom`, `mail`, `mdp`, `role`, `urlImageProfil`, `urlImageBanniere`, `dateI`, `estValider`) VALUES ('coucou', 'sylvaintr', 'sylvain', 'strouilh@iutbayonne.univ-pau.fr', '$2y$10$atJ0nQniHbLC95/mG3T4xuOtX8PLRNtyyWGwl4UzZC82lDnb3Q3gS', 'Moderateur', 'images/imageProfil_coucou.png', 'images/imageBanniere_coucou.png', '2024-12-10 18:07:53', '1');
INSERT INTO `vhs_utilisateur` (`idUtilisateur`, `pseudo`, `vraiNom`, `mail`, `mdp`, `role`, `urlImageProfil`, `urlImageBanniere`, `dateI`, `estValider`) VALUES ('DameHalcyon', 'DameHalcyon', 'Marylou Lohier', 'mary.lohier@gmail.com', '$2y$10$l8AtpnPIQF7JXMuTeRHClOj/4fSjq8jg/SWrABpC3dlK3Qdg6RSVi', 'Utilisateur', 'images/imageProfil_DameHalcyon.jpg', 'images/Baniere_de_base.png', '2025-01-11 09:20:47', '1');
INSERT INTO `vhs_utilisateur` (`idUtilisateur`, `pseudo`, `vraiNom`, `mail`, `mdp`, `role`, `urlImageProfil`, `urlImageBanniere`, `dateI`, `estValider`) VALUES ('FranzouGame', 'FranzouGame', 'LeFrançois', 'franz04@outlook.fr', '$2y$10$XqjiERAemQZCfRObjc5nZecvlWt5Hk/P2r/VMmJpGdveo2MkdhkD6', 'Utilisateur', 'images/imageProfil_FranzouGame.png', 'images/imageBanniere_FranzouGame.png', '2024-12-18 18:07:53', '1');
INSERT INTO `vhs_utilisateur` (`idUtilisateur`, `pseudo`, `vraiNom`, `mail`, `mdp`, `role`, `urlImageProfil`, `urlImageBanniere`, `dateI`, `estValider`) VALUES ('Kong', 'Kong', 'Debaisieux', 'nathanael.debaisieux@gmail.com', '$2y$10$KKyD6mExuJ9vcz0VZ2mwHOJ13Cpmt9qHVzkSiKEth/I4BTt/OGDdW', 'Utilisateur', 'images/Profil_de_base.svg', 'images/Baniere_de_base.png', '2024-12-10 18:07:53', '1');
INSERT INTO `vhs_utilisateur` (`idUtilisateur`, `pseudo`, `vraiNom`, `mail`, `mdp`, `role`, `urlImageProfil`, `urlImageBanniere`, `dateI`, `estValider`) VALUES ('Lifzerr', 'Lifzerr', 'Maxime BOURCIEZ', 'maxime.bourciez@gmail.com', '$2y$10$fakNfhsjSK/KdQVZtVEAGevwwxPg.dHPk2/OuUsIzSsxY2j8jpsIO', 'Utilisateur', 'images/imageProfil_Lifzerr.png', 'images/imageBaniere_Lifzerr.png', '2024-12-10 18:07:53', '1');

-- Données de la table `vhs_watchlist` --
INSERT INTO `vhs_watchlist` (`idWatchlist`, `nom`, `dateC`, `description`, `estPublique`, `idUtilisateur`) VALUES ('1', 'Ma Watchlist', '2025-01-01', 'film à voir', '1', 'coucou');
INSERT INTO `vhs_watchlist` (`idWatchlist`, `nom`, `dateC`, `description`, `estPublique`, `idUtilisateur`) VALUES ('2', 'Salut', '2025-01-13', 'Salut', '0', 'FranzouGame');
INSERT INTO `vhs_watchlist` (`idWatchlist`, `nom`, `dateC`, `description`, `estPublique`, `idUtilisateur`) VALUES ('3', 'Salut', '2025-01-13', 'Salut', '0', 'FranzouGame');
INSERT INTO `vhs_watchlist` (`idWatchlist`, `nom`, `dateC`, `description`, `estPublique`, `idUtilisateur`) VALUES ('4', 'Salut', '2025-01-13', 'Salut', '0', 'FranzouGame');
INSERT INTO `vhs_watchlist` (`idWatchlist`, `nom`, `dateC`, `description`, `estPublique`, `idUtilisateur`) VALUES ('5', 'Salut', '2025-01-13', 'Salut', '0', 'FranzouGame');
INSERT INTO `vhs_watchlist` (`idWatchlist`, `nom`, `dateC`, `description`, `estPublique`, `idUtilisateur`) VALUES ('6', 'Salut', '2025-01-13', 'Salut', '0', 'FranzouGame');

