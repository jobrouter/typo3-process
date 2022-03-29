.. include:: _includes.rst.txt

.. highlight:: bash

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
   :alt: Log options in the extension configuration

   Log options in the extension configuration

Log
---

.. deprecated:: 1.1.0
   The configuration of logging is deprecated and will be removed in version
   2.0.0. :ref:`Configure the required log writers <configuration-logging>` in
   your site package instead.

Log into file
~~~~~~~~~~~~~

If this option is activated, the log output is written to the file
:file:`var/log/typo3_jobrouter_process_<hash>.log`. It is enabled by default.

Log into table
~~~~~~~~~~~~~~

Activate this option to log into the table `tx_jobrouterbase_log`. It is
disabled by default.

.. hint::

   To display the log entries of this table in the TYPO3 backend, install the
   extension `co-stack/logs`_.

Log level
~~~~~~~~~

Using the drop down menu you can select the log level for the activated log
options. :guilabel:`warning` is selected by default.


Encryption
----------

Encrypt transfer data
~~~~~~~~~~~~~~~~~~~~~

If activated, the fields `processtable` and `summary` are encrypted for enhanced
security in the transfer table when using the :ref:`Preparer <developer-preparer>`
class or the :ref:`JobRouterStartInstance <form-finisher-start-instances>` form
finisher.

.. tip::
   You should consider activating the encryption of the transfer data as this
   ensures confidentiality and integrity!

.. important::
   If you lose or change the encryption key (generated with the :doc:`JobRouter
   Connector extension <ext_jobrouter_connector:introduction>`), data cannot
   be decrypted by the :ref:`start process command <command-start>` anymore!


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


.. _configuration-logging:

Logging
=======

If logging is necessary to track process instance starts and possible warnings
or errors, you can set up :ref:`log writers <t3api:logging-writers>` depending
on your needs.

**Example:** To log all warnings and higher levels of this extension into a
file, add this snippet to the :file:`ext_localconf.php` file of your site
package extension:

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['LOG']['Brotkrueml']['JobRouterProcess']['writerConfiguration'][\Psr\Log\Level::WARNING] = [
      \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
         'logFileInfix' => 'jobrouter_process'
      ]
   ];

The messages are then written to the
:file:`var/log/typo3_jobrouter_process_<hash>.log` file.


.. _co-stack/logs: https://extensions.typo3.org/extension/logs
