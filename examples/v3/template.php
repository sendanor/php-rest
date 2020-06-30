Hello World!

This is a sample template.

  EMAIL: <?= $to ?? 'Default' ?>

  GLOBAL: <?= implode(", ", get_defined_vars() ?? ['None']) ?>

--
Template.