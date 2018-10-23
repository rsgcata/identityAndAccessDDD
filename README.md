# Identity and access domain model

This is a sample domain model for identity and access written in PHP.  
This domain solves the general requirements of logging in a user, logging him out, creating a profile, changing his profile information, etc..  
This is a pure DDD (domain driven design) approach to implementing this bounded context.  
If you are used to the command and query segregation principle then you will see that the aggregates are mostly intended to be used by the "command" layer.  
The aggregates are designed to be reconstituted manually, so it does not matter what kind of storage you use to persist them. You can adapt the aggregates to be used by an ORM in such a way that you won't have to deal with persistence and you can even create full aggregate references/sql relationships so you can reuse the models in the query layer.  
If you want to use this code you will have to adapt it to your needs as some parent abstract classes are not available in this repository. 