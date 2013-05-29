Enroll
======
Flexible scheduling for today's classroom.

Enroll is course registration software originally created for [Northside College Prep](http://northsideprep.org). The current version of Enroll handles the enrollment for Colloquium, X, and Y courses as seen in this sample [schedule](http://northsideprep.org/ncphs/Programming/Block%20Schedule.pdf).

Installation
------------
### MySQL Database
[enroll.sql](enroll.sql) should be used to install the necessary tables into an empty database named Enroll.

### Configure Settings File
[settings.php.sample](admin/settings.php.sample) should be renamed to settings.php and then configured with the settings specific to your school or organization.

### Course Images Folder
Create a folder in [img](img/) called courses. The user that your web server runs under should be given access to write to this folder. Enroll will use this folder to store all course images in.

Feedback
--------
Please send code specific questions and feedback to [Marcos Alcozer](mailto:mealcozer@gmail.com) or contact him on [twitter](http://twitter.com/marcos_chi). 