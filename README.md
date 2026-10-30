# AUAF-event-management-website
# Project Description: AUAF Event Management Website

The **AUAF Event Management Website** is a web-based platform designed to help the American University of Afghanistan (AUAF) community discover student clubs, explore upcoming events, and register for virtual activities. The system provides a centralized platform where student clubs can create accounts, share information about their clubs, and manage their events independently.

Clubs can register using their official AUAF email addresses, provide a description of their club, and share a Google Space link so interested students can connect with them. After logging in, club representatives can create, edit, and cancel events, provide virtual event links, and monitor student registrations through their dashboards.

Students can browse available clubs, view club details, explore upcoming virtual events, and register using their first name, last name, and AUAF student email address. The website also allows students to access virtual event links, making it easier to participate in online activities.

The system is developed using **PHP, MySQL, HTML, CSS, Bootstrap, and JavaScript**. MySQL stores club information, event details, and student registrations, while PHP processes forms and manages communication between the website and database.

The main objective of this project is to simplify event management, improve communication between student clubs and students, and provide an organized, accessible platform for promoting and participating in virtual university events.

Database Design and Entity-Relationship Diagram

The Entity-Relationship Diagram illustrates the structure of the database used in the AUAF Event Management Website. It consists of three main entities: clubs, events, and event_registrations. Each entity contains attributes that store the information required for the website to function.

The clubs table stores club information, including the club name, official email, description, Google Space URL, and password hash. Each club is assigned a unique club_id, which serves as its primary key.

The events table stores information about events created by clubs, including the event title, description, date, time, virtual event URL, agenda, and status. The event_id is the primary key, while club_id is a foreign key that links each event to the club that created it. One club can create multiple events, establishing a one-to-many relationship between the clubs and events tables.

The event_registrations table stores student registration information, including first name, last name, student email, and registration date. The registration_id is the primary key, and event_id is a foreign key connecting each registration to its corresponding event. One event can have multiple student registrations, establishing a one-to-many relationship between the events and event_registrations tables.

Overall, the ER diagram demonstrates how the database organizes club information, virtual events, and student registrations while maintaining relationships between the tables through primary and foreign keys. This structure supports the website's core functionality and helps maintain data consistency.
