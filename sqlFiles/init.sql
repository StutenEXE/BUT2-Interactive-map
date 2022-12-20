-- **********************************************************************
-- Script MySQL de crétion de la base LEX-A
-- **********************************************************************   

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS FONTAINES_BUES CASCADE;
DROP TABLE IF EXISTS UTILISATEUR CASCADE;
DROP TABLE IF EXISTS FONTAINE CASCADE;
DROP TABLE IF EXISTS GROUPE CASCADE;

-- Creation des tables

CREATE TABLE IF NOT EXISTS GROUPE(
   ID INT AUTO_INCREMENT,
   Code VARCHAR(5)  NOT NULL,
   PRIMARY KEY(ID),
   UNIQUE(Code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS FONTAINE(
   ID INT AUTO_INCREMENT,
   Disponible BOOLEAN NOT NULL,
   Rue VARCHAR(50)  NOT NULL,
   Coords GEOMETRY NOT NULL,
   ID_Groupe INT,
   PRIMARY KEY(ID),
   FOREIGN KEY(ID_Groupe) REFERENCES GROUPE(ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS UTILISATEUR(
   ID INT AUTO_INCREMENT,
   Pseudo VARCHAR(50)  NOT NULL,
   MDP VARCHAR(50)  NOT NULL,
   ID_Groupe INT,
   PRIMARY KEY(ID),
   UNIQUE(Pseudo),
   FOREIGN KEY(ID_Groupe) REFERENCES GROUPE(ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS FONTAINES_BUES(
   ID_Utilisateur INT,
   ID_Fontaine INT,
   PRIMARY KEY(ID_Utilisateur, ID_Fontaine),
   FOREIGN KEY(ID_Utilisateur) REFERENCES UTILISATEUR(ID),
   FOREIGN KEY(ID_Fontaine) REFERENCES FONTAINE(ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;