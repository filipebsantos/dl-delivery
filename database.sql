-- DROP SCHEMA dbo;

CREATE SCHEMA dbo;
-- DLDELIVERY.dbo.clients definition

-- Drop table

-- DROP TABLE DLDELIVERY.dbo.clients;

CREATE TABLE DLDELIVERY.dbo.clients (
	id smallint NOT NULL,
	name varchar(50) COLLATE SQL_Latin1_General_CP1_CI_AI NOT NULL,
	CONSTRAINT clients_pk PRIMARY KEY (id)
);


-- DLDELIVERY.dbo.users definition

-- Drop table

-- DROP TABLE DLDELIVERY.dbo.users;

CREATE TABLE DLDELIVERY.dbo.users (
	id smallint IDENTITY(1,1) NOT NULL,
	username varchar(50) COLLATE SQL_Latin1_General_CP1_CI_AI NOT NULL,
	firstname varchar(50) COLLATE SQL_Latin1_General_CP1_CI_AI NOT NULL,
	lastname varchar(50) COLLATE SQL_Latin1_General_CP1_CI_AI NOT NULL,
	token varchar(32) COLLATE SQL_Latin1_General_CP1_CI_AI NULL,
	tokenexpiration datetime NULL,
	[role] tinyint DEFAULT 1 NOT NULL,
	active bit DEFAULT 0 NOT NULL,
	password varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AI NOT NULL,
	phonenumber varchar(13) COLLATE SQL_Latin1_General_CP1_CI_AI NULL,
	CONSTRAINT users_pk PRIMARY KEY (id)
);


-- DLDELIVERY.dbo.locations definition

-- Drop table

-- DROP TABLE DLDELIVERY.dbo.locations;

CREATE TABLE DLDELIVERY.dbo.locations (
	id int IDENTITY(1,1) NOT NULL,
	clientid smallint NOT NULL,
	latitude varchar(30) COLLATE SQL_Latin1_General_CP1_CI_AI NOT NULL,
	longitude varchar(30) COLLATE SQL_Latin1_General_CP1_CI_AI NOT NULL,
	neighborhood varchar(30) COLLATE SQL_Latin1_General_CP1_CI_AI NOT NULL,
	housepicture varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AI NULL,
	obs text COLLATE SQL_Latin1_General_CP1_CI_AI NULL,
	[type] varchar(20) COLLATE SQL_Latin1_General_CP1_CI_AI NOT NULL,
	CONSTRAINT clients_location_pk PRIMARY KEY (id),
	CONSTRAINT clients_location_clients_FK FOREIGN KEY (clientid) REFERENCES DLDELIVERY.dbo.clients(id) ON UPDATE CASCADE
);


-- DLDELIVERY.dbo.routes definition

-- Drop table

-- DROP TABLE DLDELIVERY.dbo.routes;

CREATE TABLE DLDELIVERY.dbo.routes (
	id int IDENTITY(1,1) NOT NULL,
	deliveryman smallint NOT NULL,
	[user] smallint NOT NULL,
	starttime time(3) NULL,
	endtime time(3) NULL,
	status varchar(20) COLLATE SQL_Latin1_General_CP1_CI_AI DEFAULT 'PENDENTE' NOT NULL,
	datecreation datetime DEFAULT getdate() NOT NULL,
	CONSTRAINT routes_pk PRIMARY KEY (id),
	CONSTRAINT routes_delivery_users_FK FOREIGN KEY (deliveryman) REFERENCES DLDELIVERY.dbo.users(id) ON UPDATE CASCADE,
	CONSTRAINT routes_users_FK FOREIGN KEY ([user]) REFERENCES DLDELIVERY.dbo.users(id)
);

-- DLDELIVERY.dbo.routes_clients definition

-- Drop table

-- DROP TABLE DLDELIVERY.dbo.routes_clients;

CREATE TABLE DLDELIVERY.dbo.routes_clients (
	id int IDENTITY(1,1) NOT NULL,
	routeid int NOT NULL,
	clientid smallint NOT NULL,
	phonenumber varchar(13) COLLATE SQL_Latin1_General_CP1_CI_AI NULL,
	status tinyint DEFAULT 0 NULL,
	CONSTRAINT routes_clients_pk PRIMARY KEY (id),
	CONSTRAINT routes_clients_clients_FK FOREIGN KEY (clientid) REFERENCES DLDELIVERY.dbo.clients(id) ON UPDATE CASCADE,
	CONSTRAINT routes_clients_routes_FK FOREIGN KEY (routeid) REFERENCES DLDELIVERY.dbo.routes(id) ON DELETE CASCADE
);


-- DLDELIVERY.dbo.routes_clients foreign keys

ALTER TABLE DLDELIVERY.dbo.routes_clients ADD CONSTRAINT routes_clients_clients_FK FOREIGN KEY (clientid) REFERENCES DLDELIVERY.dbo.clients(id) ON UPDATE CASCADE;
ALTER TABLE DLDELIVERY.dbo.routes_clients ADD CONSTRAINT routes_clients_routes_FK FOREIGN KEY (routeid) REFERENCES DLDELIVERY.dbo.routes(id) ON DELETE CASCADE;


-- DLDELIVERY.dbo.routes_locations definition

-- Drop table

-- DROP TABLE DLDELIVERY.dbo.routes_locations;

CREATE TABLE DLDELIVERY.dbo.routes_locations (
	id int IDENTITY(1,1) NOT NULL,
	routeid int NOT NULL,
	locationid int NULL,
	CONSTRAINT routes_locations_pk PRIMARY KEY (id),
	CONSTRAINT routes_locations_locations_FK FOREIGN KEY (locationid) REFERENCES DLDELIVERY.dbo.locations(id) ON DELETE SET NULL,
	CONSTRAINT routes_locations_routes_FK FOREIGN KEY (routeid) REFERENCES DLDELIVERY.dbo.routes(id) ON DELETE CASCADE
);

INSERT INTO DLDELIVERY.dbo.users (username,firstname,lastname,[role],active,password) VALUES ('inovafarma', 'INOVA', 'FARMA', 4, 1, '$2y$10$gffsJDr4HRWavp8o7hAFgeVKqY3SOvF4WahqwSu3Sx2D2qTiQPITe');