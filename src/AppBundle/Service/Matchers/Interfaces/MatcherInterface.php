<?php

namespace AppBundle\Service\Matchers\Interfaces;

interface MatcherInterface
{
    /**
     * @param string $content
     * @return array|false
     */
    public function match(string $content);
}