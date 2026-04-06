CREATE DATABASE IF NOT EXISTS estimia_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE estimia_db;

CREATE TABLE IF NOT EXISTS estimations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adresse VARCHAR(255) NOT NULL,
    adresse_complete VARCHAR(500) NULL,
    ville VARCHAR(100),
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    code_postal VARCHAR(10) NULL,
    departement VARCHAR(100) NULL,
    place_id VARCHAR(255) NULL,
    type_bien ENUM('appartement','maison','studio','terrain') NOT NULL,
    surface INT NOT NULL,
    budget_estimation ENUM('moins_150k','150k_300k','300k_500k','plus_500k') NOT NULL,
    prix_estime INT,
    prix_bas INT,
    prix_haut INT,
    prix_m2 INT,
    nom VARCHAR(100) NULL,
    email VARCHAR(255) NULL,
    telephone VARCHAR(20) NULL,
    rdv_pris TINYINT(1) DEFAULT 0,
    source ENUM('formulaire_simple','formulaire_detaille','api') DEFAULT 'formulaire_simple',
    lead_type ENUM('estimation_gratuite','estimation_detaillee','rdv') DEFAULT 'estimation_gratuite',
    lead_score INT DEFAULT 0,
    lead_statut ENUM('nouveau','contacte','qualifie','en_negociation','converti','perdu') DEFAULT 'nouveau',
    notes_agent TEXT NULL,
    agent_assigne INT NULL,
    dernier_contact DATETIME NULL,
    relance_prevue DATETIME NULL,
    nombre_visites INT DEFAULT 1,
    utm_source VARCHAR(100) NULL,
    utm_medium VARCHAR(100) NULL,
    utm_campaign VARCHAR(100) NULL,
    utm_content VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS villes_prix (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville VARCHAR(100) UNIQUE NOT NULL,
    code_postal VARCHAR(10),
    departement VARCHAR(100),
    region VARCHAR(100),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    prix_m2_appartement INT,
    prix_m2_maison INT,
    prix_m2_studio INT,
    prix_m2_terrain INT,
    tendance_annuelle DECIMAL(4,2),
    nb_transactions INT,
    population INT DEFAULT 0,
    distance_centre DECIMAL(6,1) DEFAULT 0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS agents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    email VARCHAR(255) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    secteur_geographique TEXT NULL,
    actif TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS rdv (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estimation_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    date_souhaitee DATE NULL,
    creneau ENUM('matin','apres_midi','soir') NULL,
    message TEXT NULL,
    statut ENUM('nouveau','contacte','confirme','annule') DEFAULT 'nouveau',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rdv_estimation FOREIGN KEY (estimation_id) REFERENCES estimations(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS leads_detailles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estimation_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    email VARCHAR(255) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    est_proprietaire TINYINT(1) DEFAULT 1,
    nb_pieces INT,
    nb_chambres INT,
    etage INT NULL,
    nb_etages_immeuble INT NULL,
    annee_construction INT NULL,
    etat_general ENUM('neuf','tres_bon','bon','a_rafraichir','a_renover') NULL,
    type_chauffage ENUM('individuel_gaz','individuel_electrique','collectif','pompe_chaleur','autre') NULL,
    dpe ENUM('A','B','C','D','E','F','G','non_renseigne') DEFAULT 'non_renseigne',
    balcon TINYINT(1) DEFAULT 0,
    terrasse TINYINT(1) DEFAULT 0,
    jardin TINYINT(1) DEFAULT 0,
    surface_terrain INT NULL,
    parking TINYINT(1) DEFAULT 0,
    garage TINYINT(1) DEFAULT 0,
    piscine TINYINT(1) DEFAULT 0,
    cave TINYINT(1) DEFAULT 0,
    projet ENUM('vendre','estimer_seulement','succession','divorce','investissement','autre') DEFAULT 'estimer_seulement',
    delai_vente ENUM('urgent','3_mois','6_mois','1_an','pas_presse') DEFAULT 'pas_presse',
    deja_en_vente TINYINT(1) DEFAULT 0,
    agence_actuelle VARCHAR(200) NULL,
    prix_estime_detaille INT NULL,
    prix_bas_detaille INT NULL,
    prix_haut_detaille INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_leads_detailles_estimation FOREIGN KEY (estimation_id) REFERENCES estimations(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS lead_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estimation_id INT NOT NULL,
    type_interaction ENUM('creation','vue','appel_sortant','appel_entrant','email_envoye','email_recu','note','rdv_fixe','rdv_effectue','relance','changement_statut') NOT NULL,
    description TEXT,
    agent_id INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lead_interactions_estimation FOREIGN KEY (estimation_id) REFERENCES estimations(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(50) NOT NULL,
    details TEXT,
    admin_email VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS objectifs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mois DATE NOT NULL,
    objectif_leads INT DEFAULT 0,
    objectif_rdv INT DEFAULT 0,
    objectif_mandats INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO villes_prix (ville, code_postal, departement, region, lat, lng, prix_m2_appartement, prix_m2_maison, prix_m2_studio, prix_m2_terrain, tendance_annuelle, nb_transactions, population, distance_centre) VALUES
('Bordeaux', '33000', 'Gironde', 'Nouvelle-Aquitaine', 44.83780000, -0.57920000, 4750, 4300, 5250, 1568, 2.10, 8450, 261804, 0.0),
('Paris', '75000', 'Paris', 'Île-de-France', 48.85660000, 2.35220000, 9850, 10200, 11200, 3250, 1.20, 18950, 2161000, 499.6),
('Lyon', '69000', 'Rhône', 'Auvergne-Rhône-Alpes', 45.76400000, 4.83570000, 5600, 5200, 6100, 1848, 1.80, 11200, 516092, 435.2),
('Nantes', '44000', 'Loire-Atlantique', 'Pays de la Loire', 47.21840000, -1.55360000, 4100, 3850, 4500, 1353, 2.40, 7650, 325070, 275.2),
('Toulouse', '31000', 'Haute-Garonne', 'Occitanie', 43.60470000, 1.44420000, 3950, 3700, 4300, 1304, 2.70, 9800, 511684, 211.6),
('Marseille', '13000', 'Bouches-du-Rhône', 'Provence-Alpes-Côte d''Azur', 43.29650000, 5.36980000, 3600, 3350, 3900, 1188, 1.60, 12300, 873076, 506.0),
('Lille', '59000', 'Nord', 'Hauts-de-France', 50.62920000, 3.05730000, 3450, 3200, 3750, 1139, 1.90, 7100, 236710, 699.2),
('Nice', '06000', 'Alpes-Maritimes', 'Provence-Alpes-Côte d''Azur', 43.71020000, 7.26200000, 5900, 5600, 6500, 1947, 1.50, 6900, 348085, 637.4),
('Strasbourg', '67000', 'Bas-Rhin', 'Grand Est', 48.57340000, 7.75210000, 3850, 3550, 4200, 1271, 2.00, 5400, 290576, 758.1),
('Montpellier', '34000', 'Hérault', 'Occitanie', 43.61080000, 3.87670000, 4200, 3950, 4600, 1386, 2.50, 8350, 302454, 381.0)
ON DUPLICATE KEY UPDATE
    code_postal = VALUES(code_postal),
    departement = VALUES(departement),
    region = VALUES(region),
    lat = VALUES(lat),
    lng = VALUES(lng),
    prix_m2_appartement = VALUES(prix_m2_appartement),
    prix_m2_maison = VALUES(prix_m2_maison),
    prix_m2_studio = VALUES(prix_m2_studio),
    prix_m2_terrain = VALUES(prix_m2_terrain),
    tendance_annuelle = VALUES(tendance_annuelle),
    nb_transactions = VALUES(nb_transactions),
    population = VALUES(population),
    distance_centre = VALUES(distance_centre);

INSERT INTO admin_users (email, password_hash)
VALUES ('admin@estimia.fr', '$2y$12$nE7fm.zqKiDebocPd78aGeYf7GwlOaPxmMWWEoKi8M/Qq21/4TUBu')
ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash);
-- Mot de passe par défaut: Admin123! (à changer immédiatement en production)

INSERT INTO agents (nom, prenom, email, telephone, secteur_geographique, actif)
VALUES
('Dupont', 'Marie', 'marie.dupont@estimia.fr', '0612345678', '{"zone":"Bordeaux/Gironde","villes":["Bordeaux","Mérignac","Pessac"]}', 1),
('Martin', 'Pierre', 'pierre.martin@estimia.fr', '0678563412', '{"zone":"Paris/IDF","villes":["Paris","Boulogne","Nanterre"]}', 1)
ON DUPLICATE KEY UPDATE
    telephone = VALUES(telephone),
    secteur_geographique = VALUES(secteur_geographique),
    actif = VALUES(actif);

WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 50
)
INSERT INTO estimations (
    adresse, adresse_complete, ville, latitude, longitude, code_postal, departement,
    place_id, type_bien, surface, budget_estimation, prix_estime, prix_bas, prix_haut,
    prix_m2, nom, email, telephone, rdv_pris, source, lead_type, lead_score, lead_statut,
    agent_assigne, dernier_contact, relance_prevue, nombre_visites, utm_source, utm_medium,
    utm_campaign, utm_content, created_at, ip_address, user_agent
)
SELECT
    CONCAT((10 + n), ' rue Exemple'),
    CONCAT((10 + n), ' rue Exemple, ',
        CASE n % 10
            WHEN 0 THEN 'Bordeaux'
            WHEN 1 THEN 'Paris'
            WHEN 2 THEN 'Lyon'
            WHEN 3 THEN 'Nantes'
            WHEN 4 THEN 'Toulouse'
            WHEN 5 THEN 'Marseille'
            WHEN 6 THEN 'Lille'
            WHEN 7 THEN 'Nice'
            WHEN 8 THEN 'Strasbourg'
            ELSE 'Montpellier'
        END
    ),
    CASE n % 10
        WHEN 0 THEN 'Bordeaux'
        WHEN 1 THEN 'Paris'
        WHEN 2 THEN 'Lyon'
        WHEN 3 THEN 'Nantes'
        WHEN 4 THEN 'Toulouse'
        WHEN 5 THEN 'Marseille'
        WHEN 6 THEN 'Lille'
        WHEN 7 THEN 'Nice'
        WHEN 8 THEN 'Strasbourg'
        ELSE 'Montpellier'
    END,
    CASE n % 10
        WHEN 0 THEN 44.837789
        WHEN 1 THEN 48.856613
        WHEN 2 THEN 45.764042
        WHEN 3 THEN 47.218371
        WHEN 4 THEN 43.604652
        WHEN 5 THEN 43.296482
        WHEN 6 THEN 50.629250
        WHEN 7 THEN 43.710173
        WHEN 8 THEN 48.573405
        ELSE 43.610769
    END,
    CASE n % 10
        WHEN 0 THEN -0.579180
        WHEN 1 THEN 2.352222
        WHEN 2 THEN 4.835659
        WHEN 3 THEN -1.553621
        WHEN 4 THEN 1.444209
        WHEN 5 THEN 5.369780
        WHEN 6 THEN 3.057256
        WHEN 7 THEN 7.261953
        WHEN 8 THEN 7.752111
        ELSE 3.876716
    END,
    CASE n % 10
        WHEN 0 THEN '33000'
        WHEN 1 THEN '75000'
        WHEN 2 THEN '69000'
        WHEN 3 THEN '44000'
        WHEN 4 THEN '31000'
        WHEN 5 THEN '13000'
        WHEN 6 THEN '59000'
        WHEN 7 THEN '06000'
        WHEN 8 THEN '67000'
        ELSE '34000'
    END,
    CASE n % 10
        WHEN 0 THEN 'Gironde'
        WHEN 1 THEN 'Paris'
        WHEN 2 THEN 'Rhône'
        WHEN 3 THEN 'Loire-Atlantique'
        WHEN 4 THEN 'Haute-Garonne'
        WHEN 5 THEN 'Bouches-du-Rhône'
        WHEN 6 THEN 'Nord'
        WHEN 7 THEN 'Alpes-Maritimes'
        WHEN 8 THEN 'Bas-Rhin'
        ELSE 'Hérault'
    END,
    CONCAT('place_', n),
    CASE n % 3
        WHEN 0 THEN 'appartement'
        WHEN 1 THEN 'maison'
        ELSE 'studio'
    END,
    20 + (n * 5),
    CASE
        WHEN n % 4 = 0 THEN 'moins_150k'
        WHEN n % 4 = 1 THEN '150k_300k'
        WHEN n % 4 = 2 THEN '300k_500k'
        ELSE 'plus_500k'
    END,
    120000 + (n * 8500),
    ROUND((120000 + (n * 8500)) * 0.92),
    ROUND((120000 + (n * 8500)) * 1.08),
    2800 + (n * 40),
    CASE WHEN n % 2 = 0 THEN CONCAT('Client', n) ELSE NULL END,
    CASE WHEN n % 2 = 0 THEN CONCAT('client', n, '@email.com') ELSE NULL END,
    CASE WHEN n % 3 = 0 THEN CONCAT('06', LPAD(n * 1234, 8, '0')) ELSE NULL END,
    CASE WHEN n % 3 = 0 THEN 1 ELSE 0 END,
    CASE
        WHEN n % 5 = 0 THEN 'api'
        WHEN n % 5 = 1 THEN 'formulaire_detaille'
        ELSE 'formulaire_simple'
    END,
    CASE
        WHEN n % 6 = 0 THEN 'rdv'
        WHEN n % 4 = 0 THEN 'estimation_detaillee'
        ELSE 'estimation_gratuite'
    END,
    20 + (n % 8) * 10,
    CASE n % 6
        WHEN 0 THEN 'nouveau'
        WHEN 1 THEN 'contacte'
        WHEN 2 THEN 'qualifie'
        WHEN 3 THEN 'en_negociation'
        WHEN 4 THEN 'converti'
        ELSE 'perdu'
    END,
    CASE WHEN n % 2 = 0 THEN 1 ELSE 2 END,
    DATE_SUB(NOW(), INTERVAL (n % 20) DAY),
    DATE_ADD(NOW(), INTERVAL (n % 15) DAY),
    1 + (n % 7),
    CASE n % 3
        WHEN 0 THEN 'google'
        WHEN 1 THEN 'facebook'
        ELSE 'direct'
    END,
    CASE n % 3
        WHEN 0 THEN 'cpc'
        WHEN 1 THEN 'social'
        ELSE 'organic'
    END,
    CONCAT('campagne_', 1 + (n % 5)),
    CONCAT('annonce_', 1 + (n % 7)),
    DATE_SUB(NOW(), INTERVAL (n * 2) DAY),
    CONCAT('192.168.1.', n),
    'Mozilla/5.0 Seed Data'
FROM seq;

WITH ordered_estimations AS (
    SELECT id, nom, email, telephone,
           ROW_NUMBER() OVER (ORDER BY created_at DESC, id DESC) AS rn
    FROM estimations
)
INSERT INTO rdv (estimation_id, nom, email, telephone, date_souhaitee, creneau, message, statut, created_at)
SELECT
    id,
    COALESCE(nom, CONCAT('Prospect ', rn)),
    COALESCE(email, CONCAT('prospect', rn, '@email.com')),
    COALESCE(telephone, CONCAT('06', LPAD(rn * 4321, 8, '0'))),
    DATE_SUB(CURDATE(), INTERVAL (rn % 60) DAY),
    CASE rn % 3 WHEN 0 THEN 'matin' WHEN 1 THEN 'apres_midi' ELSE 'soir' END,
    'RDV de test généré automatiquement',
    CASE rn % 4 WHEN 0 THEN 'nouveau' WHEN 1 THEN 'contacte' WHEN 2 THEN 'confirme' ELSE 'annule' END,
    DATE_SUB(NOW(), INTERVAL rn DAY)
FROM ordered_estimations
WHERE rn <= 15;

WITH ordered_estimations AS (
    SELECT id,
           ROW_NUMBER() OVER (ORDER BY created_at DESC, id DESC) AS rn
    FROM estimations
)
INSERT INTO leads_detailles (
    estimation_id, nom, prenom, email, telephone, est_proprietaire,
    nb_pieces, nb_chambres, etage, nb_etages_immeuble, annee_construction,
    etat_general, type_chauffage, dpe, balcon, terrasse, jardin, surface_terrain,
    parking, garage, piscine, cave, projet, delai_vente, deja_en_vente,
    agence_actuelle, prix_estime_detaille, prix_bas_detaille, prix_haut_detaille
)
SELECT
    id,
    CONCAT('Nom', rn),
    CONCAT('Prenom', rn),
    CONCAT('detail', rn, '@email.com'),
    CONCAT('07', LPAD(rn * 8765, 8, '0')),
    1,
    2 + (rn % 6),
    1 + (rn % 4),
    rn % 5,
    3 + (rn % 6),
    1960 + (rn * 3),
    CASE rn % 5 WHEN 0 THEN 'neuf' WHEN 1 THEN 'tres_bon' WHEN 2 THEN 'bon' WHEN 3 THEN 'a_rafraichir' ELSE 'a_renover' END,
    CASE rn % 5 WHEN 0 THEN 'individuel_gaz' WHEN 1 THEN 'individuel_electrique' WHEN 2 THEN 'collectif' WHEN 3 THEN 'pompe_chaleur' ELSE 'autre' END,
    CASE rn % 8 WHEN 0 THEN 'A' WHEN 1 THEN 'B' WHEN 2 THEN 'C' WHEN 3 THEN 'D' WHEN 4 THEN 'E' WHEN 5 THEN 'F' WHEN 6 THEN 'G' ELSE 'non_renseigne' END,
    rn % 2,
    (rn + 1) % 2,
    rn % 3 = 0,
    100 + (rn * 20),
    rn % 2,
    rn % 2,
    rn % 4 = 0,
    rn % 3 = 0,
    CASE rn % 6 WHEN 0 THEN 'vendre' WHEN 1 THEN 'estimer_seulement' WHEN 2 THEN 'succession' WHEN 3 THEN 'divorce' WHEN 4 THEN 'investissement' ELSE 'autre' END,
    CASE rn % 5 WHEN 0 THEN 'urgent' WHEN 1 THEN '3_mois' WHEN 2 THEN '6_mois' WHEN 3 THEN '1_an' ELSE 'pas_presse' END,
    rn % 2,
    CASE WHEN rn % 2 = 0 THEN 'Agence Premium' ELSE NULL END,
    220000 + (rn * 12000),
    ROUND((220000 + (rn * 12000)) * 0.93),
    ROUND((220000 + (rn * 12000)) * 1.07)
FROM ordered_estimations
WHERE rn <= 10;

WITH ordered_estimations AS (
    SELECT id,
           ROW_NUMBER() OVER (ORDER BY created_at DESC, id DESC) AS rn
    FROM estimations
), seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM seq WHERE n < 30
)
INSERT INTO lead_interactions (estimation_id, type_interaction, description, agent_id, created_at)
SELECT
    oe.id,
    CASE seq.n % 11
        WHEN 0 THEN 'creation'
        WHEN 1 THEN 'vue'
        WHEN 2 THEN 'appel_sortant'
        WHEN 3 THEN 'appel_entrant'
        WHEN 4 THEN 'email_envoye'
        WHEN 5 THEN 'email_recu'
        WHEN 6 THEN 'note'
        WHEN 7 THEN 'rdv_fixe'
        WHEN 8 THEN 'rdv_effectue'
        WHEN 9 THEN 'relance'
        ELSE 'changement_statut'
    END,
    CONCAT('Interaction CRM #', seq.n, ' sur le lead #', oe.id),
    CASE WHEN seq.n % 2 = 0 THEN 1 ELSE 2 END,
    DATE_SUB(NOW(), INTERVAL seq.n DAY)
FROM seq
JOIN ordered_estimations oe ON oe.rn = ((seq.n - 1) % 15) + 1;

INSERT INTO objectifs (mois, objectif_leads, objectif_rdv, objectif_mandats)
VALUES
(DATE_FORMAT(CURDATE(), '%Y-%m-01'), 120, 45, 12),
(DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01'), 110, 40, 10),
(DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), '%Y-%m-01'), 100, 35, 8);
