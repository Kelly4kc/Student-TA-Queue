CREATE TYPE role AS enum ('Manager', 'TA', 'Professor', 'Admin');

CREATE TABLE person
(
    id         serial     NOT NULL PRIMARY KEY,
    eid        varchar(8) NOT NULL,
    password   varchar    NOT NULL,
    first_name varchar    NOT NULL,
    last_name  varchar    NOT NULL,
    role       role       NOT NULL,
    semester_id int NOT NULL REFERENCES semester,
    max_hours int,
    UNIQUE(eid)
);

