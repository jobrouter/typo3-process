.. _upgrade:

=======
Upgrade
=======

Target group: **Developers**

From version 3.0 to 4.0
=======================

Dashboard
---------

The dashboard widgets "Instance Starts" and "Instance Start Types" have been
removed. If statistics are necessary, use Matomo, Google Analytics or another
web analytics tool.

Form finisher
-------------

The "type" option has been removed from the "JobRouterStartInstance" form
finisher as it serves no purpose anymore.

Command
-------

The default value for the "ageOfDays" option in the
:ref:`command-cleanuptransfers` command has been lowered from 30 days to 7 days.
If you rely on the 30 days, and haven't used the "ageOfDays" option before, you
can set it explicitly:

.. tabs::

   .. group-tab:: Composer-based installation

      .. code-block:: bash

         vendor/bin/typo3 jobrouter:process:cleanuptransfers 30

   .. group-tab:: Legacy installation

      .. code-block:: bash

         php public/typo3/sysext/core/bin/typo3 jobrouter:process:cleanuptransfers 30


From version 2.0 to 3.0
=======================

The namespace of the JobRouter TYPO3 Process classes have changed from

.. code-block:: plaintext

   \Brotkrueml\JobRouterProcess

to

.. code-block:: plaintext

   \JobRouter\Addon\Typo3Process

The easiest way to update your code to the new namespace is to use
search/replace in your project.

The package name (used in :file:`composer.json`) has changed from
`brotkrueml/jobrouter-typo3-process` to `jobrouter/typo3-process`.

From version 1.x to version 2
=============================

Version 2 of this extension introduced some breaking changes, notably:

*  The repository classes are no longer based on Extbase. They are now using the
   connection object or the query builder provided by TYPO3 and Doctrine DBAL.

*  The Extbase model classes are gone. Instead there are now immutable entity
   classes for process, processtablefield, step and transfer under the namespace
   :php:`JobRouter\AddOn\Typo3Process\Domain\Entity`. There are also no getters
   available anymore, instead use the public properties (which are readonly).
