<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WebauthnCredentialRepository")
 */
class WebauthnCredential
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Username;

    /**
     * @ORM\Column(type="string",  length=255)
     */
    private $credentialId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $credentialPublicKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $AAGUID;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->Username;
    }

    public function setUsername(string $Username): self
    {
        $this->Username = $Username;

        return $this;
    }

    public function getCredentialId(): ?string
    {
        return $this->credentialId;
    }

    public function setCredentialId(string $credentialId): self
    {
        $this->credentialId = $credentialId;

        return $this;
    }

    public function getCredentialPublicKey(): ?string
    {
        return $this->credentialPublicKey;
    }

    public function setCredentialPublicKey(string $credentialPublicKey): self
    {
        $this->credentialPublicKey = $credentialPublicKey;

        return $this;
    }

    public function getAAGUID(): ?string
    {
        return $this->AAGUID;
    }

    public function setAAGUID(string $AAGUID): self
    {
        $this->AAGUID = $AAGUID;

        return $this;
    }
}
