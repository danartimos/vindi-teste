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
                'id' => $agenda->getId(),
                'nome' => $usuario->getNome(),
                'data' => $agenda->getData()->format('Y-m-d') . 'T' . $agenda->getHora()->format('H:i'),
            ];
        }
        
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
            $dados = $request->request->all();
            if (isset($dados['nome'])) {
                $this->getDoctrine()->getRepository(Usuario::class)->salvar($request);
                $this->addFlash('success','Usuário cadastrado!');
            } else {
                $this->addFlash('danger','Usuário não cadastrado');
            }
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
        if ( array_key_exists('in', $this->session->get('logado')) ) {
            $this->session->clear();
            $this->addFlash('success','Nos vemos em breve!');
        }

        return $this->redirectToRoute('homepage');
    }
    
    /**
     * @Route("/agenda/salvar", name="agendaSalvar")
     */
    public function agendaSalvarAction(Request $request) {
        $return = [
            'status' => 'error',
            'message' => 'Não logado',
        ];
        
        if ( $request->isMethod('POST') && $this->session->get('logado')) {
            $dados = $request->request->all();
            $dados = explode('T', $dados['data']);
            
            $data = new \DateTime($dados[0]);
            $hora = new \DateTime(substr($dados[1],0,5));
            
            if ( ( $data->format('w') != '0' and $data->format('w') != '6' ) and ( $hora->format('G') >= '9' and $hora->format('H:m') < '17:30' ) ) {
                $params = [
                    'data' => $data,
                    'hora' => $hora,
                    'usuarioId' => $this->session->get('logado')['id'],
                ];
                
                $agendaExiste = $this->getDoctrine()->getRepository(Agenda::class)->validar([
                    'data' => $data,
                    'hora' => $hora,
                ]);
                
                $return['status'] = 'ok';
                if (!$agendaExiste) {
                    $agenda = $this->getDoctrine()->getRepository(Agenda::class)->salvar($params);

                    $return['message'] = [
                        'id' => $agenda->getId(),
                        'nome' => $this->session->get('logado')['nome'],
                        'data' => $agenda->getData()->format('Y-m-d') . 'T' . $agenda->getHora()->format('H:i'),
                    ];
                } else {
                    $return['message'] = 'Data sobreposta';
                }
            }
        }
        
        return new JsonResponse($return);
    }
    
    /**
     * @Route("/agenda/apagar", name="agendaApagar")
     */
    public function agendaApagarAction(Request $request) {
        $return = [
            'status' => 'error',
            'message' => 'Não logado',
        ];
        
        if ( $request->isMethod('POST') && $this->session->get('logado')) {
            $dados = $request->request->all();
            
            $agenda = $this->getDoctrine()->getRepository(Agenda::class)->find($dados['id']);
            if ($agenda && $agenda->getUsuarioId() == $this->session->get('logado')['id']) {
                $remover = $this->getDoctrine()->getRepository(Agenda::class)->remover($agenda);
                
                $return['status'] = 'ok';
                if ($remover) {
                    $return['message'] = $dados['id'];
                } else {
                    $return['message'] = '';
                }
            } else {
                $return['status'] = 'error';
                $return['message'] = 'Usuário divergente';
            }
        }
        
        return new JsonResponse($return);
    }
}
