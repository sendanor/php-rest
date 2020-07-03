
CREATE TABLE user (
    user_id       BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_locked   BOOL NOT NULL DEFAULT FALSE
);

CREATE TABLE email (
    email_address  VARCHAR(255) UNIQUE NOT NULL,
    email_created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE email_auth (
    email_auth_email         VARCHAR(255) UNIQUE NOT NULL,
    email_auth_created       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    email_auth_public_token  VARCHAR(255) UNIQUE NOT NULL,
    email_auth_secret_token  VARCHAR(255) NOT NULL
);

CREATE TABLE session (
    session_id       BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    session_email    VARCHAR(255) DEFAULT '',
    session_created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
