# chat
Test task Anonymous chat on Symfony 3 with GOS WebsocketBundle

To install project you need:

1. Make Composer install the project's dependencies into vendor/ with command composer install
2. When Composer will ask database_name enter name "chat"
3. Create DB with command php bin/console doctrine:database:create
4. Update Schema with command php bin/console doctrine:schema:update --force

Run web socket server with command php bin/console gos:websocket:server

Run Symfony server php bin/console server:run