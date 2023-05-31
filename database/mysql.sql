
drop database if exists urbaine;
create database urbaine;
use urbaine;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `email_exists` (`my_email` VARCHAR(80)) RETURNS TINYINT(1) READS SQL DATA begin
	DECLARE exists_1, exists_2, exists_3, exists_4  boolean;
    set exists_1 = exists(select * from administrateurs WHERE email = my_email); 
    if exists_1 then return 1 ; end if;
    set exists_2 = exists(select * from directeurs WHERE email = my_email); 
    if exists_2 then return 1 ; end if;
    set exists_3 = exists(select * from employes WHERE email = my_email); 
    if exists_3 then return 1 ; end if;
    set exists_4 = exists(select * from secretaires WHERE email = my_email); 
    if exists_4 then return 1 ; end if;
    
    RETURN 0;
end$$

DELIMITER ;



CREATE TABLE `administrateurs` (
  `id` int(11) NOT NULL,
  `prenom` varchar(60) NOT NULL,
  `nom` varchar(60) NOT NULL,
  `telephone` varchar(10) DEFAULT NULL,
  `date_de_naissance` date DEFAULT NULL,
  `genre` enum('homme','femme') DEFAULT NULL,
  `ville` varchar(20) DEFAULT NULL,
  `adresse` varchar(120) DEFAULT NULL,
  `id_service` int(11) NOT NULL,
  `email` varchar(80) NOT NULL,
  `mote_de_pass` char(32) NOT NULL,
  `date_de_linscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut_du_compte` enum('normal','suspendu') NOT NULL DEFAULT 'normal',
  `image_de_profil` varchar(100) DEFAULT NULL,
  `derniere_vu` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `demandes_directeur` (
  `id` int(11) NOT NULL,
  `expediteur` int(11) NOT NULL,
  `titre` varchar(60) NOT NULL,
  `demande` longtext NOT NULL,
  `reponse` tinyint(1) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `dir_vu` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `demandes_service` (
  `id` int(11) NOT NULL,
  `expediteur` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `titre` varchar(60) NOT NULL,
  `demande` longtext NOT NULL,
  `reponse` tinyint(1) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `directeurs` (
  `id` int(11) NOT NULL,
  `email` varchar(60) NOT NULL,
  `mote_de_pass` char(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `employes` (
  `id` int(11) NOT NULL,
  `prenom` varchar(60) NOT NULL,
  `nom` varchar(60) NOT NULL,
  `telephone` varchar(10) DEFAULT NULL,
  `date_de_naissance` date DEFAULT NULL,
  `genre` enum('homme','femme') DEFAULT NULL,
  `ville` varchar(20) DEFAULT NULL,
  `adresse` varchar(120) DEFAULT NULL,
  `role` varchar(100) NOT NULL,
  `id_service` int(11) NOT NULL,
  `email` varchar(80) NOT NULL,
  `mote_de_pass` char(32) NOT NULL,
  `date_de_linscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut_du_compte` enum('normal','suspendu') NOT NULL DEFAULT 'normal',
  `image_de_profil` varchar(100) DEFAULT NULL,
  `derniere_vu` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `id_expediteur` varchar(2500) NOT NULL,
  `id_destinataire` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `notifications_directeur` (
  `id` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `titre` varchar(60) NOT NULL,
  `notification` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `notifications_directeur_vu` (
  `id` int(11) NOT NULL,
  `id_notification` int(11) NOT NULL,
  `id_administrateur` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `notifications_service` (
  `id` int(11) NOT NULL,
  `expediteur` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `titre` varchar(60) NOT NULL,
  `notification` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `notifications_service_vu` (
  `id` int(11) NOT NULL,
  `id_notification` int(11) NOT NULL,
  `id_employe` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `plaintes_directeur` (
  `id` int(11) NOT NULL,
  `expediteur` int(11) NOT NULL,
  `titre` varchar(60) NOT NULL,
  `plainte` longtext NOT NULL,
  `vu` tinyint(1) NOT NULL DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `dir_vu` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `plaintes_service` (
  `id` int(11) NOT NULL,
  `expediteur` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `titre` varchar(60) NOT NULL,
  `plainte` longtext NOT NULL,
  `vu` tinyint(1) NOT NULL DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `secretaires` (
  `id` int(11) NOT NULL,
  `prenom` varchar(60) DEFAULT NULL,
  `nom` varchar(60) DEFAULT NULL,
  `telephone` varchar(10) DEFAULT NULL,
  `date_de_naissance` date DEFAULT NULL,
  `genre` enum('homme','femme') DEFAULT NULL,
  `ville` varchar(20) DEFAULT NULL,
  `adresse` varchar(120) DEFAULT NULL,
  `email` varchar(80) NOT NULL,
  `mote_de_pass` char(32) NOT NULL,
  `date_de_linscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut_du_compte` enum('normal','suspendu') NOT NULL DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `nom` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



ALTER TABLE `administrateurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_service` (`id_service`);

ALTER TABLE `demandes_directeur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expediteur` (`expediteur`);

ALTER TABLE `demandes_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_service` (`id_service`),
  ADD KEY `expediteur` (`expediteur`);

ALTER TABLE `directeurs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_service` (`id_service`);

ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `notifications_directeur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_service` (`id_service`);

ALTER TABLE `notifications_directeur_vu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_notification` (`id_notification`,`id_administrateur`),
  ADD KEY `id_administrateur` (`id_administrateur`);

ALTER TABLE `notifications_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_service` (`id_service`),
  ADD KEY `expediteur` (`expediteur`);

ALTER TABLE `notifications_service_vu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_notification` (`id_notification`,`id_employe`),
  ADD KEY `id_employe` (`id_employe`);

ALTER TABLE `plaintes_directeur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expediteur` (`expediteur`);

ALTER TABLE `plaintes_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_service` (`id_service`),
  ADD KEY `expediteur` (`expediteur`);

ALTER TABLE `secretaires`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `administrateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `demandes_directeur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `demandes_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `directeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notifications_directeur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notifications_directeur_vu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notifications_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notifications_service_vu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `plaintes_directeur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `plaintes_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `secretaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;




ALTER TABLE `administrateurs`
  ADD CONSTRAINT `administrateurs_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `service` (`id`);

ALTER TABLE `demandes_directeur`
  ADD CONSTRAINT `demandes_directeur_ibfk_1` FOREIGN KEY (`expediteur`) REFERENCES `administrateurs` (`id`);

ALTER TABLE `demandes_service`
  ADD CONSTRAINT `demandes_service_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `service` (`id`),
  ADD CONSTRAINT `demandes_service_ibfk_2` FOREIGN KEY (`expediteur`) REFERENCES `employes` (`id`);

ALTER TABLE `employes`
  ADD CONSTRAINT `employes_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `service` (`id`);

ALTER TABLE `notifications_directeur`
  ADD CONSTRAINT `notifications_directeur_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `service` (`id`);

ALTER TABLE `notifications_directeur_vu`
  ADD CONSTRAINT `notifications_directeur_vu_ibfk_1` FOREIGN KEY (`id_administrateur`) REFERENCES `administrateurs` (`id`),
  ADD CONSTRAINT `notifications_directeur_vu_ibfk_2` FOREIGN KEY (`id_notification`) REFERENCES `notifications_directeur` (`id`);

ALTER TABLE `notifications_service`
  ADD CONSTRAINT `notifications_service_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `service` (`id`),
  ADD CONSTRAINT `notifications_service_ibfk_2` FOREIGN KEY (`expediteur`) REFERENCES `administrateurs` (`id`);

ALTER TABLE `notifications_service_vu`
  ADD CONSTRAINT `notifications_service_vu_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`),
  ADD CONSTRAINT `notifications_service_vu_ibfk_2` FOREIGN KEY (`id_notification`) REFERENCES `notifications_service` (`id`);

ALTER TABLE `plaintes_directeur`
  ADD CONSTRAINT `plaintes_directeur_ibfk_1` FOREIGN KEY (`expediteur`) REFERENCES `administrateurs` (`id`);

ALTER TABLE `plaintes_service`
  ADD CONSTRAINT `plaintes_service_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `service` (`id`),
  ADD CONSTRAINT `plaintes_service_ibfk_2` FOREIGN KEY (`expediteur`) REFERENCES `employes` (`id`);

INSERT INTO `service` 
  (`id`, `nom`)
VALUES
  (1, 'Département de Gestion Urbaine et de la Réglementation'),
  (2, 'Division des affaires Financieres'),
  (3, 'Département des études et de la Topographie'),
  (4, 'Service Informatique');

INSERT INTO `directeurs` 
  (`id`, `email`, `mote_de_pass`) 
VALUES
  (1, 'directeur@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055');

INSERT INTO `secretaires` (`email`, `mote_de_pass`) VALUES ('secretaire@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055');

INSERT INTO `administrateurs` 
  (`prenom`, `nom`,`date_de_naissance`, `genre`,`id_service`, `email`, `mote_de_pass`) 
VALUES 
  ('Rachid', 'Mazzene','1990-01-01','homme',1, 'rachid.mazzene@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055'),
  ('Youssef', 'Makini','1990-01-01','homme',2, 'youssef.makini@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055'),
  ('Najat', 'Atiq','1990-01-01','femme',3, 'najat.atiq@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055'),
  ('Zahra', 'Boushabi','1990-01-01','femme',4, 'zahra.boushabi@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055');

INSERT INTO `employes` 
  (`prenom`, `nom`, `role`, `id_service`, `email`, `mote_de_pass`) 
VALUES
  ('Ossama', 'Rahimi', 'chef', 1, 'ossama.rahimi@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055'),
  ('Ahmed', 'Merwane', 'chef', 1, 'ahmed.merwane@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055'),  
  ('Zakaria', 'Mezouinou', 'service du personnel', 2, 'zakaria.mezouinou@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055'),
  ('Jalal', 'Afoukal', 'service de budget, des marchés', 2, 'jalal.afoukal@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055'),
  ('Elham', 'Essaadi', 'division des etudes', 3, 'elham.essaadi@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055'),
  ('Abderahim', 'Kajjaj', 'division des etudes', 3, 'abderahim.kajjaj@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055'),
  ('Ali', 'Bilali', 'chef', 4, 'ali.bilali@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055'),
  ('Mohamed', 'Tijani', 'chef', 4, 'mohamed.tijani@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055');


-- mote_de_pass = 1234