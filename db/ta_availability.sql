CREATE TYPE day AS enum ('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

CREATE TABLE ta_availability
(
	ta_id      int  NOT NULL REFERENCES person,
	day        day NOT NULL,
	start_time int  NOT NULL,
	end_time   int  NOT NULL,
  semester_id int REFERENCES semester
);
