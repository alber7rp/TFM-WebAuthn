<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use WebAuthn\WebAuthn;
use App\Entity\WebauthnCredential;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\WebauthnService;

class WebAuthnController extends AbstractController
{
    private $WebAuthn;
    private $session;
    private $formats;
    private $post;
    private $webauthnservice;

    public function __construct(SessionInterface $currentsession, WebauthnService $waservice){
      $this->session = $currentsession;
      $this->webauthnservice = $waservice;
      //$this->formats = array("android-key", "android-safetynet", "fido-u2f", "none", "packed");
      $this->formats = array("none");
      $this->WebAuthn = new WebAuthn('TFM WebAuthn', 'localhost', $this->formats);
      $this->post = trim(file_get_contents('php://input'));
      if ($this->post) {
          $this->post = json_decode($this->post);
      }
    }

    /**
     * @Route("/webauthn", name="web_authn")
     */
    public function index()
    {
        echo $this->session->get('eje');
        return $this->render('web_authn/index.html.twig', [
            'controller_name' => 'WebAuthnController',
        ]);
    }

    /**
     * @Route("/webauthn/getCreateArgs")
     */
    public function getCreateArgs()
    {
      if($this->session->get('logueado')||$this->session->get('registrado')){
        $createArgs = $this->WebAuthn->getCreateArgs($this->session->get('Username'), $this->session->get('Username'), $this->session->get('Username'), 20, false);

        // save challange to session. you have to deliver it to processGet later.
        $this->session->set('challange', $this->WebAuthn->getChallenge());

        return new Response(
              json_encode($createArgs)
          );
      }
      else{
        return new Response(
              "No estas autenticado"
          );
      }
    }

    /**
     * @Route("/webauthn/getGetArgs")
     */
    public function getGetArgs()
    {
      $ids = array();

      $credentials = $this->webauthnservice->findCredentials($this->session->get('Username'));
      foreach ($credentials as $valor) {
        array_push($ids, base64_decode($valor->getCredentialId()));
      }

      if (count($ids) === 0) {
          throw new Exception('no registrations in session.');
      }

      if (count($ids) === 0) {
          throw new Exception('no registrations in session.');
      }

      $getArgs = $this->WebAuthn->getGetArgs($ids);

      // save challange to session. you have to deliver it to processGet later.
      $this->session->set('challange', $this->WebAuthn->getChallenge());

      return new Response(
            json_encode($getArgs)
        );
    }

    /**
     * @Route("/webauthn/processCreate")
     */
    public function processCreate()
    {
      try{
      $clientDataJSON = base64_decode($this->post->clientDataJSON);
      $attestationObject = base64_decode($this->post->attestationObject);
      $challenge = $this->session->get('challange');

      $data = $this->WebAuthn->processCreate($clientDataJSON, $attestationObject, $challenge);
      $webauthncredential = new WebauthnCredential();
      $webauthncredential->setCredentialId(base64_encode($data->credentialId));
      $webauthncredential->setCredentialPublicKey($data->credentialPublicKey);
      $webauthncredential->setAAGUID($data->AAGUID);
      $webauthncredential->setUsername($this->session->get('Username'));
      $this->webauthnservice->insertCredential($webauthncredential);


      $return = new \stdClass();
      $return->success = true;
      return new Response(
        json_encode($return)
      );

      } catch (Throwable $ex) {
        $return = new \stdClass();
        $return->success = false;
        $return->msg = $ex->getMessage();
        return new Response(
          json_encode($return)
        );
      }
    }

    /**
     * @Route("/webauthn/processGet")
     */
    public function processGet()
    {
      $clientDataJSON = base64_decode($this->post->clientDataJSON);
      $authenticatorData = base64_decode($this->post->authenticatorData);
      $signature = base64_decode($this->post->signature);
      $id = $this->post->id;
      $credentialPublicKey = null;
      $challenge = $this->session->get('challange');

      $webauthncredential = $this->webauthnservice->findCredentialById($this->session->get('Username'), $this->post->id );
      if ($webauthncredential) {
        $credentialPublicKey = $webauthncredential->getCredentialPublicKey();
      }

      if ($credentialPublicKey === null) {
            throw new Exception('Public Key for credential ID not found!');
        }

      // process the get request. throws WebAuthnException if it fails
      $this->WebAuthn->processGet($clientDataJSON, $authenticatorData, $signature, $credentialPublicKey, $challenge);
      $this->session->set('logueado', 'ok');
      $return = new \stdClass();
      $return->success = true;
      return new Response(
        json_encode($return)
      );

    }
}
