CREATE TABLE question
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
);