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
);