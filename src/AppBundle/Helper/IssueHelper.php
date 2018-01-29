<?php
namespace AppBundle\Helper;
/**
 * Issue Helper
 *
 */
trait IssueHelper
{
    
     /**
     * Get Current DateTime
     * @return DateTime
     */
    public function getCurrentDateTIme()
    {
        return new DateTime(date('Y-m-d H:i:s'));
    }
    
     /**
     * 
     * @param int $time
     * @return string
     */
    protected function convertSecondsToDateString(int $time)
    {
        $dateTime = new \DateTime();
        $dateTime->setTimeStamp($time);

        return $dateTime->format('H:i:s');
    }
}
