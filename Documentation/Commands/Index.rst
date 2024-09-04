.. _commands:

========
Commands
========

Target group: **Administrators**

.. contents:: Table of Contents
   :depth: 2
   :local:

Surely you want to execute the commands regularly. Simply set up cron jobs that
will execute the commands regularly, for example, once an hour or once a day,
depending on your needs.

.. _command-start:

Starting instances
------------------

If you rely on the :ref:`form finisher <form-finisher>` or use the
:ref:`transfer table <developer-start-instance>` directly to start instances in
JobRouter® installations, you have to use the start command in the project
directory:

.. tabs::

   .. group-tab:: Composer-based installation

      .. code-block:: bash

         vendor/bin/typo3 jobrouter:process:start

   .. group-tab:: Legacy installation

      .. code-block:: bash

         php public/typo3/sysext/core/bin/typo3 jobrouter:process:start

In general you should receive a successful answer:

.. code-block:: text

   [OK] 18 incident(s) started successfully

If an error occurs, the command issues a warning:

.. code-block:: text

   [WARNING] 4 out of 11 incident(s) had errors on start

Other process starts are not affected by an error in one start. According
to your :ref:`logging configuration <configuration-extension>`, the error is
also logged.

.. note::
   Only one start command can run at a time. If the command starts while
   another is in progress, the second command is terminated and a warning
   is displayed.

The last run of the command is displayed in the system information toolbar
(:guilabel:`Last Instance Start`):

.. figure:: /Images/system-information.png
   :alt: System information with last run of the start command

   System information with last run of the start command


.. _command-cleanuptransfers:

Clean up transfers
------------------

After successfully starting instances from the transfer table, these transfers
are marked as successful. They may contain sensitive data and should be deleted
regularly. A command is available for this task. Enter in the project
directory:

.. tabs::

   .. group-tab:: Composer-based installation

      .. code-block:: bash

         vendor/bin/typo3 jobrouter:process:cleanuptransfers

   .. group-tab:: Legacy installation

      .. code-block:: bash

         php public/typo3/sysext/core/bin/typo3 jobrouter:process:cleanuptransfers

In general you should receive a successful answer:

.. code-block:: text

   [OK] 42 successful transfers older than 30 days deleted

By default, successful transfer records that are older than 30 days are deleted.
You can adjust this value by adding an argument to the command:

.. tabs::

   .. group-tab:: Composer-based installation

      .. code-block:: bash

         vendor/bin/typo3 jobrouter:process:cleanuptransfers 7

   .. group-tab:: Legacy installation

      .. code-block:: bash

         php public/typo3/sysext/core/bin/typo3 jobrouter:process:cleanuptransfers 7

.. code-block:: php

   vendor/bin/typo3 jobrouter:process:cleanuptransfers 7

Now successful transfer records that are older than seven days are deleted. If
you use `0` as argument, all successful transfers are deleted.

.. important::
   Erroneous transfer entries are not deleted and must be handled manually.
