<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuário', $crawler->filter('.nav-link')->text());
    }
    
    public function testUsuario()
    {
        $client = static::createClient();

        //entrada
        $crawler = $client->request('GET', '/usuario');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Novo Usuário', $crawler->filter('h4')->text());
        
        //teste de inserção vazio
        $crawler = $client->request('POST', '/usuario');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuário não cadastrado', $crawler->filter('.alert-danger')->text());
        
        //teste de inserção com dados
        $params = [
            'nome' => 'Teste',
            'usuario' => 'teste',
            'senha' => 'teste'
        ];
        $crawler = $client->request('POST', '/usuario', $params);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuário cadastrado!', $crawler->filter('.alert-success')->text());
    }
    
    public function testUsuarioLogin()
    {
        $client = static::createClient();

        //teste de login errado
        $params = [
            'usuario' => 'teste1',
            'senha' => 'teste1',
        ];
        $crawler = $client->request('POST', '/usuario/entrar', $params);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Usuário ou senha não conferem', $crawler->filter('.alert-danger')->text());
        
        //teste de entrada
        $params = [
            'usuario' => 'teste',
            'senha' => 'teste'
        ];
        $crawler = $client->request('POST', '/usuario/entrar', $params);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Bem-vindo', $crawler->filter('.alert-success')->text());
        
        //teste de saida
        $crawler = $client->request('GET', '/usuario/sair');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Nos vemos em breve!', $crawler->filter('.alert-success')->text());
    }
}
