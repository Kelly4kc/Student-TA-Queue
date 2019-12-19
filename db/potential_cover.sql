CREATE TABLE potential_cover
(
	cover_id     int NOT NULL REFERENCES cover,
  potential_ta_id int NOT NULL REFERENCES person,
  unique(cover_id, potential_ta_id)
);

