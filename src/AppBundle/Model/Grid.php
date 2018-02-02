<?php
namespace AppBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Issue;
use AppBundle\Entity\Breaks;
use AppBundle\Helper\TimeHelper;

/**
 * Grid Data Model
 *
 */
class Grid
{

    use TimeHelper;

    public function prepareDateForCsv($data)
    {

        $dataForCsv = Array();
        $dataForCsv[] = array(
            'id' => 'Issue ID',
            'description' => 'Issue description',
            'begin' => 'Time between - begin',
            'end' => 'Time between - end',
            'time' => 'Time total (excluding breakes)'
        );
        foreach ($data AS $key => $item) {

            $begin = $item->getBegin();
            if ($begin) {
                $begin = $begin->format('Y-m-d H:i:s');
            }
            $end = $item->getEnd();
            if ($end) {
                $end = $end->format('Y-m-d H:i:s');
            }

            $totaltime = $item->getTotalTime();

            if (!empty($totaltime)) {
                $time = $this->convertSecondsToDateString($totaltime);
            } else {
                $time = '';
            }

            $dataForCsv[] = array(
                'id' => $item->getId(),
                'description' => $item->getDescription(),
                'begin' => $begin,
                'end' => $end,
                'time' => $time
            );
        }

        return $dataForCsv;
    }
}
