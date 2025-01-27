<?php
/**
 * Calendar for Craft
 *
 * @package       Solspace:Calendar
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2010-2018, Solspace, Inc.
 * @link          https://solspace.com/craft/calendar
 * @license       https://solspace.com/software/license-agreement
 */

namespace Calendar\Library\Duration;

use Calendar\Library\DateHelper;
use Calendar\Library\Carbon;

class WeekDuration extends AbstractDuration
{
    /**
     * @param Carbon $targetDate
     */
    protected function init(Carbon $targetDate)
    {
        $startDate = Carbon::createFromTimestampUTC($targetDate->getTimestamp());
        $startDate->startOfWeek();

        $endDate = $startDate->copy();
        $endDate->endOfWeek();
        
        $this->startDate         = $startDate;
        $this->endDate           = $endDate;
    }
}
