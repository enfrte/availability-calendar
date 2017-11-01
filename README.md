# Availability Calendar

**Warning:** This is my first attempt at an MVC project in CodeIgniter, and one of my first projects in general. As a result, it may be a little inconsistent in technique and process, and probably a little rough around the edges. It is mainly for potential employers to see *where I'm at* rather than being something ready for adoption. 

### Video Demo (click the image)

[![DIY Lingo Demo](https://img.youtube.com/vi/K8l2x3Ow47Q/0.jpg)](https://www.youtube.com/watch?v=K8l2x3Ow47Q)

## Introduction

This minimalist, calendar based people organiser allows a group of people to mark their availability for project tasks created by an admin group. The admin group are managed by a super admin group, and well...that's it. 

Various projects can run on various days of the week, and each project day can have a status set to public or draft. Tasks are created for each project day, and the project creator can set the number of volunteers required for each task. Volunteers log into the system, select a project (their previous project view is stored in the database), and then select a date from the calendar. The volunteer then selects the task(s) they want to participate in, and save their selection. Dates/days are only selectable if the project is running on those dates/days. Admin can also cancel a particular date though a cancelled date interface.  

## Permissions

The system is closed to registration form the outside world. Only admins can register new users. The super admin can create/edit users and regular admins. Projects can only be edited by admins and super admins. 

## To do

There are still a couple of things to do to polish this app off, but for the most part, it is finished. 

## Third party acknowledgement 

This app uses the CodeIgniter framework, Ion Auth for user management (within CodeIgniter), and query UI for the calendar widget and other JavaScript wizardry. 

## Installing 

You'll want to check out the db_setup folder and create the database/default users. Then setup the CodeIgniter environment (Currently 3.1.5).
