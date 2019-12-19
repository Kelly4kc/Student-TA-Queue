DROP TABLE IF EXISTS ta_availability;
DROP TABLE IF EXISTS schedule;
DROP TABLE IF EXISTS potential_cover;
DROP TABLE IF EXISTS parent_child_question;
DROP TABLE IF EXISTS question;
DROP TABLE IF EXISTS queue;
DROP TABLE IF EXISTS ta_course_experience;
DROP TABLE IF EXISTS assignment;
DROP TABLE IF EXISTS course;
DROP TABLE IF EXISTS cover;
DROP TABLE IF EXISTS person;
DROP TABLE IF EXISTS semester_schedule;
DROP TABLE IF EXISTS semester;
DROP TYPE IF EXISTS role;
DROP TYPE IF EXISTS day;
DROP TYPE IF EXISTS term;CREATE TYPE term AS enum ('Spring', 'Summer', 'Fall', 'Winter');

CREATE TABLE semester
(
	id         serial NOT NULL PRIMARY KEY,
	term       term   NOT NULL,
	start_date date   NOT NULL,
	end_date   date   NOT NULL
);
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

CREATE TABLE cover
(
  id serial NOT NULL PRIMARY KEY,
	unavailable_ta_id int  NOT NULL REFERENCES person,
	cover_ta_id       int REFERENCES person,
	date              date NOT NULL,
	start_time        int  NOT NULL,
	end_time          int  NOT NULL
);
CREATE TABLE course
(
	id            serial   NOT NULL PRIMARY KEY,
  subject varchar NOT NULL,
	number  int
);

CREATE TABLE assignment
(
  id              serial  NOT NULL PRIMARY KEY,
	professor_id    int     NOT NULL REFERENCES person,
	course_id       int     NOT NULL REFERENCES course,
	due_date        date    NOT NULL,
	assignment_name varchar NOT NULL,
	assignment_link varchar,
  semester_id int REFERENCES semester NOT NULL
);CREATE TABLE ta_course_experience
(
	ta_id     int NOT NULL REFERENCES person,
	course_id int NOT NULL REFERENCES course
);

CREATE TABLE queue
(
	id         serial        NOT NULL PRIMARY KEY,
	author     varchar       NOT NULL,
	course_id  int REFERENCES course,
  assignment_id int REFERENCES assignment,
	time_added timestamp          NOT NULL,
  time_resolved timestamp,
  resolver_id int REFERENCES person,
  semester_id int REFERENCES semester
);CREATE TABLE question
(
	id         serial        NOT NULL PRIMARY KEY,
	author     varchar       NOT NULL,
	question   text          NOT NULL,
	course_id  int REFERENCES course,
  assignment_id int REFERENCES assignment,
  likes      int DEFAULT 0 NOT NULL,
	time_asked timestamp          NOT NULL,
  time_resolved timestamp,
  resolver_id int REFERENCES person,
  resolution text,
  semester_id int REFERENCES semester
);CREATE TABLE parent_child_question
(
  parent_question_id int REFERENCES question,
  child_question_id int REFERENCES question
);CREATE TABLE potential_cover
(
	cover_id     int NOT NULL REFERENCES cover,
  potential_ta_id int NOT NULL REFERENCES person,
  unique(cover_id, potential_ta_id)
);

CREATE TABLE schedule
(
	ta_id      int  NOT NULL REFERENCES person,
	day        days NOT NULL,
	start_time int  NOT NULL,
	end_time   int  NOT NULL,
  semester_id int REFERENCES semester
);

CREATE TYPE day AS enum ('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

CREATE TABLE ta_availability
(
	ta_id      int  NOT NULL REFERENCES person,
	day        day NOT NULL,
	start_time int  NOT NULL,
	end_time   int  NOT NULL,
  semester_id int REFERENCES semester
);
CREATE TABLE semester_schedule
(
	day        day NOT NULL,
	start_time int  NOT NULL,
	end_time   int  NOT NULL,
  semester_id int REFERENCES semester
);\copy semester(term, start_date, end_date) from 'csv_data/semester.csv' delimiter ',' NULL AS '' CSV HEADER;
\copy course(subject, number) from 'csv_data/cs_courses.csv' delimiter ',' NULL AS '' CSV HEADER;
\copy person(eid, password, first_name, last_name, role, semester_id) from 'csv_data/people.csv' delimiter ',' NULL AS '' CSV HEADER;
\copy assignment(professor_id, course_id, due_date, assignment_name, assignment_link, semester_id) from 'csv_data/assignments.csv' delimiter ',' NULL AS '' CSV HEADER;
\copy question(author, question, course_id, assignment_id, time_asked, time_resolved, resolver_id, resolution) from 'csv_data/questions.csv' delimiter ',' NULL AS '' CSV HEADER;
\copy schedule(ta_id, day, start_time, end_time, semester_id) from 'csv_data/schedule.csv' delimiter ',' NULL AS '' CSV HEADER;
\copy ta_availability(ta_id, day, start_time, end_time, semester_id) from 'csv_data/ta_availability.csv' delimiter ',' NULL AS '' CSV HEADER;
\copy cover(unavailable_ta_id, cover_ta_id, date, start_time, end_time) from 'csv_data/covers.csv' delimiter ',' NULL AS '' CSV HEADER;
\copy ta_course_experience(ta_id, course_id) from 'csv_data/ta_course_experience.csv' delimiter ',' NULL AS '' CSV HEADER;
\copy semester_schedule(day, start_time, end_time, semester_id) from 'csv_data/semester_schedule.csv' delimiter ',' NULL AS '' CSV HEADER;
\copy parent_child_question(parent_question_id, child_question_id) from 'csv_data/parent_child_questions.csv' delimiter ',' NULL AS '' CSV HEADER;