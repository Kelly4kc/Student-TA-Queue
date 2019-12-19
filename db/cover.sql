CREATE TABLE cover
(
  id serial NOT NULL PRIMARY KEY,
	unavailable_ta_id int  NOT NULL REFERENCES person,
	cover_ta_id       int REFERENCES person,
	date              date NOT NULL,
	start_time        int  NOT NULL,
	end_time          int  NOT NULL
);
