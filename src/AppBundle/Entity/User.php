<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users of the system who can use chat rooms.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Doctrine\UserRepository")
 * @ORM\Table("users")
 */
class User
{
    use EntityIdTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false, unique=true)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="email_confirmed", type="boolean", nullable=false)
     */
    private $emailConfirmed = false;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email_confirm_code", type="string", nullable=true, length=32)
     */
    private $emailConfirmCode;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=255, nullable=false, unique=true)
     */
    private $nickname;

    /**
     * Used for authentication.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * To show to other users, unique.
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param string $nickname
     */
    public function setNickname(string $nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * Was email confirmed after registration?.
     *
     * @return boolean
     */
    public function isEmailConfirmed(): bool
    {
        return $this->emailConfirmed;
    }

    /**
     * @param boolean $emailConfirmed
     */
    public function setEmailConfirmed(bool $emailConfirmed)
    {
        $this->emailConfirmed = $emailConfirmed;
    }

    /**
     * Confirmation code created after registration to confirm email address.
     *
     * @return string|null
     */
    public function getEmailConfirmCode()
    {
        return $this->emailConfirmCode;
    }

    /**
     * @param string|null $emailConfirmCode
     */
    public function setEmailConfirmCode(string $emailConfirmCode = null)
    {
        $this->emailConfirmCode = $emailConfirmCode;
    }
}