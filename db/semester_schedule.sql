CREATE TABLE semester_schedule
(
	day        day NOT NULL,
	start_time int  NOT NULL,
	end_time   int  NOT NULL,
  semester_id int REFERENCES semester
);