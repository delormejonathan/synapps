# Innéair Synapps library
------
This library contains several high-level interfaces, aiming at improving application development and test. The main features are:

- Many high level object-oriented interfaces with files, O/S, system runtime, command line, and PHP runtime.
- A HTTP dedicated class encapsulating HTTP header names.
- A set of meaningful exception class ready-to-use

------
## 1. Change log
This change log references the relevant changes (bug fixes, security fixes, new features, improvements, documentation
fixes) done in the library.

Syntax for changes: _`<type of modification> [domain] <description>`_

`<type of modification>` can be one of the following:

- _NEW_: new feature.
- _IMP_: improvement of an existing functionality.
- _REF_: code refactoring (no functional changes).
- _BUG_: bug fix.
- _UPG_: dependency upgrade.

`[domain]` is the name of the updated domain/component, and is optional (brackets are mandatory).

`<description>` is a descriptive text of the modification.

#### 1.0.5 (2016-01-05)

- UPG Upgrade for PHP 7

#### 1.0.3 (2015-05-29)

- NEW Added FileInputStream class.

#### 1.0.2 (2014-10-14)

- UPG Added unit test to IO/File.
- NEW Added Util\RegexUtils class to support multi-byte string when quoting string.

#### 1.0.1 (2014-10-20)

- UPG Added PHPUnit 4.2+ dependency for automated testing.
- UPG Added phpDocumentor 2.0+ dependency.
- IMP Added many test cases to increase confidence in released packages.
- NEW Added Http\Method class which encapsulates HTTP methods definition.

#### 1.0.0 (2014-09-15)

- NEW Migration into a dedicated VCS.
- IMP Added test suite for the File class.

------
## 2. Requirements
### Software requirements
- [PHP](http://www.php.net/) 5.3+

### PHP configuration
#### Settings
    ; Even not mandatory, using UTC for PHP is highly recommended.
    date.timezone = UTC

    ; Restricted directories shall be disabled.
    ; If enabled, be sure the directories you are accessing are under one of the directories in the directive:
    ; - Each path in the 'include_path' directive
    ; - Database file of Unix command 'file': either /etc/magic or /usr/share/file/magic
    ; open_basedir =

#### Extensions
According to the list of extensions provided [here](http://php.net/manual/en/extensions.alphabetical.php):

- Calendar
- Ctype
- Date/Time
- Directories
- Error handling
- Fileinfo
- Filesystem
- Filter
- Function handling
- iconv
- Multibyte string
- Misc.
- Output control
- PCRE
- Program execution
- SPL
- Strings
- ZIP
