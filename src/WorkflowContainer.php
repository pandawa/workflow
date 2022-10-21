<?php
declare(strict_types=1);

namespace Pandawa\Workflow;

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\Workflow\Metadata\MetadataStoreInterface;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\TransitionBlockerList;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class WorkflowContainer
{
    public function __construct(public readonly WorkflowInterface $workflow, public readonly object $subject)
    {
    }

    /**
     * @return Marking
     */
    public function getMarking(): Marking
    {
        return $this->workflow->getMarking($this->subject);
    }

    /**
     * @param string $transitionName
     *
     * @return bool
     */
    public function can(string $transitionName): bool
    {
        return $this->workflow->can($this->subject, $transitionName);
    }

    /**
     * @param string $transitionName
     *
     * @return Marking
     */
    public function apply(string $transitionName): Marking
    {
        return $this->workflow->apply($this->subject, $transitionName);
    }

    /**
     * @param string $transitionName
     *
     * @return TransitionBlockerList
     */
    public function buildTransitionBlockerList(string $transitionName): TransitionBlockerList
    {
        return $this->workflow->buildTransitionBlockerList($this->subject, $transitionName);
    }

    /**
     * @return array|Transition[]
     */
    public function getEnabledTransitions(): array
    {
        return $this->workflow->getEnabledTransitions($this->subject);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->workflow->getName();
    }

    /**
     * @return Definition
     */
    public function getDefinition(): Definition
    {
        return $this->workflow->getDefinition();
    }

    /**
     * @return MarkingStoreInterface
     */
    public function getMarkingStore(): MarkingStoreInterface
    {
        return $this->workflow->getMarkingStore();
    }

    /**
     * @return MetadataStoreInterface
     */
    public function getMetadataStore(): MetadataStoreInterface
    {
        return $this->workflow->getMetadataStore();
    }
}
