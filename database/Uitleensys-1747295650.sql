CREATE TABLE IF NOT EXISTS `categorie` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`Categorie_naam` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `product` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`Product_naam` varchar(255) NOT NULL,
	`catergorie_id` int NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `leenlijst` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`inlever_datum` date NOT NULL,
	`beschikbaarheid` boolean NOT NULL,
	`product_id` int NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `user` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`user_name` varchar(255) NOT NULL,
	`password` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `student` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`student_naam` int NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `schade` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`beschrijving` text NOT NULL,
	`docent_id` int NOT NULL,
	`student_id` int NOT NULL,
	`product_id` int NOT NULL,
	PRIMARY KEY (`id`)
);


ALTER TABLE `product` ADD CONSTRAINT `product_fk2` FOREIGN KEY (`catergorie_id`) REFERENCES `categorie`(`id`);
ALTER TABLE `leenlijst` ADD CONSTRAINT `leenlijst_fk3` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`);


ALTER TABLE `schade` ADD CONSTRAINT `schade_fk2` FOREIGN KEY (`docent_id`) REFERENCES `user`(`id`);

ALTER TABLE `schade` ADD CONSTRAINT `schade_fk3` FOREIGN KEY (`student_id`) REFERENCES `student`(`id`);

ALTER TABLE `schade` ADD CONSTRAINT `schade_fk4` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`);