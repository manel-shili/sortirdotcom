delete from ville;
delete from lieu;
delete from site;
delete from utilisateur;
delete from sortie;
delete from photo_sortie;
delete from inscription;

delete from sqlite_sequence;

/*Jeux de donnéees ville */

INSERT INTO ville (nom, code_postal)
VALUES
('Nantes', '44000'),
('Saint-Herblain','44800'),
('Rennes','35000'),
('Chartres-de-Bretagne','35131'),
('Quimper','29000'),
('Niort', '79000');

/* Jeux de données lieu */

INSERT INTO lieu (ville_id, nom, rue, latitude, longitude)
VALUES
(2, 'ENI Campus Nantes FARADAY', '3 rue Mickael FARADAY', 47.225433349609375, -1.6185470819473267),
(3, 'Eni Compus Nantes FRANKLIN','r Benjamin Franklin,',48.10929870605469,-1.7080506086349487 ),
(5, 'ENI Campus Quimper','2, rue Georges Perros',47.97711944580078, -4.083467960357666),
(6, 'ENI Campus Niort', '19 avenue Léo Lagrange', 46.31629943847656, -0.4703825116157532 ),
(1, 'Lieu Unique','2 Rue de la Biscuiterie', 47.215084075927734, -1.5454285144805908),
(3, 'Roazhon Park','111 Rue de Lorient',48.107745,-1.714349 ),
(1, 'Gare de Nantes','25 boulevard de staligrad',47.21796,-1.542652 ),
(4, 'ENI Campus Rennes', 'ZAC de La Conterie, 8 Rue Léo Lagrange',48.039398193359375, -1.6918691396713257 );


/* Jeux de données site */

INSERT INTO site (localisation_id, nom)
VALUES
(2, ' Nantes Faraday'),
/*(3, ' Nantes Franklin'),*/
/*(4 ,'Rennes'),*/
(5 ,'Quimper'),
(6 , 'Niort');

/* Jeux de données utilisateur*/

/*  password : azerty0123456789 */
INSERT INTO utilisateur (username,site_id,roles, password, courriel, is_verified, nom, prenom, telephone, is_actif, nom_photo, is_cgu_accepte)
VALUES
( 'admin',1,'["ROLE_ADMIN"]','$2y$13$dedlIDS4Oa9T0NHhoTeIg.8quSiR3IkZQ3jx.T9hmfV2jctg1DoBq', 'admin@campus-eni.fr', true, 'AUVERGNAT', 'Aurélie','0123456789', true,'1.jpg',true),
( 'Boubou',1,'[]','$2y$13$dedlIDS4Oa9T0NHhoTeIg.8quSiR3IkZQ3jx.T9hmfV2jctg1DoBq', 'bernard.balvert@campus-eni.fr', true, 'BALVERT', 'Bernard','0123456789',true,'2.jpg',true),
( 'la Mouette ',1,'[]','$2y$13$dedlIDS4Oa9T0NHhoTeIg.8quSiR3IkZQ3jx.T9hmfV2jctg1DoBq', 'caro.cownell@campus-eni.fr', true, 'COWNELL', 'Caroline','0123456789',true,'3.jpg',true),
( 'JarretdePorcSelPoivre',1,'[]','$2y$13$dedlIDS4Oa9T0NHhoTeIg.8quSiR3IkZQ3jx.T9hmfV2jctg1DoBq', 'david.darwin@campus-eni.fr', true, 'DARWIN', 'David','0123456789',true,'4.jpg',true),
( 'pizzaiolo pizzaiolo',1,'[]','$2y$13$dedlIDS4Oa9T0NHhoTeIg.8quSiR3IkZQ3jx.T9hmfV2jctg1DoBq', 'eloise.epsilon@campus-eni.fr', true, 'EPSILON', 'Eloise','0123456789',true,'5.png',true);

/* Jeux de données de sortie*/


INSERT INTO sortie (organisateur_id, adresse_id, nom, nb_inscription_max, description, date_ouverture_inscription, date_fermeture_inscription, date_debut_sortie, is_annulee, date_enregistrement, date_fin_sortie)
VALUES
(2,5,'KMRU',10,'Né à Nairobi et actuellement basé à Berlin pour des études universitaires, KMRU est un artiste sonore et un producteur qui nourrit sa musique de field recording*, d’improvisation, de bruit, de machine learning*, d’art radiophonique et de drones. À la frontière entre l’ambient et les musiques africaines, KMRU explore les sonorités et réveille d’intenses émotions pour celui qui l’écoute','2022-10-13 12:00','2022-11-12 12:00', '2022-12-15 19:00', false, '2022-10-13 14:00','2022-12-15 23:00'),
(3,6,'Football, Stade Rennais - FC Toulouse', 10, '15 ème journée de Ligue 1 ','2022-10-13 12:00','2022-10-14 12:00', '2022-12-15 20:00', false, '2022-10-10 12:00','2022-12-15 23:59'),
(4,7,'harmonie', 2, 'Venez vous ressourcer dans un lieu zen au contact de la nature','2022-10-19 12:00','2022-12-31 12:00', '2023-01-01 20:00', false, '2022-10-12 12:00','2023-03-02 21:00')
;
/*Jeux de données photoSortie*/


INSERT INTO photo_sortie (sortie_id, nom)
VALUES
(1,'kmru.jpg'),
(2,'rennestoulouse.jpg');

/* Jeux de données inscription */
INSERT INTO inscription (utilisateur_id, sortie_id, date_inscription, is_participant)
VALUES
(3,2,'2022-10-20 14:00',true),
(4,2,'2022-10-20 14:00',true),
(5,1,'2022-10-20 14:00',true);




