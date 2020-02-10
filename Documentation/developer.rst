.. include:: _includes.txt

.. _developer:

================
Developer Corner
================

Target group: **Developers**


.. _developer-start-instance:

Start Instances
===============

Sometimes it is necessary to start instances in a JobRouter
installation programmatically. An API and a :ref:`transmit command
<configuration-transmit-command>` are available for this use case.

Instances are started asynchronously, since a JobRouter installation may be
unavailable or in maintenance mode and to avoid long page loads. Let's take a
look at the flow:

.. figure:: _images/transfer-flow.png
   :alt: Transferring data sets

   Transferring data sets

As you can see from the diagram, you can prepare multiple instances. The
different instances can be transmitted to different JobRouter installations –
depending on the configuration of the step link in the
:ref:`Process module <module>`.


Preparing The Instance Data
---------------------------

If you want to start instances programmatically in a JobRouter installation,
you can use the :php:`Preparer` class within TYPO3:

::

   <?php
   use Brotkrueml\JobRouterProcess\Exception\PrepareException;
   use Brotkrueml\JobRouterProcess\Transfer\Preparer;
   use TYPO3\CMS\Core\Utility\GeneralUtility;

   // It's important to use the makeInstance method to inject all necessary
   // dependencies
   $preparer = GeneralUtility::makeInstance(Preparer::class);
   try {

      $preparer->store(
          // The step link uid
         1,
         // Some descriptive identifier for the source of the instance
         'some identifier',
         // Your JSON encoded daa
         '{"initiator":"some initiator","summary":"some summary","jobfunction":"sales","processtable":{"name":"John Doe"}}'
      );
   } catch (PrepareException $e) {
      // In some rare cases an exception can be thrown
      var_dump($e->getMessage());
   }

The :ref:`transmit command <configuration-transmit-command>` must be activated
with a cron job to periodically start instances in the JobRouter
installation(s).

.. important::

   Do not insert the data sets directly into the transfer table, as the table
   schema can be changed without notice. Use the API described above.
