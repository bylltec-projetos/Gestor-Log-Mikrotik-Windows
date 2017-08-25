###### Apache Friends XAMPP (Basis Package) version 1.7.3 ######

  + Apache 2.2.14 (IPV6 enabled)
  + MySQL 5.1.41 (Community Server) with PBXT engine 1.0.09-rc
  + PHP 5.3.1 (PEAR, Mail_Mime, MDB2, Zend)
  + Perl 5.10.1 (Bundle::Apache2, Apache2::Request, Bundle::Apache::ASP, Bundle::Email, Bundle::DBD::mysql, DBD::SQlite, Randy Kobes PPM)
  + XAMPP Control Version 2.5.8 (ApacheFriends Edition)
  + XAMPP CLI Bundle 1.6
  + XAMPP Port Check 1.5
  + XAMPP Security 1.1
  + SQLite 2.8.17
  + SQLite 3.6.20
  + OpenSSL 0.9.8l
  + phpMyAdmin 3.2.4
  + ADOdb v5.10
  + FPDF v1.6
  + Zend Framework 1.9.6 Minimal Package (via PEAR)
  + Mercury Mail Transport System v4.72
  + msmtp 1.4.19 (a sendmail compatible SMTP client)
  + FileZilla FTP Server 0.9.33
  + Webalizer 2.21-02 (with GeoIP lite)
  + apc 3.1.3p1 for PHP
  + eAccelerator 0.9.6-rc1 for PHP
  + Ming 0.4.3 for PHP
  + PDF with pdflib lite v7.0.4p4 for PHP
  + rar 2.0.0-dev for PHP
  + Xdebug 2.0.6-dev for PHP
  + libapreq2 v2.12 (mod_apreq2) for Apache

---------------------------------------------------------------

* System Requirements:

  + 128 MB RAM
  + 320 MB free fixed disk
  + Windows 2000, XP (Server 2003), Vista (Server 2008), 7
  + all systems 32 bit (64 bit should also work)

---------------------------------------------------------------

* QUICK INSTALLATION:

[NOTE: Unpack the package to your USB stick or a partition of your choice. It must be on the highest level like E:\ or W:\. It will build E:\xampp or W:\xampp or something like this.]

Step 1: Please start the "setup_xampp.bat" and beginning the installation. Note: XAMPP makes no entries in the windows registry or adds new system variables.

Step 2: Start Apache with the Control Panel (xampp-control.exe) or with => \xampp\apache_start.bat.
        Stop Apache with the  Control Panel (xampp-control.exe) or with => \xampp\apache_stop.bat.

Step 3: Start MySQL with the Control Panel (xampp-control.exe) or with => \xampp\mysql_start.bat.
        Stop MySQL with the Control Panel (xampp-control.exe) or with => \xampp\mysql_stop.bat.

Step 4: Start your browser and type http://127.0.0.1/ or http://localhost/. You should see our pre-made start page with certain examples and test screens.

Step 5: The root directory (main document) f¸r HTTP(S) is => \xampp\htdocs. PHP files have the extension *.php, SSI *.shtml , CGI *.cgi (e.g. also for Perl scripts), Perl *.pl and ASP *.asp

Step 6: XAMPP UNINSTALL? Simply remove the "XAMPP" directory.
        You can also use "uninstall_xampp.bat" to do this task.

---------------------------------------------------------------

* PASSWORDS:

1) MySQL:

   User: root
   Password:
   (means no password!)

2) FileZilla FTP:

   User: newuser
   Password: wampp

   User: anonymous
   Password: some@mail.net

3) Mercury:

   Postmaster: postmaster (postmaster@localhost)
   Administrator: Admin (admin@localhost)

   TestUser: newuser
   Password: wampp

4) WEBDAV:

   User: wampp
   Password: xampp

---------------------------------------------------------------

* WINDOWS SERVICES:

- \xampp\apache\apache_installservice.bat
  ===> Install Apache as service

- \xampp\apache\apache_uninstallservice.bat
  ===> Uninstall Apache as service

- \xampp\mysql\mysql_installservice.bat
  ===> Install MySQL as service

- \xampp\mysql\mysql_uninstallservice.bat
  ===> Uninstall MySQL as service

- \xampp\filezilla\filezilla_installservice.bat
  ===> Install FileZilla as service

- \xampp\filezilla\filezilla_uninstallservice.bat
  ===> Uninstall FileZilla as service

- \xampp\mercury\mercury_installservice.bat
  ===> Install Mercury as service

- \xampp\mercury\mercury_uninstallservice.bat
  ===> Uninstall Mercury as service

Or just use the "Svc" checkboxes in the Control Panel.

----------------------------------------------------------------

A matter of security (A MUST READ!)

As mentioned before, XAMPP is not meant for production use but only for developers in a development environment. The way XAMPP is configured, is to be open as possible and allowing the developer anything he/she wants.
For development environments this is great but in a production environment it could be fatal.
Here a list of missing security in XAMPP:

- The MySQL administrator (root) has no password.
- The MySQL daemon is accessible via network.
- phpMyAdmin is accessible via network.
- Examples are accessible via network.

To fix most of the security weaknesses simply call the following URI:

    http://localhost/security/

The root password for MySQL and phpMyAdmin, and also a XAMPP directory protection can being established here.

* NOTE: Some example sites can only access by the local systems, means over localhost.

---------------------------------------------------------------

* MYSQL NOTES:

MySQL starts with standard values for the username and the password. The preset username is "root", the password is "" (= no password). To access MySQL via PHP with the preset values, you'll have to use the following syntax:

    mysql_connect("localhost", "root", "");

If you want to set a password for MySQL access, please use of MySQL Admin.
To set the passwort "secret" for the user "root", type the following:

    \xampp\mysql\bin\mysqladmin.exe -u root -psecret

After changing the password you'll have to reconfigure phpMyAdmin to use the new password, otherwise it won't be able to access the databases. To do that, open the file config.inc.php in \xampp\phpmyadmin\ and edit the following lines:

    $cfg['Servers'][$i]['user']            = 'root';   // MySQL User
    $cfg['Servers'][$i]['auth_type']       = 'cookie';   // HTTP authentification

So first the 'root' password is queried by the MySQL server, before you can access phpMyAdmin.

---------------------------------------------------------------

* CPAN/PEAR:

CPAN and PEAR are preinstalled with only the basic packages. If you need additional packages,
you can use the XAMPP Shell (xampp_shell.bat) and install them with the command line tools:
- cpanp i Foo
- pear install Foo

If you don't have a VC6 compiler, you can use "ppm" instead of "cpanp", to install binary packages.

---------------------------------------------------------------

        Have a lot of fun! | Viel Spaﬂ! | Bonne Chance!
