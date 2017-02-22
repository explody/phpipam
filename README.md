Description
===========
phpipam is an open-source web IP address management application. Its goal is to provide light and simple IP address management application.
It is ajax-based using jQuery libraries, it uses php scripts and javascript and some HTML5/CSS3 features, so some modern browser is preferred
to be able to display javascript quickly and correctly.

### Intentions of this fork

* Consistent coding standards across the board (where possible)
* Move all external PHP dependencies under composer
* Move all external JS/CSS dependencies under npm
* Manage static assets with grunt
* Semantic versioning
* Manageable database via Phinx migrations
* DRY
 * Consolidate frequently repeated code into central methods
* Less code, less text, less whitespace
 * Remove unnecessary conditionals, comments
* Consistent output from internal and REST API methods
 * Don't intermix return types where avoidable (e.g. return [], not 'false')
* php-cs-fixer code formatting
 * Consistent indentiation and formatting
* Better handling of large data sets
* Drop backwards support for excessively old PHP, DB, etc.
* Move towards actual OOP as possible

### Where the fork is going



Features and tools:
- https://phpipam.net/documents/features/

License
=======
phpipam is released under the GPL v3 license, see misc/gpl-3.0.txt.

Requirements
============
- https://phpipam.net/documents/installation/

Install
=======
- https://phpipam.net/documents/installation/

API guide
=========
- https://phpipam.net/api-documentation/

Update
=======
- https://phpipam.net/documents/upgrade/

Demo page
============
http://demo.phpipam.net

Default user
============
Admin / ipamadmin

Reset admin password
====================
php functions/scripts/reset-admin-password.php

Changelog
=========
See misc/CHANGELOG

Roadmap
=========
See misc/Roadmap

Contact
=======
miha.petkovsek@gmail.com

special thank also to Hosterdam team (http://www.hosterdam.com) for VPS server
that is used for development of phpIPAM and for demo site.

And also to all users that filed a bug report / feature report and helped with feature testing!
