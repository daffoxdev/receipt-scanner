<?php

namespace AppBundle\Service;

use AppBundle\Service\Matchers\Interfaces\MatcherInterface;
use AppBundle\Service\Matchers\MatchesCollection;

class ReceiptDataFinder
{
    /** @var MatcherInterface[] $matchers */
    protected $matchers;

    /**
     * @param MatcherInterface $matcher
     * @return ReceiptDataFinder
     */
    public function addMatcher(MatcherInterface $matcher)
    {
        $this->matchers[] = $matcher;
        return $this;
    }

    /**
     * @param string $content
     * @return MatchesCollection
     */
    public function findMatches(string $content)
    {
        $collection = new MatchesCollection();

        /** @var MatcherInterface $matcher */
        foreach ($this->matchers as $matcher) {
            $found = $matcher->match($content);
            if (false !== $found) {
                $collection->set(get_class($matcher), $found);
            }
        }

        return $collection;
    }
}