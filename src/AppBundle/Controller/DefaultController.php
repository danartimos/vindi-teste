<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use AppBundle\Entity\Usuario;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        
        
        
        
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
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
            
            $this->addFlash(
                'notice',
                'Usuário cadastrado!'
            );
        }
        
        //seleciona todos os usuarios
        $usuarios = $this->getDoctrine()->getRepository(Usuario::class)->findAll();
        
        return $this->render('default/usuario.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'usuarios' => $usuarios,
        ]);
    }
}
