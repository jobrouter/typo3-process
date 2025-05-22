.. _changelog:

Changelog
=========

All notable changes to this project will be documented in this file.

The format is based on `Keep a Changelog <https://keepachangelog.com/en/1.0.0/>`_\ ,
and this project adheres to `Semantic Versioning <https://semver.org/spec/v2.0.0.html>`_.

`Unreleased <https://github.com/jobrouter/typo3-process/compare/v4.0.0...HEAD>`_
------------------------------------------------------------------------------------

Fixed
^^^^^


* Finisher preset identified by "JobRouterStartInstance" could not be found

`4.0.0 <https://github.com/jobrouter/typo3-process/compare/v3.0.1...v4.0.0>`_ - 2024-10-01
----------------------------------------------------------------------------------------------

Added
^^^^^


* Compatibility with TYPO3 v13

Changed
^^^^^^^


* Default value of "ageOfDays" option in cleanup command lowered from 30 to 7 days

Removed
^^^^^^^


* Compatibility with TYPO3 v11
* Widgets "Instance Starts" and "Instance Start Types"
* "type" option from the "JobRouterStartInstance" form finisher

`3.0.1 <https://github.com/jobrouter/typo3-process/compare/v3.0.0...v3.0.1>`_ - 2024-06-06
----------------------------------------------------------------------------------------------

Fixed
^^^^^


* Closing edit view in backend shows empty page

`3.0.0 <https://github.com/jobrouter/typo3-process/compare/v2.0.0...v3.0.0>`_ - 2024-02-21
----------------------------------------------------------------------------------------------

Changed
^^^^^^^


* Require JobRouter REST Client in version 3
* Namespace from ``Brotkrueml\JobRouterProcess`` to ``JobRouter\AddOn\Typo3Process``

`2.0.0 <https://github.com/jobrouter/typo3-process/compare/v1.2.0...v2.0.0>`_ - 2023-05-31
----------------------------------------------------------------------------------------------

Added
^^^^^


