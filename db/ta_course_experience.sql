CREATE TABLE ta_course_experience
(
	ta_id     int NOT NULL REFERENCES person,
	course_id int NOT NULL REFERENCES course
);

