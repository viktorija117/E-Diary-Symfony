SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema bazaZaWebProjekatDnevnik
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `bazaZaWebProjekatDnevnik` ;

-- -----------------------------------------------------
-- Schema bazaZaWebProjekatDnevnik
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `bazaZaWebProjekatDnevnik` DEFAULT CHARACTER SET utf8 ;
USE `bazaZaWebProjekatDnevnik` ;

-- -----------------------------------------------------
-- Table `bazaZaWebProjekatDnevnik`.`direktor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bazaZaWebProjekatDnevnik`.`direktor` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ime` VARCHAR(45) NOT NULL,
  `prezime` VARCHAR(45) NOT NULL,
  `korisnicko_ime` VARCHAR(45) NOT NULL,
  `lozinka` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO `bazaZaWebProjekatDnevnik`.`direktor` (`ime`, `prezime`, `korisnicko_ime`, `lozinka`)
VALUES ('Pera', 'Peric', 'direktorPera', MD5('direktorPera'));

-- -----------------------------------------------------
-- Table `bazaZaWebProjekatDnevnik`.`predmet`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bazaZaWebProjekatDnevnik`.`predmet` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `naziv_predmeta` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO `predmet` (`naziv_predmeta`) VALUES
('Uvod u Web'),
('Web'),
('Baze podataka'),
('Matematika1'),
('Fizicko'),
('Srpski jezik'),
('Operativni sistemi'),
('Diskretne strukture'),
('Matematika2'),
('Matematika3');


-- -----------------------------------------------------
-- Table `bazaZaWebProjekatDnevnik`.`profesor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bazaZaWebProjekatDnevnik`.`profesor` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ime` VARCHAR(45) NOT NULL,
  `prezime` VARCHAR(45) NOT NULL,
  `korisnicko_ime` VARCHAR(45) NOT NULL,
  `lozinka` VARCHAR(45) NOT NULL,
  `predmet_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_profesor_predmet1_idx` (`predmet_id` ASC),
  CONSTRAINT `fk_profesor_predmet1`
    FOREIGN KEY (`predmet_id`)
    REFERENCES `bazaZaWebProjekatDnevnik`.`predmet` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `bazaZaWebProjekatDnevnik`.`student`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bazaZaWebProjekatDnevnik`.`student` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ime` VARCHAR(45) NOT NULL,
  `prezime` VARCHAR(45) NOT NULL,
  `korisnicko_ime` VARCHAR(45) NOT NULL,
  `lozinka` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `bazaZaWebProjekatDnevnik`.`student_slusa_predmete`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bazaZaWebProjekatDnevnik`.`student_slusa_predmete` (
  `student_id` INT(11) NOT NULL,
  `predmet_id` INT(11) NOT NULL,
  `ocena` INT(11),
  PRIMARY KEY (`student_id`, `predmet_id`),
  INDEX `fk_student_has_predmet_predmet1_idx` (`predmet_id` ASC),
  INDEX `fk_student_has_predmet_student1_idx` (`student_id` ASC),
  CONSTRAINT `fk_student_has_predmet_predmet1`
    FOREIGN KEY (`predmet_id`)
    REFERENCES `bazaZaWebProjekatDnevnik`.`predmet` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_has_predmet_student1`
    FOREIGN KEY (`student_id`)
    REFERENCES `bazaZaWebProjekatDnevnik`.`student` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
