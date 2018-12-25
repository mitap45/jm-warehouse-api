# Warehouse API

This project is sample warehouse api project. 
This project developed using Symfony **v4.2** , php **v7.2** , mariadb **10.1**.
With using this api you can do the operations below

- Login: login with username and password and get token 
- Give Order
- Update Order
- Get Shipping Status 
- Get Delivery Status
- Cancel Order

> Default username and passwords can be found in SampleUsers.txt file
> All sample requests and responses can be found in WarehouseApi.postman_collection.json and SampleRequests.txt file

# Installation

To start this project you need web server, php and mysql. 
You need to put this project to your web servers root folder.
You can change .env file to specify your database credentials.
Also you need to have composer installed on your system to install 3. party libraries.
Finally you need to run commands below to start the project

```composer install // to install dependencies```  
```php bin/console doctrine:database:create // to create db based on connection string in the .env file```  
```php bin/console doctrine:migrations:migrate // to update the db with latest migration```  
```php bin/console doctrine:fixtures:load // to load pre-defined data to database ```  
```you can check src/DataFixtures/AppFixtures.php for details```  

Now you are ready to make requests to api.

# Additional Info
- Doctrine Fixtures Bundle **v3.1** used for loading pre defined data to database  
- Ecommerce and Cargo companies stored as User in the system  
- Sample products added to database when loading fixtures
- Order statuses are stored in order_status table and 
latest status is the current status for that order
