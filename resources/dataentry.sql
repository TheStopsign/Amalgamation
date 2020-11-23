DROP DATABASE IF EXISTS amalgamation;
CREATE DATABASE amalgamation;

CREATE TABLE users (
      UserID INT AUTO_INCREMENT, 
      name VARCHAR(255), 
      password VARCHAR(255), 
      PRIMARY KEY (UserID)
);

CREATE TABLE projects (
    ProjectID INT AUTO_INCREMENT,
    name varchar(255),
    UserID INT,
	Description TEXT,
    PRIMARY KEY (ProjectID),
    FOREIGN KEY (UserID) REFERENCES users(UserID)
);


CREATE TABLE collaborators (
    CollabID INT AUTO_INCREMENT,
    permissions VARCHAR(255),
    ProjectID int,
    PRIMARY KEY (CollabID),
    FOREIGN KEY (ProjectID) REFERENCES projects(ProjectID)
);



INSERT INTO users (name, password) VALUES
('Ted', 'password123'),
('Alice', 'securePW'),
('Roger', '123456789');

INSERT INTO projects (name, UserID, Description) VALUES 
('test project 1', 1, 'Here\'s a fun short description of your project! Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua'),
('test project 2', 1, 'existance is meaningless'),
('test project 3', 1, 'i can eat an entire cantelope in a single sitting'),
('test project 4', 1, 'i wish the roman empire would return'),
('test project 5', 1, 'hehe');



