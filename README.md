# README #

This application is designed to copy your schedule from the Support & PSO Schedule sheet into a google calendar on a public account, which enables sharing of the events to users outside the granburyrs.com domain


### What is this repository for? ###

* Syncs google sheet scheudule to google calendar

### How do I get set up? ###

* Summary of set up
The application has configuration settings available in the top of sync.php.  You can open this file with a text editor.

* Configuration
$leftBound - Column letter to start parsing at.  Example: Start parsing on JAN-1, Set this to C
$rightBound - Column letter to stop parsing at.  Example: To stop parsing on 11-JAN, set this to M
$invitees - Broken!  Workaround by sharing the entire calendar via Google Calendar interface.  See [Google Calendar Help][1]
$firstName - First name as it appears on the schedule
$lastName - Last name as it appears on schedule.  Any - MGR or additional entries will be placed here
$calendarName - The name of the calendar you wish to copy the events to, as it appears in google.
APP_PATH - The path where this application lives.  On windows, replace \ in the windows path with / in the APP_PATH.  

* Dependencies
XAMPP - download at xampp.org
During installation, you only need Apache and PHP, everything else can be disabled.
All dependencies are pre installed.  If packages get dated, they are:

Google_Service_Calendar
Google_Service_Sheets

Which can be installed via composer

* Usage

Once PHP is installed, open a command line by holding the Windows Key and R, then typing cmd and hitting enter.
On the terminal, type: C:\xampp\php\php.exe -f <Path-To-This-Folder>\sync.php

* Common Errors
If you get an error about a refresh token, navigate to the credentials/ folder, and delete all entries.
This is due to the fact that I don't support refresh tokens yet.  This will get implemented in the future.

### Who do I talk to? ###

* Alex Schittko (aschittko@granburyrs.com) x22452


  [1]:  	https://support.google.com/calendar/answer/37082?hl=en