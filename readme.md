When I was working on the Glue-ORM module, I realised that much of its complexity could be advantageously moved down at the DBAL level.

This includes :

- type casting of strings coming from the database to the appropriate PHP types (and more complex formatting operations),
- emulating in PHP something that no RDBMS fully supports, that is, deletable / insertable / updatable views, 
- computed fields,
- aliasing of tables and columns.


Some other things that left me wanting for more with existing DBALs are :

- I think there is still room for improvement regarding query builders.
- Native prepared statements should be supported.
- IMHO a good DBAL should be based on PDO and fully expose its interface instead of hiding it behind a wrapper.

No DBAL library for PHP seems to meet all these requirements. This is my modest attempt to fix this.

Developement of Glue-ORM will cease until this module is complete.