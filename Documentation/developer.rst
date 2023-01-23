.. include:: _includes.rst.txt

.. _developer:

================
Developer corner
================

Target group: **Developers**

.. contents:: Table of Contents
   :depth: 3
   :local:

.. _developer-start-instance:

Start instances
===============

Sometimes it is necessary to start instances in a JobRouter®
installation programmatically. An API and a :ref:`start command
<command-start>` are available for this use case.

Instances are started asynchronously when submitting a form and using the
:ref:`form finisher <form-finisher>` since a JobRouter® installation may be
unavailable or in maintenance mode and to avoid long page loads. Let's take a
look at the flow:

.. figure:: _images/transfer-flow.png
   :alt: Transferring data sets

   Transferring data sets

As you can see from the diagram, you can prepare multiple instances. The
different instances can be started on different JobRouter® installations –
depending on the configuration of the step link in the
:ref:`Process module <module>`.

.. _developer-preparer:

Preparing the instance data
---------------------------

If you want to start instances programmatically in a JobRouter® installation,
you can use the :php:`Preparer` class within TYPO3, for example in an Extbase
controller:

.. code-block:: php
   :caption: EXT:my_extension/Controller/MyController.php

   <?php
   declare(strict_types=1);

   namespace MyVendor\MyExtension\Controller;

   use Brotkrueml\JobRouterProcess\Domain\Dto\Transfer;
   use Brotkrueml\JobRouterProcess\Domain\Repository\StepRepository;
   use Brotkrueml\JobRouterProcess\Exception\PrepareException;
   use Brotkrueml\JobRouterProcess\Transfer\Preparer;
   use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

   final class MyController extends ActionController
   {
      public function __construct(
         private readonly Preparer $preparer,
         private readonly StepRepository $stepRepository,
      ) {
      }

      public function myAction()
      {
         // ... some other code

         // First get the step link uid from the step handle.
         // It is advised to use the handle because the step link uid can differ
         // from development to production system (it is an auto increment).
         $step = $this->stepRepository->findByHandle('your_step_handle');

         // Define the transfer DTO with your parameters
         // Have a look in the Domain\Dto\Transfer class to see the available setters
         $transfer = new Transfer(time(), $step->getUid(), 'my-correlation-id);
         $transfer->setType('Demo');
         $transfer->setSummary('My summary');
         $transfer->setProcesstable(
            \json_encode([
               'name' => 'John Doe',
               'company' => 'Acme Ltd.',
               'email_address' => 'jdoe@example.com',
               'message' => 'Please send me information.',
            ])
         );

         try {
            $this->preparer->store($transfer);
         } catch (PrepareException $e) {
            // On errors an exception can be thrown
            var_dump($e->getMessage());
         }

The :ref:`start command <command-start>` must be activated with a
cron job to periodically start instances in the JobRouter® installation(s).
