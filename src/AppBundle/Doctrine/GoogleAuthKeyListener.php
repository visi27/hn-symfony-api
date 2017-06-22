<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Doctrine;

use AppBundle\Entity\User;
use AppBundle\Security\Encryption\EncryptionService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class GoogleAuthKeyListener implements EventSubscriber
{
    /**
     * @var EncryptionService
     */
    private $encryptionService;

    /**
     * GoogleAuthKeyListener constructor.
     *
     * @param EncryptionService $encryptionService
     */
    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return;
        }

        $this->encryptAuthKey($entity);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return;
        }

        $this->encryptAuthKey($entity);

        // necessary to force the update to see the change
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return;
        }
    }

    /**
     * @param User $entity
     */
    private function encryptAuthKey(User $entity)
    {
        if (!$entity->getPlainGoogleAuthenticatorCode()) {
            return;
        }

        $encrypted = $this->encryptionService->encrypt(
            $entity->getPlainGoogleAuthenticatorCode()
        );
        $entity->setGoogleAuthenticatorCode($encrypted);
    }
}
