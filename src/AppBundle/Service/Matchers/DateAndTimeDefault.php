<?php

namespace AppBundle\Service\Matchers;

use AppBundle\Service\Matchers\Interfaces\MatcherInterface;

class DateAndTimeDefault implements MatcherInterface
{
    protected $regEx = [
        ['d.m.Y H:i', '/(?<full>(\d{2})\.(\d{2})\.(\d{4})(\s)*(\d{2}):(\d{2}))/'],
        ['d.m.y H:i', '/(?<full>(\d{2})\.(\d{2})\.(\d{2})(\s)*(\d{2}):(\d{2}))/'],
        ['d-m-Y H:i:s', '/(?<full>(\d{2})\.(\d{2})\.(\d{4})(\s)*(\d{2}):(\d{2}):(\d{2}))/'],
        ['d/m/y H:i', '/(?<full>(\d{2})\/(\d{2})\/(\d{2})(\s)*(\d{2}):(\d{2}))/'],
        ['d/m/y H:i:s', '/(?<full>(\d{2})\/(\d{2})\/(\d{2})(\s)*(\d{2}):(\d{2}):(\d{2}))/'],
        ['d/m/Y H:i:s', '/(?<full>(\d{2})\/(\d{2})\/(\d{4})(\s)*(\d{2}):(\d{2}):(\d{2}))/'],
        ['j/n/Y g:i', '/(?<full>(\d{1,2})\/(\d{1,2})\/(\d{4})(\s)*(\d{1,2}):(\d{2}))/'],
    ];
    protected $dateFormat = 'd.m.Y H:i';

    /**
     * @param string $content
     * @return array|false
     */
    public function match(string $content)
    {
        $matches = [];
        $matchedRegExKey = null;
        foreach ($this->regEx as $key => $regEx) {
            $result = preg_match_all($regEx[1], $content, $matches);

            if (!empty($matches['full'])) {
                $matchedRegExKey = $key;
                break;
            }
        }

        if (empty($matches['full'])) {
            return false;
        }

        $res = [];
        foreach ($matches['full'] as $matched) {

            $dateTime = \DateTime::createFromFormat($this->regEx[$matchedRegExKey][0], $matched) ?? null;

            $res[] = [
                'data' => $matched,
                'app_data' => $dateTime,
                'name' => get_class($this)
            ];
        }

        return $res;
    }
}