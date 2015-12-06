<?php

namespace BasketPlanner\UserBundle\Security\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Security\Core\Util\SecureRandom;
use PhpAmqpLib\Exception\AMQPTimeoutException;

class OAuthUserProvider extends BaseClass
{

    protected $container;

    public function __construct($userManager, array $properties,Container $container){
        parent::__construct($userManager, $properties);
        $this->container = $container;
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

            //welcoming email message
            $message = 'Sveiki '.$user->getFullName().', sveikiname sėkmingai prisijugus prie BasketPlanner bendruomenės.';
            $msg = array(
                'email' => $user->getEmail(),
                'subject' => 'Sveikiname užsiregistravus BasketPlanner svetainėje.',
                'message' => $message
            );
            try {
                $this->container->get('old_sound_rabbit_mq.send_email_producer')->publish(serialize($msg), 'send_email');
            }catch (AMQPTimeoutException $e){
                //nothing to do
            }
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