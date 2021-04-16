<?php


namespace App\EventListener;


use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

class LikeNotificationSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        // returns an array of events that the subscriber is subscribed to
        return [
            Events::onFlush
        ];
        // on the flush event of the doctrine ecosystem we would
        // like to catch that new item was added to a certain collection
        // of a certain entity
        // first we subscribe on the flush because that is when
        // actually we can catch that new collection item was created
        // and then we get to create a method called onFlush
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork(); // uow keeps track of all the changes that were made to all
        // the different entities

        // getScheduledCollectionUpdates is a list of all persistent collection objects
        // or objects that actually implement the doctrine collection interface
        // and from that list we will read if it has any new elements
        // and we will check to which entity end which entity field it is actually related

        /**
         * @var PersistentCollection $collectionUpdate
         */

        foreach ( $uow->getScheduledCollectionUpdates() as $collectionUpdate)
        {
            if(!$collectionUpdate->getOwner() instanceof MicroPost){
                continue;
            }
            if('likedBy' !== $collectionUpdate->getMapping()['fieldName']){
                continue;
            }

            $insertDiff = $collectionUpdate->getInsertDiff();
            // this would be an array of elements that were added to the collection
            //

            if(!count($insertDiff)){
                return;
            }
            /**
             * @var MicroPost $microPost
             */
            $microPost = $collectionUpdate->getOwner();
            $notification = new LikeNotification();
            $notification->setUser($microPost->getUser()); // user that should be notified
            $notification->setMicroPost($microPost); // which micropost was liked
            $notification->setLikedBy(reset($insertDiff)); // who actually liked the post

            $em->persist($notification);
            $uow->computeChangeSet($em->getClassMetadata(LikeNotification::class),
                $notification);
        }
    }
}