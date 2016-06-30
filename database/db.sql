-- -----------------------------------------------------
-- Table `films`.`actors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `films`.`actors` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `films`.`movies`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `films`.`movies` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `imdb_id` VARCHAR(45) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `title_foreign_language` VARCHAR(255) NULL,
  `imdb_rating` DECIMAL(2,1) NOT NULL,
  `personal_rating` DECIMAL(2,1) NULL,
  `year` INT NOT NULL,
  `runtime` INT NOT NULL,
  `plot` MEDIUMTEXT NOT NULL,
  `image_url` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `films`.`actor_movie`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `films`.`actor_movie` (
  `actor_id` INT NOT NULL,
  `movie_id` INT NOT NULL)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `films`.`genres`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `films`.`genres` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `films`.`genre_movie`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `films`.`genre_movie` (
  `genre_id` INT NOT NULL,
  `movie_id` INT NOT NULL)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `films`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `films`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `films`.`attempts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `films`.`attempts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(255) NOT NULL,
  `count` TINYINT NOT NULL,
  `lock_time` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `films`.`views`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `films`.`views` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `movie_id` VARCHAR(45) NOT NULL,
  `view_date` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


INSERT INTO `films`.`users` (`id`, `username`, `password`) VALUES (1, 'test', '$2y$10$zGCLkCfqgyt5mlDGOAxN.OFsjmMCMRPtQOysn4Gj2Mhk/zG33IBDu');