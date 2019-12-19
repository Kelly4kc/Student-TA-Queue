CREATE TYPE term AS enum ('Spring', 'Summer', 'Fall', 'Winter');

CREATE TABLE semester
(
	id         serial NOT NULL PRIMARY KEY,
	term       term   NOT NULL,
	start_date date   NOT NULL,
	end_date   date   NOT NULL
);
