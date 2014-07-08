# Moodle Main Course User Profile Field

This plugin adds a drop-down menu option to the customisable user profile fields, showing a list of courses that user is enrolled on. The user can select the course which is considered to be the 'main' programme of study.


## Installation

* If you cloned the repo, rename the folder called moodle-profilefield_maincourse to maincourse
* if you downloaded a Zipped archive of the repository, extract the folder with the name s


## How to use

Create a new custom profile field using the plugin:

* Log in to your Moodle as admin.
* Click Administration -> Site Administration -> Users -> Accounts -> User profile fields
* On the drop-down menu, click the option Main Course menu
* Fill in the form appropriately:
	* Short Name is a Moodle-internal name, the user won't see it.
	* Name is how the menu will be shown to the user.
	* Description is just that.
	* Required? Choose Yes.
	* Locked? Probably not.
	* Unique? Definitely not, or you'll get problems.
	* Display on sign-up page? Probably not, as enrolments haven't been processed yet.
	* Visible to? Choose Everyone.
	* Choose any valid option for the Category. You may even want to create a category called 'Main Course' and add it to that so it stands out more.
* Save settings.

Now each user can choose their main course from their profile page:

* Go to your profile page (clicking on your name usually achieves this).
* Click Administration -> Edit profile
* Scroll to the section Other Settings (or if you created your own section, that one).
* Choose a course from the drop-down menu.
* Save the settings.
* On the profile view page which follows, you'll see a new entry similar to "Main Course: Internet Security (IS001)".


## To do

* Only show student roles, not teacher, manager etc? (This doesn't duplicate entries in the menu, but they probably shouldn't be there.)
* Ignore certain course IDs.
* Make the above be configurable.


## Bugs

Notice: Undefined property: stdClass::$defaultdata in /srv/moodle2_dev/releases/20140402133502/user/profile/definelib.php on line 548
