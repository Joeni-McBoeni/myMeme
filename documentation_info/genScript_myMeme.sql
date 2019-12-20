/* ----------------------------------------------------------------------
myMeme_DB_Jonas.sql

This script completely sets up the Meme database including all tables,
attributes and indexes. It can be used for an automatic deployment.

Author: Jonas Wiesli, Kanti Frauenfeld
Date: 2019-12-13

History:
Version		Date 		Who 	Changes
0.1			2019-12-13 	WIJ 	created

Copyright © 2019 Kanti Frauenfeld, Frauenfeld, Switzerland. All rights reserved.
------------------------------------------------------------------------ */

-- -----------------------------------------------------
-- Schema myMeme_DB_Jonas
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS myMeme_DB_Jonas DEFAULT CHARACTER SET utf8;
USE myMeme_DB_Jonas;

-- -----------------------------------------------------
-- Table TInteractions
-- -----------------------------------------------------
DROP TABLE TInteractions;

CREATE TABLE TInteractions (
  UserId INT NOT NULL,
  PicId INT NOT NULL,
  IntValue SMALLINT NULL,
  PRIMARY KEY (UserId, PicId))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table TUsers
-- -----------------------------------------------------
DROP TABLE TUsers;

CREATE TABLE TUsers (
  UserId INT NOT NULL,
  UserName VARCHAR(50) NULL,
  UserPassword VARCHAR(40) NULL,
  UserType ENUM('host', 'user', 'guest') NULL,
  PRIMARY KEY (UserId))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table TPictures
-- -----------------------------------------------------
DROP TABLE TPictures;

CREATE TABLE TPictures (
  PicId INT NOT NULL,
  PicUrl VARCHAR(45) NULL,
  PicTitle VARCHAR(45) NULL,
  UserId INT NOT NULL,
  PRIMARY KEY (PicId, UserId))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table TTags
-- -----------------------------------------------------
DROP TABLE TTags;

CREATE TABLE TTags (
  TagId INT NOT NULL,
  TagText VARCHAR(30) NULL,
  PRIMARY KEY (TagId))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table TInteractions
-- -----------------------------------------------------
DROP TABLE TInteractions;

CREATE TABLE TInteractions (
  UserId INT NOT NULL,
  PicId INT NOT NULL,
  IntValue SMALLINT NULL,
  PRIMARY KEY (UserId, PicId))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table TAppliedTags
-- -----------------------------------------------------
DROP TABLE TAppliedTags;

CREATE TABLE TAppliedTags (
  PicId INT NOT NULL,
  TagId INT NOT NULL,
  PRIMARY KEY (PicId, TagId))
ENGINE = InnoDB;
