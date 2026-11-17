CREATE DATABASE IF NOT EXISTS petcare_db;

USE petcare_db;


CREATE TABLE IF NOT EXISTS users (

    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    email VARCHAR(190) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL

) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS appointments (

    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    pet_name VARCHAR(100) NOT NULL,

    service VARCHAR(100) NOT NULL,

    date DATE NOT NULL,

    status VARCHAR(30) NOT NULL DEFAULT 'Pending',

    CONSTRAINT fk_user

        FOREIGN KEY (user_id)

        REFERENCES users(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE

) ENGINE = InnoDB;