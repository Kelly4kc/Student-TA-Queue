CREATE TABLE schedule
(
	ta_id      int  NOT NULL REFERENCES person,
	day        days NOT NULL,
	start_time int  NOT NULL,
	end_time   int  NOT NULL,
  semester_id int REFERENCES semester
);

