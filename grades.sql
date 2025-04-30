CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,  
    student_id INT,
    subject_id INT,
    grade INT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (subject_id) REFERENCES subject(id)
);