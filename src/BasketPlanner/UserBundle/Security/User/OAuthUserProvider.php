<?php

namespace BasketPlanner\UserBundle\Security\User;

use BasketPlanner\UserBundle\Events\RegistrationEvent;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Util\SecureRandom;

class OAuthUserProvider extends BaseClass
{
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function __construct($userManager, array $properties, $dispatcher){
        parent::__construct($userManager, $properties);
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $serviceUserId = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $serviceUserId));

        //registration
        if (null === $user) {
            //check if user with service email exist in database
            if($this->userManager->findUserByEmail($response->getEmail())){
                $message = 'Vartotojas su nurodytu el.pašto adresu jau egzistuoja.';
                throw new \Symfony\Component\Security\Core\Exception\AuthenticationException($message);
            }

            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';
            //create new user
            $user = $this->userManager->createUser();
            $user->$setter_id($serviceUserId);
            $user->$setter_token($response->getAccessToken());
            //get response as array
            $responseCustomFields = $response->getResponse();
            //fill user info
            $user->setEmail($response->getEmail());
            $user->setProfilePicture($response->getProfilePicture());
            //custom fields witch can be empty if user doesn't allow to provide them
            if($response->getRealName() !== null){
                $user->setUsername($serviceUserId);
            }else{
                $user->setUsername($response->getEmail());
            }

            switch($service){
                case "facebook":
                    if (array_key_exists('first_name', $responseCustomFields)){
                        $user->setFirstName($responseCustomFields['first_name']);
                    }

                    if (array_key_exists('last_name', $responseCustomFields)){
                        $user->setLastName($responseCustomFields['last_name']);
                    }
                    break;
                case "google":
                    if (array_key_exists('given_name', $responseCustomFields)){
                        $user->setFirstName($responseCustomFields['given_name']);
                    }

                    if (array_key_exists('family_name', $responseCustomFields)){
                        $user->setLastName($responseCustomFields['family_name']);
                    }
                    break;
            }

            if (array_key_exists('gender', $responseCustomFields)){
                $user->setGender($responseCustomFields['gender']);
            }

            //generate random password
            $passwordGenerator = new SecureRandom();
            $random = $passwordGenerator->nextBytes(16);
            $passwordString= bin2hex($random);
            $password = substr($passwordString,0,10);

            $user->setPlainPassword($password);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);

            $this->dispatcher->dispatch('registration.event', new RegistrationEvent($user));

            return $user;
        }
        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        $user->$setter($response->getAccessToken());

        return $user;
    }
}