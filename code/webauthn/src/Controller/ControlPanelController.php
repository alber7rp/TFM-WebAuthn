<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\User;
use App\Entity\WebauthnCredential;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\WebauthnService;

class ControlPanelController extends AbstractController
{
  private $session;
  private $webauthnservice;

    public function __construct(SessionInterface $currentsession, WebauthnService $waservice){
      $this->session = $currentsession;
      $this->webauthnservice = $waservice;
    }
    /**
     * @Route("/", name="control_panel")
     */
    public function index()
    {
      if($this->session->get('logueado')){
        $webauthncredentials = $this->getDoctrine()->getRepository(WebauthnCredential::class)->findBy(['Username' => $this->session->get('Username')]);

        return $this->render('control_panel/controlpanel.html.twig', [
            'controller_name' => 'ControlPanelController',
            'credentials' => $webauthncredentials,
            'Username' => $this->session->get('Username')
        ]);
      }
      else{

        return $this->render('control_panel/index.html.twig', [
            'controller_name' => 'ControlPanelController'
        ]);
      }
    }


    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request)
    {
      if($this->session->get('logueado')){
        return new Response(
              "estas autenticado"
          );
      }
      else{
        $user = $this->webauthnservice->findUserByUsername($request->request->get('Username'));

        if (!$user) {
          $this->webauthnservice->insertUser($request->request->get('Username'), $request->request->get('Password'));
          $this->session->set('registrado', 'ok');
          $this->session->set('Username', $request->request->get('Username'));
          $status = array("status" => "success");
          return new Response(
                json_encode($status)
            );
          }
        else{
          $status = array("status" => "error");
          return new Response(
                json_encode($status)
            );
          }
        }
      }



    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request)
    {
      if($this->session->get('loguedado')){
        return new Response(
              "estas autenticado"
          );
      }
      else{
        $user = $this->webauthnservice->findUserByUsername($request->request->get('Username'));

        if (!$user) {
          return new Response(
                "User not found"
            );
        }
        else{
          if (password_verify($request->request->get('Password'), $user->getPassword())) {
            $this->session->set('Username', $request->request->get('Username'));
            $webauthncredential = $this->webauthnservice->findCredentials($request->request->get('Username'));
            $status = array("status" => "success");
            if (!$webauthncredential) {
              $this->session->set('logueado', 'ok');
              $status = array("status" => "success", "credentials" => "no");
            }

            return new Response(
                  json_encode($status)
              );
            }
           else {
            return new Response(
                  "NO"
              );
          }
        }

      }
    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logout(Request $request)
    {
      $this->session->invalidate();
      return $this->redirectToRoute('control_panel');
    }

    /**
     * @Route("/deletecredential/{id}", name="deletecredential")
     */
    public function deletecredential(Request $request, $id)
    {
      $this->webauthnservice->deleteCredential($this->session->get('Username'), $id);
      return $this->redirectToRoute('control_panel');
    }

}
