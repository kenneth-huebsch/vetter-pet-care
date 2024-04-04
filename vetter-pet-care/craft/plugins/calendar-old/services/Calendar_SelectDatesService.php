<?php

namespace Craft;

use Calendar\Library\DateTimeUTC;
use Calendar\Library\Carbon;

class Calendar_SelectDatesService extends BaseApplicationComponent
{
    /**
     * Returns a list of Calendar_SelectDateModel's if any are found
     *
     * @param Calendar_EventModel $eventModel
     * @param \DateTime           $rangeStart
     * @param \DateTime           $rangeEnd
     *
     * @return Calendar_SelectDateModel[]
     */
    public function getSelectDatesForEvent(
        Calendar_EventModel $eventModel,
        \DateTime $rangeStart = null,
        \DateTime $rangeEnd = null
    ) {
        return $this->getSelectDatesForEventId($eventModel->id, $rangeStart, $rangeEnd);
    }

    /**
     * Returns a list of Calendar_SelectDateModel's if any are found
     *
     * @param int       $eventId
     * @param \DateTime $rangeStart
     * @param \DateTime $rangeEnd
     *
     * @return Calendar_SelectDateModel[]
     */
    public function getSelectDatesForEventId($eventId, \DateTime $rangeStart = null, \DateTime $rangeEnd = null)
    {
        if (!$eventId) {
            return array();
        }

        $conditions = array();
        $params     = array();

        if ($rangeStart) {
            $conditions[]          = 'date >= :rangeStart';
            $params[':rangeStart'] = $rangeStart->format('Y-m-d');
        }

        if ($rangeEnd) {
            $conditions[]        = 'date <= :rangeEnd';
            $params[':rangeEnd'] = $rangeEnd->format('Y-m-d');
        }

        $selectDateRecords = Calendar_SelectDateRecord::model()->findAllByAttributes(
            array('eventId' => $eventId),
            implode(' AND ', $conditions),
            $params
        );

        $selectDateModels = Calendar_SelectDateModel::populateModels($selectDateRecords);

        usort(
            $selectDateModels,
            function (Calendar_SelectDateModel $dateA, Calendar_SelectDateModel $dateB) {
                if ($dateA->date < $dateB->date) {
                    return -1;
                } elseif ($dateA->date > $dateB->date) {
                    return 1;
                }

                return 0;
            }
        );

        return $selectDateModels;
    }

    /**
     * Returns a list of date strings
     *
     * @param int       $eventId
     * @param \DateTime $rangeStart
     * @param \DateTime $rangeEnd
     *
     * @return Carbon[]
     */
    public function getSelectDatesAsCarbonsForEventId(
        $eventId,
        \DateTime $rangeStart = null,
        \DateTime $rangeEnd = null
    ) {
        $selectDates = $this->getSelectDatesForEventId($eventId, $rangeStart, $rangeEnd);

        $dateStrings = array();
        foreach ($selectDates as $date) {
            $dateStrings[] = Carbon::createFromTimestampUTC($date->date->getTimestamp());
        }

        return $dateStrings;
    }

    /**
     * @param Calendar_EventModel $event
     * @param array               $dates
     */
    public function saveDates(Calendar_EventModel $event, array $dates)
    {
        $query = craft()->db->createCommand();
        $query->delete('calendar_select_dates', array('eventId' => $event->id));

        foreach ($dates as $selectDate) {
            $selectDateRecord          = new Calendar_SelectDateRecord();
            $selectDateRecord->eventId = $event->id;
            $selectDateRecord->date    = DateTimeUTC::createFromString($selectDate);

            $selectDateRecord->save();
        }
    }

    /**
     * @param Calendar_EventModel $event
     * @param DateTime            $date
     */
    public function removeDate(Calendar_EventModel $event, DateTime $date)
    {
        $records = Calendar_SelectDateRecord::model()->findAllByAttributes(
            array(
                'eventId' => $event->id,
                'date'    => $date->format('Y-m-d H:i:s'),
            )
        );

        foreach ($records as $record) {
            $record->delete();
        }
    }
}
