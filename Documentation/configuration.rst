.. include:: _includes.txt

.. highlight:: shell

.. _configuration:

=============
Configuration
=============

Target group: **Administrators**

.. contents:: Table of Contents
   :depth: 2
   :local:

.. _configuration-extension:

Extension configuration
=======================

To configure the extension, go to :guilabel:`Admin Tools` > :guilabel:`Settings`
> :guilabel:`Extension Configuration` and click on the :guilabel:`Configure
extensions` button. Open the :guilabel:`jobrouter_process` configuration:

.. figure:: _images/extension-configuration.png
   :alt: Options in the extension configuration

   Options in the extension configuration

log.logIntoFile
---------------

If this option is activated, the log output is written to the file
:file:`var/log/typo3_jobrouter_process_<hash>.log` (for Composer-based
installations). It is enabled by default.

log.logIntoTable
----------------

Activate this option to log into the table `tx_jobrouterconnector_log`. It is
disabled by default.

.. hint::

   To display the log entries of this table in the TYPO3 backend, install the
   extension `co-stack/logs <https://extensions.typo3.org/extension/logs>`_.

log.logLevel
------------

Using the drop down menu you can select the log level for the activated log
options. :guilabel:`warning` is selected by default.

.. _configuration-dashboard:

Dashboard widget configuration
==============================

Instance Starts
---------------

The number of instance starts by days can be visualised by the widget
:ref:`Instance Starts <dashboard-widget-instance-starts>`. By default, 14 days
are shown. This can be overridden in the :file:`Configuration/Services.yaml`
of your site package extension (or any other dependent extension):

.. code-block:: yaml

   parameters:
      jobrouter_process.widget.transfersPerDay.numberOfDays: 14


Instance start types
--------------------

The instance starts of the last 14 days (including the current day) are taken
into account for the :ref:`Instance Start Types widget
<dashboard-widget-instance-start-types>`. This can be overridden in the
:file:`Configuration/Services.yaml` of your site package extension (or any other
dependent extension):

.. code-block:: yaml

   parameters:
      jobrouter_process.widget.typeOfInstanceStarts.numberOfDays: 14

As already mentioned, the current day is also considered. So if you use
:yaml:`1` for the number of days, the widget will only show instance starts from
today.


.. _configuration-commands:

Commands
========

Surely you want to execute the commands regularly. Simply set up cron jobs that
will execute the commands regularly, e.g. once an hour or once a day, depending
on your needs.

.. _configuration-start-command:

Starting instances
------------------

If you rely on the :ref:`form finisher <form-finisher>` or use the
:ref:`transfer table <developer-start-instance>` directly to start
instances in JobRouter installations, you have to use the start command:

::

   vendor/bin/typo3 jobrouter:process:start

In general you should receive a successful answer:

::

   [OK] 18 transfer(s) started successfully

If an error occurs, the command issues a warning:

::

   [WARNING] 4 out of 11 transfer(s) had errors on start

Other transfer starts are not affected by an error in one start. According
to your :ref:`logging configuration <configuration-extension>`, the error is
also logged.

.. note::
   Only one start command can run at a time. If the command starts while
   another is in progress, the second command is terminated and a warning
   is displayed.

The last run of the command is shown in the system information toolbar
(:guilabel:`Last Instance Start`):

.. figure:: _images/system-information.png
   :alt: System information with last run of the start command

   System information with last run of the start command


.. _configuration-cleanuptransfers-command:

Clean up transfers
------------------

After successfully starting instances from the transfer table, these transfers
are marked as successful. They may contain sensitive data and should be deleted
regularly. A command is available for this task:

::

   vendor/bin/typo3 jobrouter:process:cleanuptransfers

In general you should receive a successful answer:

::

   [OK] 42 successful transfers older than 30 days deleted

By default, successful transfer records that are older than 30 days are deleted.
You can adjust this value by adding an argument to the command:

::

   vendor/bin/typo3 jobrouter:process:cleanuptransfers 7

Now successful transfer records that are older than seven days are deleted. If
you use `0` as argument, all successful transfers are deleted.

.. important::
   Erroneous transfer entries are not deleted and must be handled manually.

.. note::
   If there were deleted successful transfer records, the number of affected
   rows is logged as *notice*, if there were none it is logged as *info*.

.. note::
   The number of days is also taken into account for the :ref:`Dashboard widgets
   <dashboard-widgets>`.
