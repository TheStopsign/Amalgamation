DROP DATABASE IF EXISTS amalgamation;
CREATE DATABASE amalgamation;

CREATE TABLE users (
      rcs VARCHAR(255), 
      password VARCHAR(255), 
      PRIMARY KEY (rcs)
);

CREATE TABLE projects (
    ProjectID INT AUTO_INCREMENT,
    name varchar(255),
	Description TEXT,
    history JSON,
    PRIMARY KEY (ProjectID)
);

CREATE TABLE permissions (
    PermID INT AUTO_INCREMENT,
    ProjectID INT,
	rcs VARCHAR(255),
    perm VARCHAR(255),
    PRIMARY KEY (PermID),
    FOREIGN KEY (ProjectID) REFERENCES projects(ProjectID),
	FOREIGN KEY (rcs) REFERENCES users(rcs)
);





INSERT INTO users (rcs, password) VALUES
('blakee3', 'password123'),
('smithj1', 'securePW'),
('jonest2', '123456789');

INSERT INTO projects (name, Description) VALUES 
('test project 1', 'Here\'s a fun short description of your project! Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua'),
('test project 2', 'existance is meaningless'),
('test project 3', 'i can eat an entire cantelope in a single sitting'),
('test project 4', 'i wish the roman empire would return'),
('test project 5', 'hehe');

INSERT INTO permissions (ProjectID, rcs, perm) VALUES 
(1, 'blakee3', 'owner'),
(2, 'blakee3', 'owner'),
(3, 'blakee3', 'owner'),
(4, 'blakee3', 'edit'),
(4, 'jonest2', 'owner');
