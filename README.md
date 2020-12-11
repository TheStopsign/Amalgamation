# Team Tux


## Overview
Team tux created a shared doodling page web application.
Users create accounts linked to their rcs ID.
They can then create projects and share them with others. 
Multiple users can edit the doodles at the same time, making use of our drawing tools suite.

### Login Page
Our login page links with CAS authentication to allow RPI students to create accounts easily.

### Dashboard
The dashboard integrates with the CAS login using a PHP session, and serves data stored in our database. Projects the user owns and has shared with them are displayed, along with their level of access. From here, the user can share projects, unshare projects, and add or delete projects. For administrative purposes, and to allow restores, deleted projects are not truly deleted but their permissions are removed from that user.  

The user can also open the project to open our live collaboration session.

### Doodling Page

Using the open source doodling widget Literally Canvas, users can draw on a canvas provided. All accounts the project is shared with can edit simultaniously and recieve updates in real time. The doodling page also contains a real time updated count of the users on the drawing page.

