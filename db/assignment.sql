CREATE TABLE assignment
(
  id              serial  NOT NULL PRIMARY KEY,
	professor_id    int     NOT NULL REFERENCES person,
	course_id       int     NOT NULL REFERENCES course,
	due_date        date    NOT NULL,
	assignment_name varchar NOT NULL,
	assignment_link varchar,
  semester_id int REFERENCES semester NOT NULL
);