* Allow attachment and date types for an instance start (#9)
* Compatibility with TYPO3 v12

Changed
^^^^^^^


* Require JobRouter Client in version 2
* Require JobRouter Connector extension in version 2
* Require JobRouter Base extension in version 2
* Models are no longer Extbase-based and are moved to the Domain/Entity namespace
* Repositories are no longer Extbase-based

Removed
^^^^^^^


* Compatibility with TYPO3 v10 (#5)
* Compatibility with PHP 7.4 and 8.0
* Configuration of log writers in the extension configuration

`1.2.0 <https://github.com/jobrouter/typo3-process/compare/v1.1.1...v1.2.0>`_ - 2022-05-31
----------------------------------------------------------------------------------------------

Removed
^^^^^^^


* Compatibility with PHP 7.3

`1.1.1 <https://github.com/jobrouter/typo3-process/compare/v1.1.0...v1.1.1>`_ - 2022-05-20
----------------------------------------------------------------------------------------------

Fixed
^^^^^


* Database error in module DB Check > Records Statistics due to wrong label in TCA
* Handle multibyte characters correctly when cutting string to specific length for transfer

`1.1.0 <https://github.com/jobrouter/typo3-process/compare/v1.0.0...v1.1.0>`_ - 2021-11-21
----------------------------------------------------------------------------------------------

Added
^^^^^


* Compatibility with TYPO3 v11 LTS
* Possibility to refresh dashboard widgets (TYPO3 v11+ only)

Deprecated
^^^^^^^^^^


* Configuration of log writers in the extension configuration

Removed
^^^^^^^


* Compatibility with PHP 7.2

`1.0.0 <https://github.com/jobrouter/typo3-process/compare/v0.5.1...v1.0.0>`_ - 2021-03-14
----------------------------------------------------------------------------------------------

Added
^^^^^


* Show number of days for available transfers in status widget

Fixed
^^^^^


* Set crdate in transfer table correctly

`0.5.1 <https://github.com/jobrouter/typo3-process/compare/v0.5.0...v0.5.1>`_ - 2021-03-07
----------------------------------------------------------------------------------------------

Added
^^^^^


* Dashboard widget "Instance Start Errors"

Changed
^^^^^^^


* Raise minimum required version to TYPO3 10.4.11

`0.5.0 <https://github.com/jobrouter/typo3-process/compare/v0.4.1...v0.5.0>`_ - 2020-10-19
----------------------------------------------------------------------------------------------

Changed
^^^^^^^


* Identifiers of widgets
* Use log table from TYPO3 JobRouter Base extension
* Rename "transfer identifier" to "correlation id" in transfer table

Fixed
^^^^^


* Differentiate between empty string and 0 in form finisher (integer field)
* Consider maximum length of step parameters

`0.4.1 <https://github.com/jobrouter/typo3-process/compare/v0.4.0...v0.4.1>`_ - 2020-09-07
----------------------------------------------------------------------------------------------

Added
^^^^^


* Possibility to encrypt transfer data

`0.4.0 <https://github.com/jobrouter/typo3-process/compare/v0.3.0...v0.4.0>`_ - 2020-09-02
----------------------------------------------------------------------------------------------

Added
^^^^^


* Change/add description field for process/step record
* JobRouter language variable resolver
* Get form values from ResolveFinisherVariableEvent

Changed
^^^^^^^


* Rework "Instance Start Status" widget

Updated
^^^^^^^


* TYPO3 JobRouter Connector to version 0.11

Removed
^^^^^^^


* RestClientFactory is not available anymore, use from TYPO3 JobRouter connector extension instead
* Default parameters in step record

Fixed
^^^^^


* Show disabled hint in list view
* Resolve all form values in StartInstanceFinisher (also not available because of unfulfilled conditions)

`0.3.0 <https://github.com/jobrouter/typo3-process/compare/v0.2.2...v0.3.0>`_ - 2020-06-06
----------------------------------------------------------------------------------------------

Added
^^^^^


* Support for TYPO3 v10 LTS
* Prepare for upcoming major PHP versions
* Dashboard widget "Instance starts"
* Dashboard widget "Instance start status"
* Dashboard widget "Types of instance starts"
* Identify types of transfers

Changed
^^^^^^^


* Rename DeleteOldTransfersCommand to CleanUpTransfersCommand

Removed
^^^^^^^


* Support for TYPO3 v9 LTS

`0.2.2 <https://github.com/jobrouter/typo3-process/compare/v0.2.1...v0.2.2>`_ - 2020-04-03
----------------------------------------------------------------------------------------------

Added
^^^^^


* Use toggle to display process table fields in module

Changed
^^^^^^^


* Throw exception when using undefined process table field

Fixed
^^^^^


* Assign correct value to process table field when using multi checkbox

`0.2.1 <https://github.com/jobrouter/typo3-process/compare/v0.2.0...v0.2.1>`_ - 2020-04-01
----------------------------------------------------------------------------------------------

Added
^^^^^


* Type to list of process table fields in module

Fixed
^^^^^


* Correct translation for frontend context

`0.2.0 <https://github.com/jobrouter/typo3-process/compare/v0.1.1...v0.2.0>`_ - 2020-03-30
----------------------------------------------------------------------------------------------

Changed
^^^^^^^


* Simplify configuration in form finisher

Fixed
^^^^^


* Shorten strings to the maximum length of process field in form finisher

`0.1.1 <https://github.com/jobrouter/typo3-process/compare/v0.1.0...v0.1.1>`_ - 2020-03-02
----------------------------------------------------------------------------------------------

Added
^^^^^


* Language variable resolver for form finisher
* Localised label variable resolver for form finisher
* Display last run of start command in system information toolbar

`0.1.0 <https://github.com/jobrouter/typo3-process/releases/tag/v0.1.0>`_ - 2020-02-24
------------------------------------------------------------------------------------------

Initial pre-release
