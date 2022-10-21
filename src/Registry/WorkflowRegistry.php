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

namespace Pandawa\Workflow\Registry;

use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class WorkflowRegistry implements WorkflowRegistryInterface
{
    /**
     * @var EventDispatcher
     */
    private readonly EventDispatcher $eventDispatcher;

    /**
     * @var Registry
     */
    private readonly Registry $workflowRegistry;

    /**
     * Constructor.
     *
     * @param array                    $config
     * @param EventSubscriberInterface $eventSubscriber
     */
    public function __construct(array $config, EventSubscriberInterface $eventSubscriber)
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->workflowRegistry = new Registry();

        $this->eventDispatcher->addSubscriber($eventSubscriber);

        foreach ($config as $name => $workflowConfig) {
            $this->addFromArray((string) $name, $workflowConfig);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(object $subject, string $workflowName = null): WorkflowInterface
    {
        return $this->workflowRegistry->get($subject, $workflowName);
    }

    /**
     * {@inheritdoc}
     */
    public function add(WorkflowInterface $workflow, string $supportStrategy): void
    {
        $this->workflowRegistry->addWorkflow($workflow, new InstanceOfSupportStrategy($supportStrategy));
    }

    /**
     * {@inheritdoc}
     */
    public function addFromArray(string $name, array $workflowConfig): void
    {
        $builder = new DefinitionBuilder($workflowConfig['places']);
        foreach ($workflowConfig['transitions'] as $transitionName => $transition) {
            if (!is_string($transitionName)) {
                $transitionName = $transition['name'];
            }

            foreach ((array) $transition['from'] as $from) {
                $builder->addTransition(new Transition($transitionName, $from, $transition['to']));
            }
        }

        $definition = $builder->build();
        $markingStore = $this->getMarkingStoreInstance($workflowConfig);
        $workflow = $this->getWorkflowInstance($name, $workflowConfig, $definition, $markingStore);

        foreach ($workflowConfig['supports'] as $supported) {
            if (class_exists($supported)) {
                $this->add($workflow, $supported);

                continue;
            }

            throw new RuntimeException(
                sprintf(
                    'Supported item "%s" in workflow "%s" must be a class or a resource.',
                    $supported,
                    $workflow->getName()
                )
            );
        }
    }

    /**
     * @param string                $name
     * @param array                 $workflowConfig
     * @param Definition            $definition
     * @param MarkingStoreInterface $markingStore
     *
     * @return WorkflowInterface
     */
    private function getWorkflowInstance(string $name, array $workflowConfig, Definition $definition, MarkingStoreInterface $markingStore): WorkflowInterface
    {
        if (isset($workflowConfig['class'])) {
            $className = $workflowConfig['class'];
        } else {
            if (isset($workflowConfig['type']) && $workflowConfig['type'] === 'state_machine') {
                $className = StateMachine::class;
            } else {
                $className = Workflow::class;
            }
        }

        return new $className($definition, $markingStore, $this->eventDispatcher, $name);
    }

    /**
     * @param array $workflowConfig
     *
     * @return MarkingStoreInterface
     * @throws ReflectionException
     */
    protected function getMarkingStoreInstance(array $workflowConfig): MarkingStoreInterface
    {
        $markingStoreData = $workflowConfig['marking_store'] ?? [];
        $arguments = $markingStoreData['arguments'] ?? [];

        if (isset($markingStoreData['class'])) {
            $className = $markingStoreData['class'];
        } else if (isset($markingStoreData['type']) && $markingStoreData['type'] === 'multiple_state') {
            $className = MethodMarkingStore::class;
            $arguments = [false, $arguments];
        } else {
            $className = MethodMarkingStore::class;
            $arguments = [true, Str::camel($arguments[0])];
        }

        $class = new ReflectionClass($className);

        /** @var MarkingStoreInterface $markingStore */
        $markingStore = $class->newInstanceArgs($arguments);

        return $markingStore;
    }
}
