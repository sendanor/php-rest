/* Contacts */
CREATE TABLE `contact` (
    contact_id        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    updated           TIMESTAMP NOT NULL DEFAULT 0,
    creation          TIMESTAMP NOT NULL DEFAULT 0,
    name              VARCHAR(255) NOT NULL DEFAULT '',
    email             VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY(contact_id)) CHARACTER SET utf8;
