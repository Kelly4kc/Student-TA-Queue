copy /b /y delete.sql + semester.sql + person.sql + cover.sql + course.sql +  create.sql + assignment.sql + ta_course_experience.sql + queue.sql + question.sql + parent_child_question.sql + potential_cover.sql + schedule.sql + ta_availability.sql + semester_schedule.sql + copy.sql create.sql
psql -h webdev-group5.cyhnjzo4iwnw.us-east-1.rds.amazonaws.com -p 5432 -U coors -f create.sql taProj
