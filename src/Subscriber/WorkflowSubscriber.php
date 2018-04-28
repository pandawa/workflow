<?php
/**
 * This file is part of the Pandawa Workflow package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Workflow\Subscriber;

use Illuminate\Contracts\Events\Dispatcher;
use Pandawa\Workflow\Event\AbstractEvent;
use Pandawa\Workflow\Event\Announced;
use Pandawa\Workflow\Event\Completed;
use Pandawa\Workflow\Event\Entered;
use Pandawa\Workflow\Event\Entering;
use Pandawa\Workflow\Event\Guard;
use Pandawa\Workflow\Event\Leaving;
use Pandawa\Workflow\Event\Transitioning;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent as SymfonyGuardEvent;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class WorkflowSubscriber implements EventSubscriberInterface
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.guard'      => ['onGuard'],
            'workflow.leave'      => ['onLeaving'],
            'workflow.transition' => ['onTransitioning'],
            'workflow.enter'      => ['onEntering'],
            'workflow.entered'    => ['onEntered'],
            'workflow.completed'  => ['onCompleted'],
            'workflow.announce'   => ['onAnnounced'],
        ];
    }

    /**
     * @param SymfonyGuardEvent $event
     */
    public function onGuard(SymfonyGuardEvent $event): void
    {
        $workflowName = $event->getWorkflowName();
        $transitionName = $event->getTransition()->getName();

        $this->dispatchForTransition(new Guard($event), 'guard', $workflowName, $transitionName);
    }

    /**
     * @param Event $event
     */
    public function onLeaving(Event $event): void
    {
        $workflowName = $event->getWorkflowName();
        $places = $event->getTransition()->getFroms();

        $this->dispatchForPlaces(new Leaving($event), 'leave', $workflowName, $places);
    }

    /**
     * @param Event $event
     */
    public function onTransitioning(Event $event): void
    {
        $workflowName = $event->getWorkflowName();
        $transitionName = $event->getTransition()->getName();

        $this->dispatchForTransition(new Transitioning($event), 'transition', $workflowName, $transitionName);
    }

    /**
     * @param Event $event
     */
    public function onEntering(Event $event): void
    {
        $workflowName = $event->getWorkflowName();
        $places = $event->getTransition()->getTos();

        $this->dispatchForPlaces(new Entering($event), 'enter', $workflowName, $places);
    }

    /**
     * @param Event $event
     */
    public function onEntered(Event $event): void
    {
        $workflowName = $event->getWorkflowName();
        $places = $event->getTransition()->getTos();

        $this->dispatchForPlaces(new Entered($event), 'entered', $workflowName, $places);
    }

    /**
     * @param Event $event
     */
    public function onCompleted(Event $event): void
    {
        $workflowName = $event->getWorkflowName();
        $transitionName = $event->getTransition()->getName();

        $this->dispatchForTransition(new Completed($event), 'completed', $workflowName, $transitionName);
    }

    /**
     * @param Event $event
     */
    public function onAnnounced(Event $event): void
    {
        $workflowName = $event->getWorkflowName();
        $transitionName = $event->getTransition()->getName();

        $this->dispatchForTransition(new Announced($event), 'announce', $workflowName, $transitionName);
    }

    /**
     * @param AbstractEvent $event
     * @param string        $eventName
     * @param string        $workflowName
     * @param string        $transitionName
     */
    private function dispatchForTransition(AbstractEvent $event, string $eventName, string $workflowName, string $transitionName): void
    {
        $this->dispatcher->dispatch($event);
        $this->dispatcher->dispatch(sprintf('workflow.%s', $eventName), $event);
        $this->dispatcher->dispatch(sprintf('workflow.%s.%s', $workflowName, $eventName), $event);
        $this->dispatcher->dispatch(sprintf('workflow.%s.%s.%s', $workflowName, $eventName, $transitionName), $event);
    }

    /**
     * @param AbstractEvent $event
     * @param string        $eventName
     * @param string        $workflowName
     * @param array         $places
     */
    private function dispatchForPlaces(AbstractEvent $event, string $eventName, string $workflowName, array $places): void
    {
        $this->dispatcher->dispatch($event);
        $this->dispatcher->dispatch(sprintf('workflow.%s', $eventName), $event);
        $this->dispatcher->dispatch(sprintf('workflow.%s.%s', $workflowName, $eventName), $event);

        foreach ($places as $place) {
            $this->dispatcher->dispatch(sprintf('workflow.%s.%s.%s', $workflowName, $eventName, $place), $event);
        }
    }
}
