# Availability Calendar

**Warning:** This is my first attempt at an MVC project in CodeIgniter, and one of my first projects in general. As a result, it may be a little inconsistent in technique and process, and probably a little rough around the edges. It is mainly for potential employers to see *where I'm at* rather than being something ready for adoption. 

### Video Demo (click the image)

Note: This is an early demo version and needes to be updated. 

[![Demo](https://img.youtube.com/vi/K8l2x3Ow47Q/0.jpg)](https://youtu.be/Dy4SnFRVhXQ)

## Introduction

This minimalist, calendar based people organiser allows a group of people to mark their availability for project tasks created by an admin group. The admin group are managed by a super admin group, and well...that's it. 

Various projects can run on various days of the week, and each project day can have a status set to public or draft. Tasks are created for each project day, and the project creator can set the number of volunteers required for each task. Volunteers log into the system, select a project (their previous project view is stored in the database), and then select a date from the calendar. The volunteer then selects the task(s) they want to participate in, and save their selection. Dates/days are only selectable if the project is running on those dates/days. 

## Other features

* Admin can cancel a particular date though a cancelled date interface.  
* An archive feature. Admin can view a monthly summary of activity for a given project along with a log of any edits to the project that month. 
* Ability to create a general info page. 
* Ability to hide projects from users through a requirements interface. User/project requirements can be created. A project can then subscribe to the requirement, and users have to be subscribed (by an admin) to the requirement in order to have visibility of the project. 
* Contact admin form. 
* Credential management. 

## Permissions

The system is closed to registration form the outside world. Only admins can register new users. The super admin can create/edit users and regular admins. Projects can only be edited by admins and super admins. 

## Third party acknowledgement 

This app uses the CodeIgniter framework, Ion Auth for user management (within CodeIgniter), and query UI for the calendar widget and other JavaScript wizardry. 

## Installing 

You'll want to check out the db_setup folder and create the database/default users. Then setup the CodeIgniter environment (Currently 3.1.5).
