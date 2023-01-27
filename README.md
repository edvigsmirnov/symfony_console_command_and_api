This project is Symfony-based.

It consists of two main parts:
1. Console command
2. API for retrieving database rows

For a basic setup you need to execute 'symfony composer install' command.
Then database creation is needed (either by Symfony command or manually).
After that run migrations to set up database table structure.

Application is ready.

Console command is executed by entering 'symfony console app:load-products' with fileUrl parameter in the end. 
fileUrl endpoint must be accessible and have a properly organized XML structure with a list of products.
For development purposes example file is located in app root -> storage/ folder.
Command parses file and saves each product as an Entity into database.

A list of products can be retrieved by accessing API endpoint:
/api/products?page={$pageNumber}&count={$productsNumberPerPage}

GET parameters allow pagination. By default, each of those parameters are equal to 1.

API has a Swagger documentation that can be accessed through /api/doc endpoint.

Console command and API endpoint have their own automatic tests, that are located in App\Tests namespace.
API test checks response type and structure, while console command test checks successful execution of console command.
