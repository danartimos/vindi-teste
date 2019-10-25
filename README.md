projeto
=======

Projeto onde se agenda horário de uma sala.
---

Para iniciar o projeto é necessário iniciar o servidor do Symfony

> php bin\console server:run

Além disso, é necessário criar a base de dados caso ela ainda não tenha sido criada

> php bin/console doctrine:database:create

Depois de criada a base, é necessário criar as tabelas a serem utilizadas (Agenda, Usuario)

> php bin/console doctrine:schema:create

> php bin/console doctrine:schema:validate

Após isso é só entrar no projeto e ir em 'Usuário' para criar um novo e dar início a utilização da Agenda
