<?php

namespace AppBundle\DataView;

use AppBundle\Service\Matchers\DateAndTimeDefault;
use AppBundle\Service\Matchers\TotalMatcher;

class ExtractedDataView
{
    public function prepareDataForView(array $matches)
    {
        $res = [];

        foreach ($matches as $key => $match) {

            switch ($key) {
                case TotalMatcher::class: {
                    $res[] = [
                        'label' => 'Summa',
                        'value' => number_format($match['app_data'], 2, '.', '')
                    ];
                    break;
                }

                case DateAndTimeDefault::class: {
                    $match = $match[0];
                    $res[] = [
                        'label' => 'Datums',
                        'value' => $match['data']
                    ];
                    break;
                }
            }

        }

        return $res;
    }
}