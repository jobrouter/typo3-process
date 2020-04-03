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
installation programmatically. An API and a :ref:`start command
<configuration-start-command>` are available for this use case.

Instances are started asynchronously when submitting a form and using the
:ref:`form finisher <form-finisher>` since a JobRouter installation may be
unavailable or in maintenance mode and to avoid long page loads. Let's take a
look at the flow:

.. figure:: _images/transfer-flow.png
   :alt: Transferring data sets

   Transferring data sets

As you can see from the diagram, you can prepare multiple instances. The
different instances can be started on different JobRouter installations –
depending on the configuration of the step link in the
:ref:`Process module <module>`.


Preparing The Instance Data
---------------------------

If you want to start instances programmatically in a JobRouter installation,
you can use the :php:`Preparer` class within TYPO3:

::

   <?php
   use Brotkrueml\JobRouterProcess\Domain\Model\Step;
   use Brotkrueml\JobRouterProcess\Domain\Model\Transfer;
   use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
   use Brotkrueml\JobRouterProcess\Exception\PrepareException;
   use Brotkrueml\JobRouterProcess\Transfer\Preparer;
   use TYPO3\CMS\Core\Utility\GeneralUtility;
   use TYPO3\CMS\Extbase\Object\Container\Container\ObjectManager;

   // First get the step link uid from the step handle.
   // It is advised to use the handle because the step link uid can differ from
   // development to production system (it is an auto increment).
   // If you are in an Extbase controller, the object manager is already
   // available through $this->objectManager.
   $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
   $stepRepository = $objectManager->get(StepRepository::class);
   $step = $stepRepository->findOneByHandle('your_step_handle');

   // Define the transfer domain model with your parameters
   // Have a look in the Transfer model to see the available setters
   $transfer = new Transfer();
   $transfer->setStepUid($step->getUid());
   $transfer->setSummary('My summary');
   $transfer->setProcesstable([
      'name' => 'John Doe',
      'company' => 'Acme Ltd.',
      'email_address' => 'jdoe@example.com',
      'message' => 'Please send me information.',
   ]);

   // It's important to use the makeInstance method to inject all necessary
   // dependencies
   $preparer = GeneralUtility::makeInstance(Preparer::class);
   try {
      $preparer->store($transfer);
   } catch (PrepareException $e) {
      // On errors an exception can be thrown
      var_dump($e->getMessage());
   }

The :ref:`start command <configuration-start-command>` must be activated with a
cron job to periodically start instances in the JobRouter installation(s).

Instead of the :php:`Preparer` class, you can also use the
:php:`Brotkrueml\JobRouterProcess\Domain\Repository\TransferRepository` to store
transfer records in the database.

