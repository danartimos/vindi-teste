<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Routing\Annotation\Route;

use AppBundle\Entity\Agenda;
use AppBundle\Entity\Usuario;

class DefaultController extends Controller
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $agendas = $this->getDoctrine()->getRepository(Agenda::class)->findAll();
        $eventos = [];
        foreach ($agendas as $agenda) {
            $usuario = $this->getDoctrine()->getRepository(Usuario::class)->find(
                $agenda->getUsuarioId()
            );
            
            $eventos[$agenda->getId()] = [
                'nome' => $usuario->getNome(),
                'data' => $agenda->getData().'T'.$agenda->getHora(),
            ];
        }
        $eventos[] = [
            'id' => 1,
            'nome' => 'Danilo',
            'data' => '2019-20-24T07:00',
        ];
        
        return $this->render('default/index.html.twig', [
            'eventos' => json_encode($eventos),
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
    
    /**
     * @Route("/usuario", name="usuario")
     */
    public function usuarioAction(Request $request)
    {
        //verifica o método e tenta salvar
        if ( $request->isMethod('POST') ) {
            $this->getDoctrine()->getRepository(Usuario::class)->save($request);
            
            $this->addFlash('success','Usuário cadastrado!');
        }
        
        //seleciona todos os usuarios
        $usuarios = $this->getDoctrine()->getRepository(Usuario::class)->findAll();
            
        return $this->render('default/usuario.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'usuarios' => $usuarios,
        ]);
    }
    
    /**
     * @Route("/usuario/entrar", name="usuarioEntrar")
     */
    public function usuarioEntrarAction(Request $request)
    {
        //busca usuário no banco
        if ( $request->isMethod('POST') ) {
            $dados = $request->request->all();

            $usuario = $this->getDoctrine()->getRepository(Usuario::class)->findOneBy([
                'usuario' => $dados['usuario'],
                'senha' => $dados['senha'],
            ]);

            if ($usuario) {
                $this->session->set('logado',[
                    'in' => true,
                    'id' => $usuario->getId(),
                    'nome' => $usuario->getNome(),
                    'usuario' => $usuario->getUsuario(),
                ]);
                $this->addFlash('success','Bem-vindo, ' . $usuario->getNome() . '!');
            } else {
                $this->addFlash('danger','Usuário ou senha não conferem!');
            }
        }

        return $this->redirectToRoute('homepage');
    }
    
    /**
     * @Route("/usuario/sair", name="usuarioSair")
     */
    public function usuarioSairAction(Request $request)
    {
        //busca usuário no banco
        if ( array_key_exists('in', $this->session->get('logado')) ) {
            $this->session->clear();
            $this->addFlash('success','Nos vemos em breve!');
        }

        return $this->redirectToRoute('homepage');
    }
}
