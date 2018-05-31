<?php

namespace AppBundle\Service\Matchers;

use AppBundle\Service\Matchers\Interfaces\MatcherInterface;

class TotalMatcher implements MatcherInterface
{
    protected $regEx = '/(?<total>(\d{1,8})(\.|,)(\d{2}))/';

    /**
     * @param string $content
     * @return array|false
     */
    public function match(string $content)
    {
        preg_match_all($this->regEx, $content, $matches);

        if (empty($matches)) {
            return false;
        }
        if (empty($matches['total'])) {
            return false;
        }

        $value = max($matches['total']);

        $res = [
            'data' => $value,
            'app_data' => (float) $value,
            'name' => get_class($this)
        ];

        return $res;
    }
}