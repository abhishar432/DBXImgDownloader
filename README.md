DBXImgDownloader
================

CLI application for downloading images (JPEG, GIF, SVG, RAW, etc.) from dropbox account. Output directory for the downloaded images is structured as <ACCOUNT_NAME>/YEAR/MONTH/DAY format.

Requirements:
- PHP 5.4.24 (cli)
- Mac/Linux
- Dropbox Account

Steps for Initial Setup:
- Install PHP.
- Open Terminal in Mac/Linux and go to the root directory for application.
- Make Main.php executable by running 'chmod +x Main.php' in the terminal.
- Modify shebang in Main.php to point to location of php installation, Default is setup up for OSX 10.9.4

Steps for running the application:
- Open Terminal in Mac/Linux and go to the root directory for application.
- Run ./Main.php
