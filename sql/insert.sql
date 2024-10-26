CREATE DATABASE quiz_app;

USE quiz_app;


CREATE TABLE questions
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    UNIQUE (question)
);


-- The answers table references questions by question_id.
CREATE TABLE answers
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    answer_option VARCHAR(255),
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (question_id) REFERENCES questions (id) ON DELETE CASCADE
);
