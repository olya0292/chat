<?php

namespace AppBundle\Topic;

use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Message;

/**
 * Class ChatTopic
 * @package AppBundle\Topic
 */
class ChatTopic implements TopicInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * ChatTopic constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $time = new \DateTime();
        $text = "User has joined Chat";
        $history = $this->em
            ->getRepository('AppBundle:Message')
            ->findAll();

        $connection->event($topic->getId(), ['history' => $history,]);

        $this->_saveMessage($time, $text);

        $topic->broadcast(['msg' => $text,
            'created_at' => $time->format("D M d Y H:i:s"),]);
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $time = new \DateTime();
        $text = "User has left Chat";
        $this->_saveMessage($time, $text);
        $topic->broadcast([
            'msg' => $text,
            'created_at' => $time->format("D M d Y H:i:s"),
        ]);
    }

    /**
     * This will receive any Publish requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @param $event
     * @param array $exclude
     * @param array $eligible
     * @return mixed|void
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        $time = new \DateTime();
        $this->_saveMessage($time, $event);
        $topic->broadcast([
            'msg' => $event,
            'created_at' => $time->format("D M d Y H:i:s"),
        ]);
    }

    /**
     * Like RPC is will use to prefix the channel
     * @return string
     */
    public function getName()
    {
        return 'app.topic.chat';
    }

    /**
     * Save message to DB
     * @param \DateTime $time
     * @param mixed $text
     */
    protected function _saveMessage(\DateTime $time, $text)
    {
        $message = new Message();
        $message->setMessage($text);
        $message->setPostedAt($time);
        $this->em->persist($message);
        $this->em->flush();
    }
}