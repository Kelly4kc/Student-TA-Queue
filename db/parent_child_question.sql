CREATE TABLE parent_child_question
(
  parent_question_id int REFERENCES question,
  child_question_id int REFERENCES question
);