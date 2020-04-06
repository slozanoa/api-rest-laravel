CREATE DATABASE IF NOT EXISTS  project_film;
USE project_film;

CREATE TABLE users(
id                 int(255) auto_increment NOT NULL, 
name               varchar(50) NOT NULL,
surname            varchar(100),
role               varchar(120),
email              varchar(255) NOT NULL,
password           varchar(255) NOT NULL,
description        text,
image              varchar(255),
created_at         datetime DEFAULT NULL ,
updated_ad         datetime DEFAULT NULL,
CONSTRAINT pk_users PRIMARY KEY(id) 
)ENGINE=InnoDb;

CREATE TABLE categories(
id            int(255) auto_increment NOT NULL,
name          varchar(50) NOT NULL,
created_at    datetime DEFAULT NULL,
updated_at    datetime DEFAULT NULL,
CONSTRAINT pk_categories PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE films(
id              int(255) auto_increment NOT NULL,
user_id         int(255) NOT NULL,
category_id     int(255) NOT NULL,
title           varchar(255) NOT NULL,
content         text NOT NULL,
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
CONSTRAINT pk_films PRIMARY KEY(id),
CONSTRAINT fk_film_user FOREIGN KEY (user_id) REFERENCES users(id),
CONSTRAINT fk_film_category FOREIGN KEY (category_id) REFERENCES categories(id)   
)ENGINE=InnoDb;
