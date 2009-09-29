                         __
                      __|  |__
      ______   _____ |__    __|
     /  __  \ |   _ \   |  |
     | |  | | |  | \ \  |  |
     | |__| | |  |_/ /  |  |_
     \______/ |   __/    \___| emplate
              |  |
     Open     |__| ower

     Open Power Template v. 1.1.4 readme
     Professional templating engine for PHP 5
     
1. PROJECT INFORMATION

Open Power Template is a templating engine library written in PHP. The library contains many useful
features implemented, both low- and high-level, as well as the possibility of extending. The project
is a part of Open Power Libraries. Visit the website: http://libs.invenzzia.org/

  !!!!!!!!!!!IMPORTANT NOTE!!!!!!!!!!!
  
  This is the last version of the 1.x branch. Since now, we will focus only on OPT 2.0.0, which will
  be available soon.

2. REQUIREMENTS

OPT 1.1.4 requires at least PHP 5.0 or better.

3. PACKAGE CONTENTS

/docs - reference manual (HTML, English version) - only the opt-1.x.x-docs archives!
/examples - various ready-to-run feature examples
/lib - library sources
/toolset - OPT Toolset files
/unitTest - unit test files
/readme.txt - you are reading it at the moment
/COPYING - license text

4. SHORT INSTALLATION

 1. Create directories: "templates" for your template files, "templates_c" for compiled templates
 2. "templates" has to be readable for the server, "templates_c" has to be writable for the server
 3. Copy the files from the "lib" directory into your project directory
 4. Include "opt.class.php" file into your project
 5. Enjoy!
 
5. USER MANUAL

If you have downloaded the "opt.1.x.x-docs" version, you can find the HTML English manual in the
/docs directory. The latest version is available on http://libs.invenzzia.org/

6. UNIT TESTS

How to do unitTests:

 1. Go to http://pear.php.net/package/PHPUnit and download the latest 1.3.x version of PHPUnit package.
 2. Extract it to the /unitTest/PHPUnit directory. The "PHPUnit.php" file must be under the location /unitTest/PHPUnit/PHPUnit.php. Be sure you have kept the OPT source location, as it is in the archive.
 3. Run the test files.

Test files:
 1. testme.php - basic test cases of the main OPT parser.
 2. testme_html.php - browser-friendly version of the script above.
 3. testcompiler.php - compilation tests.
 4. testcompiler_html.php - browser-friendly version of the script above.

7. LICENSE AND AUTHORS

In the beginning, OPT was a part of the OpenPB project, which did not survive. The library was
written by Tomasz "Zyx" Jędrzejewski. In 2008, he founded Invenzzia group and redirected the project
to it. That's why previous releases had different copyright headlines.

Project authors:
* Tomasz "Zyx" Jedrzejewski - main developer, programmer, documentation writer (www.zyxist.com)
* Tomasz "Slump" Szczuplinski - project coordination, PR, Polish translations, etc.

The library is available under GNU Lesser General Public License. You can find the full text
in the "COPYING" file.