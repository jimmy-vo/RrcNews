DROP TABLE IF EXISTS  `serverside`.`Users`;
DROP TABLE IF EXISTS `serverside`.`News`;
DROP TABLE IF EXISTS `serverside`.`Comments`;

CREATE TABLE `serverside`.`Users`
(
    `Id` 				INT 		NOT NULL 	AUTO_INCREMENT, 
    `UserTypeId` 		INT,
    `UserName` 			TEXT,
    `Password` 			TEXT,
    PRIMARY KEY (`Id`)
);

CREATE TABLE `serverside`.`News`
(
    `Id` 				INT 		NOT NULL 	AUTO_INCREMENT, 
	`UserId`			INT,
    `Title` 			TEXT,
    `Image` 			BLOB,
    `Content` 			TEXT,
    `TimeCreated` 		DATETIME 	DEFAULT CURRENT_TIMESTAMP,
    `TimeUpdated` 		DATETIME 	DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`Id`)
);

CREATE TABLE `serverside`.`Comments`
(
    `Id` 				INT 		NOT NULL 	AUTO_INCREMENT, 
	`NewsId`			INT,
	`UserId`			INT,
	`GuestName`			TEXT,
	`Hidden`			BIT 		DEFAULT 0,
    `Content` 			TEXT,
    `TimeCreated` 		DATETIME	DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`Id`)
);

INSERT INTO `users` (`Id`, `UserTypeId`, `UserName`, `Password`) 
	VALUES ('0', '0', 'root', '$2y$10$NRGuMNg12YnXHANmyQO/NOsdU/WRY80UvWSH1W00LYb8vLREeZe/G');

INSERT INTO `users` (`Id`, `UserTypeId`, `UserName`, `Password`) 
	VALUES ('2', '0', 'Jimmy', '$2y$10$NRGuMNg12YnXHANmyQO/NOsdU/WRY80UvWSH1W00LYb8vLREeZe/G');

INSERT INTO `users` (`Id`, `UserTypeId`, `UserName`, `Password`) 
	VALUES ('3', '1', 'test', '$2y$10$NRGuMNg12YnXHANmyQO/NOsdU/WRY80UvWSH1W00LYb8vLREeZe/G');
	
	