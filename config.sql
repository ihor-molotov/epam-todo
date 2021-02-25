CREATE TABLE users
(
    id       INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(20)  NOT NULL,
    password VARCHAR(255) NOT NULL,
    salt     VARCHAR(255) NOT NULL
);

CREATE TABLE items
(
    id      INT PRIMARY KEY AUTO_INCREMENT,
    name    TEXT    NOT NULL,
    user    INT(11),
    done    BOOLEAN NOT NULL DEFAULT 0,
    created DATETIME
);
