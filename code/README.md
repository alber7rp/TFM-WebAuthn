# Página web para la muestra del funcionamiento del estándar WebAuthn
##### La creación de esta plataforma tiene como objetivo mostrar el funcionamiento del nuevo estándar WebAuthn en un caso de uso sobre una supuesta página web. En este caso, se limitará al registro y uso de credenciales asimétricas (con tokens FIDO U2F o FIDO2), mostrándose a su vez un panel de control para administrar las claves registradas.

### Pasos para su ejecución:
- Instalar dependencias dentro del directorio /webauthn (directorio de la plataforma web con symfony)

  ```
  composer install
  ```
- Previamente se deberá tener instalado Docker en la máquina. En el directorio raíz, jecutaremos el siguiente comando para crear y levantar los contenedores necesarios (mysql y servidor de apache):
  ```
  docker-compose up -d
  ```
- Finalmente, a través de un navegador (compatible con WebAuthn) accederemos a la plataforma http://localhost:8080/.

### Tecnologías/bibliotecas empleadas:
- Docker
- Symfony 4
- Librería WebAuthn para el lado del servidor [lbuchs/WebAuthn](https://github.com/lbuchs/WebAuthn)
- OpenSSL
