<?php

namespace Calendar\Library\Export;

use Calendar\Library\Events\Event;
use Calendar\Library\Carbon;
use Craft\DateTime;

class ExportCalendarToIcs extends AbstractExportCalendar
{
    /** @var Carbon */
    private $now;

    /**
     * Collect events and parse them, and build a string
     * That will be exported to a file
     *
     * @return string
     */
    protected function prepareStringForExport()
    {
        $events = $this->getEvents();

        $exportString = "";
        $exportString .= "BEGIN:VCALENDAR\n";
        $exportString .= "PRODID:-//Solspace/Calendar//EN\n";
        $exportString .= "VERSION:2.0\n";
        $exportString .= "CALSCALE:GREGORIAN\n";
        $exportString .= "\n\n";

        $this->now = Carbon::now(DateTime::UTC);

        /** @var Event $event */
        foreach ($events as $event) {
            $exportString .= $this->combineExportString($event, $event->getStartDate());
            
            if ($event->getSelectDates()) {
                foreach ($event->getSelectDates() as $date) {
                    $dateCarbon = Carbon::createFromTimestampUTC($date->getTimestamp());
                    
                    $exportString .= $this->combineExportString($event, $dateCarbon);
                }
            }
        }

        $exportString .= "END:VCALENDAR\n";

        return $exportString;
    }

    /**
     * Builds a VEVENT string and returns it
     *
     * @param Event  $event
     * @param Carbon $date
     *
     * @return string
     */
    private function combineExportString(Event $event, Carbon $date)
    {
        $eventId      = $event->getId();
        $exportString = "";

        $dateDiff = $event->getStartDate()->diff($event->getEndDate());

        $startDate = $date->copy();
        $endDate   = $startDate->copy()->add($dateDiff);

        $description = null;
        $descriptionFieldHandle = $event->getCalendar()->getDescriptionFieldHandle();
        if (isset($event->{$descriptionFieldHandle})) {
            $description = $event->{$descriptionFieldHandle};
        }

        $location = null;
        $locationFieldHandle = $event->getCalendar()->getLocationFieldHandle();
        if (isset($event->{$locationFieldHandle})) {
            $location = $event->{$locationFieldHandle};
        }
        $title   = $event->getTitle();
        $uidHash = md5($eventId . $title . $description . $date->timestamp);

        $exportString .= "BEGIN:VEVENT\n";
        $exportString .= sprintf("UID:%s@solspace.com\n", $uidHash);
        $exportString .= sprintf("DTSTAMP:%s\n", $this->now->format(self::DATE_TIME_FORMAT));

        if ($description) {
            $exportString .= sprintf("DESCRIPTION:%s\n", $this->escapeString(strip_tags($description)));
        }
        if ($location) {
            $exportString .= sprintf("LOCATION:%s\n", $this->escapeString(strip_tags($location)));
        }

        if ($event->isAllDay()) {
            $exportString .= sprintf("DTSTART;VALUE=DATE:%s\n", $startDate->format(self::DATE_FORMAT));
            $exportString .= sprintf(
                "DTEND;VALUE=DATE:%s\n",
                $endDate->copy()->addDay()->format(self::DATE_FORMAT)
            );
        } else {
            $exportString .= sprintf("DTSTART:%s\n", $startDate->format(self::DATE_TIME_FORMAT));
            $exportString .= sprintf("DTEND:%s\n", $endDate->format(self::DATE_TIME_FORMAT));
        }

        $selectDates = $event->getSelectDates();
        if ($event->isRepeating() && empty($selectDates)) {
            $rrule = $event->getRRule();
            list ($dtstart, $rrule) = explode("\n", $rrule);

            $exportString .= sprintf("%s\n", $rrule);

            $exceptionDatesValues = array();
            foreach ($event->getExceptions() as $date) {
                $date = new Carbon($date, DateTime::UTC);
                if ($event->isAllDay()) {
                    $exceptionDatesValues[] = $date->format(self::DATE_FORMAT);
                } else {
                    $date->setTime($startDate->hour, $startDate->minute, $startDate->second);
                    $exceptionDatesValues[] = $date->format(self::DATE_TIME_FORMAT);
                }
            }

            $exceptionDates = implode(',', $exceptionDatesValues);
            if ($event->isAllDay()) {
                $exportString .= sprintf("EXDATE;VALUE=DATE:%s\n", $exceptionDates);
            } else {
                $exportString .= sprintf("EXDATE:%s\n", $exceptionDates);
            }
        }

        $exportString .= sprintf("SUMMARY:%s\n", $this->escapeString($title));
        $exportString .= "END:VEVENT\n";
        $exportString .= "\n\n";

        return $exportString;
    }
}
