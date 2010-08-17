Some of the complexity of ORM modules could be advantageously moved down at the DBAL level.

This includes :

- type casting of strings coming from the database to the appropriate PHP types, and more complex formatting operations,
- emulating deletable / insertable / updatable views in PHP so that a model class can be transparently mapped to more than one table,
- computed fields, both by PHP or by the RDBMS,
- aliasing of tables and columns,
- ...maybe even validation ?

Other things where existing DBAL solutions for PHP may leave one wanting for more include :

- Query building, there is still room for improvement in making them more expressive AND more convenient to use.
- Native prepared statements support.
- PDO support, in a way that fully exposes its interface instead of hiding it behind a wrapper.

No DBAL library for PHP seems to meet all these requirements. This is a modest attempt to fix this.

Developement of Glue-ORM will cease until this module is complete.