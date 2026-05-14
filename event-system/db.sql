CREATE DATABASE IF NOT EXISTS event_management;
USE event_management;

CREATE TABLE IF NOT EXISTS project_charter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_title VARCHAR(255) NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    organization VARCHAR(255) NOT NULL,
    project_manager VARCHAR(255) NOT NULL,
    team_members TEXT,
    objectives TEXT,
    scope TEXT,
    budget DECIMAL(10,2),
    venue VARCHAR(255),
    start_date DATE,
    end_date DATE,
    status ENUM('Planning','Ongoing','Completed','Cancelled') DEFAULT 'Planning',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS timeline_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    charter_id INT NOT NULL,
    task_name VARCHAR(255) NOT NULL,
    assigned_to VARCHAR(255),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('Not Started','In Progress','Completed') DEFAULT 'Not Started',
    FOREIGN KEY (charter_id) REFERENCES project_charter(id) ON DELETE CASCADE
);
