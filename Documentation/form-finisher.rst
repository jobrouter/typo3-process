.. include:: _includes.rst.txt

.. _form-finisher:

=============
Form finisher
=============

Target group: **Integrators**, **Developers**

.. contents:: Table of Contents
   :depth: 3
   :local:

Configuring JobRouter®
======================

Before you can start instances successfully you have to configure your
JobRouter® installation to execute steps automatically. The :ref:`command for
starting instances <command-start>` only saves the step. To send the step you
need a JobRobot configuration and add the robot user to the Job Function of the
start step.

.. rst-class:: bignums-xxl

#. Configure JobRobot module

   If you haven't an activated JobRobot configuration you have to create a new
   user in your JobRouter® installation and configure it under
   :guilabel:`System` > :guilabel:`Configuration` > :guilabel:`Modules` >
   :guilabel:`JobRobot`. You see the following screen:

   .. figure:: _images/jobrobot-configuration.png
      :alt: JobRobot configuration

      JobRobot configuration

   After activating and saving the form the JobRobot user is available.

#. Add JobRobot user to Job Function of start step

   Then add the robot user to the Job Function of the start step. It can then
   look like this:

   .. figure:: _images/robot-in-job-function.png
      :alt: Robot user in Job Function of start step

      Robot user in Job Function of start step

.. _form-finisher-start-instances:

Start instances
===============

A form finisher `JobRouterStartInstance` is available to start a JobRouter®
process instance. After submitting a form, the form values are stored in a
transfer table. A command, hopefully executed regularly, takes these transfer
records and start a process instance. This is due the fact, that a JobRouter®
installation can temporarily not available due to maintenance or network
problems. Also the submitting of a form should be as fast as possible.

.. note::

   The finisher can only be used in the yaml form definition, not in the
   :guilabel:`Form` GUI module.


Start one instance
------------------

So, let's start with an example. The form finisher is defined in the YAML
configuration of the specific form:

.. code-block:: yaml

   finishers:
      -
         identifier: JobRouterStartInstance
         options:
            handle: 'start_website_contact'
            type: 'demo'
            summary: 'Demo Contact'
            processtable:
               name: '{preName} {lastName}'
               company: '{company}'
               email_address: '{email}'
               phone_number: '{phone}'
               message: '{message}'
               form_identifier: 'www.example.com/demo'

As you can see, you can define some options. These are:

- `handle` (required): The step handle defined in the
  :ref:`Processes module <module-create-step-link>`.

- `type`: The type can be used for statistical evaluation.

- `summary`: The summary of the instance.

- `initiator`: The initiator of the instance.

- `username`: The username the instance should be assigned to.

- `jobfunction`: The Job Function the instance should be assigned to.

- `priority`: The priority of the instance (number between 1 and 3).

- `pool`: The pool of the instance (positive number).

- `processtable`: You can map the form fields to the process table fields. As
  you can see in the example above, you define the process table field as the
  key (e.g `email_address`) and then map it with the to the form field
  identifier which is enclosed in curly brackets (e.g. `{email}`).
  You can also set a static value, combine a static value with a form field
  or map multiple form fields to a process table field.

.. note::
   Only process table fields that are configured in the :ref:`process link
   <module-create-process-link>` are possible. If a process table field
   is used that is not defined, an exception is thrown.

   Inputs that are longer than the defined process table field length are
   shortened to the maximum length.

   If the value of a form field is an array, like from a multi checkbox, the
   array is converted to a csv string and stored in the given process table
   field. The value can be reconverted to an array, e.g. in a JobRouter® rule,
   with the PHP function :php:`str_getcsv()`.


Start multiple instances
------------------------

It is also possible to start multiple instances – even on different JobRouter®
installations. Just use the array notation in :yaml:`options`:

.. code-block:: yaml

   finishers:
      -
         identifier: JobRouterStartInstance
         options:
            -
               handle: 'start_website_contact'
               summary: 'Demo Contact'
               processtable:
                  name: '{preName} {lastName}'
                  company: '{company}'
                  email_address: '{email}'
                  phone_number: '{phone}'
                  message: '{message}'
                  form_identifier: 'www.example.com/demo'
            -
               handle: 'collect_anonymous_messages'
               summary: 'Demo Contact'
               processtable:
                  ANON_MESSAGE: '{message}'
                  FROM_URL: 'https://www.example.com/demo'


.. _form-finisher-variables:

Variables
---------

You can use variables in the common parameters, such as :yaml:`summary` or
:yaml:`initiator`, and in the process table fields.

For more information have a look into the available :ref:`variable resolvers
<base:variable-resolvers>`. You can also write your :ref:`own variable
resolvers <developer-variable-resolvers>`.
