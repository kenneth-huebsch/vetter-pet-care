<?php

namespace Craft;

use Calendar\Library\DateHelper;
use Calendar\Library\Carbon;
use Calendar\Library\Duration\DayDuration;
use Calendar\Library\Duration\DurationInterface;
use Calendar\Library\Duration\HourDuration;
use Calendar\Library\Duration\MonthDuration;
use Calendar\Library\Duration\WeekDuration;
use Calendar\Library\Events\EventDay;
use Calendar\Library\Events\EventHour;
use Calendar\Library\Events\EventMonth;
use Calendar\Library\Events\EventWeek;

class Calendar_ViewDataService extends BaseApplicationComponent
{
    /**
     * @param array|null $attributes
     *
     * @return EventMonth
     */
    public function getMonth(array $attributes = null)
    {
        $targetDate = $this->getDateFromAttributes($attributes);
        DateHelper::updateWeekStartDate($targetDate, $this->getFirstDayFromAttributes($attributes));

        $duration  = new MonthDuration($targetDate);
        $eventList = $this->getEventList($duration, $attributes);

        $eventMonth = new EventMonth($duration, $eventList);

        return $eventMonth;
    }

    /**
     * @param array|null $attributes
     *
     * @return EventMonth
     */
    public function getWeek(array $attributes = null)
    {
        $targetDate = $this->getDateFromAttributes($attributes);
        DateHelper::updateWeekStartDate($targetDate, $this->getFirstDayFromAttributes($attributes));

        $duration     = new WeekDuration($targetDate);
        $eventList    = $this->getEventList($duration, $attributes);

        $eventWeek = new EventWeek($duration, $eventList);

        return $eventWeek;
    }

    /**
     * @param array|null $attributes
     *
     * @return EventMonth
     */
    public function getDay(array $attributes = null)
    {
        $duration  = new DayDuration($this->getDateFromAttributes($attributes));
        $eventList = $this->getEventList($duration, $attributes);

        $eventDay = new EventDay($duration, $eventList);

        return $eventDay;
    }

    /**
     * @param array|null $attributes
     *
     * @return EventHour
     */
    public function getHour(array $attributes = null)
    {
        $duration  = new HourDuration($this->getDateFromAttributes($attributes));
        $eventList = $this->getEventList($duration, $attributes);

        $eventHour = new EventHour($duration, $eventList);

        return $eventHour;
    }

    /**
     * @param DurationInterface $duration
     * @param array|null        $attributes
     *
     * @return \Calendar\Library\Events\EventList
     */
    private function getEventList(DurationInterface $duration, $attributes = null)
    {
        /** @var Calendar_EventsService $eventService */
        $eventService = craft()->calendar_events;

        $eventList = $eventService->getEventList($this->assembleAttributes($duration, $attributes));

        return $eventList;
    }

    /**
     * Gets a Carbon date instance from $attributes if it's present
     * Today's date Carbon - if not
     *
     * @param array|null $attributes
     *
     * @return Carbon
     */
    private function getDateFromAttributes(array $attributes = null)
    {
        if (is_null($attributes) || !isset($attributes['date'])) {
            $date = "now";
        } else {
            $date = $attributes['date'];
        }

        return new Carbon($date, DateTime::UTC);
    }

    /**
     * Returns the firstDayOfWeek either from attributes or
     * user preference or craft defaults
     *
     * @param array|null $attibutes
     *
     * @return int
     */
    private function getFirstDayFromAttributes(array $attibutes = null)
    {
        if (!is_null($attibutes) && isset($attibutes['firstDay'])) {
            return abs((int)$attibutes['firstDay']);
        }

        $userModel = craft()->userSession->getUser();
        if ($userModel) {
            return $userModel->weekStartDay;
        }

        return craft()->config->get('defaultWeekStartDay');
    }

    /**
     * Merges dateRangeStart and dateRangeEnd into attributes based on $duration
     *
     * @param DurationInterface $duration
     * @param array|null        $attributes
     *
     * @return array
     */
    private function assembleAttributes(DurationInterface $duration, $attributes = null)
    {
        return array_merge(
            $attributes ?: array(),
            array(
                'dateRangeStart' => $duration->getStartDate()->copy()->subWeek(),
                'dateRangeEnd'   => $duration->getEndDate()->copy()->addWeek(),
            )
        );
    }
}
