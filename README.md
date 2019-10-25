projeto
=======

Criado projeto onde se agenda horário de uma sala.

Para iniciar o projeto é necesário rodar o Symfony

> php bin\console server:run

Além disso, é necessário dar um início na base de dados

> php bin/console doctrine:database:create

Depois de riada a base, é necessário criar as tabelas

> php bin/console doctrine:schema:validate

> php bin/console doctrine:schema:create

Após isso é só entrar no projeto e ir em Usuário para criar
um novo e dar início ao sistema.