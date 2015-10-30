<?php

namespace BasketPlanner\UserBundle\Security\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;

class OAuthUserProvider extends BaseClass
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $serviceUserId = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $serviceUserId));
        //registration
        if (null === $user) {
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
            //custom fields witch can be empty if user doesn't allow to provide them
            if($response->getRealName() != null){
                $user->setUsername($response->getRealName());
            }else{
                $user->setUsername($response->getEmail());
            }

            if (array_key_exists('first_name', $responseCustomFields)){
                $user->setFirstName($responseCustomFields['first_name']);
            }

            if (array_key_exists('last_name', $responseCustomFields)){
                $user->setLastName($responseCustomFields['last_name']);
            }

            if (array_key_exists('gender', $responseCustomFields)){
                $user->setGender($responseCustomFields['gender']);
            }

            $user->setPassword($serviceUserId);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
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