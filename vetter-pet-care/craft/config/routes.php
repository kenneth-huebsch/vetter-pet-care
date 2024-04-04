<?php

/**
 * Dynamic Site Routes
 *
 * If you’d prefer to set up your site routes in a file as opposed to Settings > Routes in the CP,
 * you can define them here.  Craft will check both places for dynamic site routes.
 *
 * Each route will take up one element in the array returned by this file.
 * The array keys are your URL patterns, and the values are the templates that should get loaded.
 *
 * The URL patterns are regular expressions. If you want to capture portions of the URL and
 * make them available to your template, use named subpatterns. For example:
 *
 *     'blog/archive/(?P<year>\d{4})' => 'blog/_archive',
 *
 * That example would match URIs such as "blog/archive/2012", and pass the request along to
 * the blog/_archive template, providing it a ‘year’ variable set to the value “2012”.
 */

return array(
 'calendar/month/(?P<year>\d{4})/(?P<month>\d{2})' => 'calendar/month.html',
 'calendar/month/calendar/(?P<slug>[^\/]+)' => 'calendar/month.html',
 'calendar/month/calendar/(?P<slug>[^\/]+)/(?P<year>\d{4})/(?P<month>\d{2})' => 'calendar/month.html',
 'calendar/week/(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})' => 'calendar/week.html',
 'calendar/week/calendar/(?P<slug>[^\/]+)' => 'calendar/week.html',
 'calendar/week/calendar/(?P<slug>[^\/]+)/(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})' => 'calendar/week.html',
 'calendar/day/(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})' => 'calendar/day.html',
 'calendar/day/calendar/(?P<slug>[^\/]+)' => 'calendar/day.html',
 'calendar/day/calendar/(?P<slug>[^\/]+)/(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})' => 'calendar/day.html',
 'calendar/calendars/(?P<slug>[^\/]+)' => 'calendar/calendars.html',
 'calendar/event/(?P<id>\d+)' => 'calendar/event.html',
 'calendar/event/(?P<id>\d+)/(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})' => 'calendar/event.html',
 'calendar/events/(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})' => 'calendar/events.html',
 'calendar/export/event/(?P<id>\d+)' => 'calendar/export.html',
 'calendar/export/calendar/(?P<id>\d+)' => 'calendar/export.html',
 'calendar/fullcalendar/(?P<slug>[^\/]+)/(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})' => 'calendar/fullcalendar.html',
 'calendar/resources/event_data/(?P<id>\d+)' => 'calendar/resources/event_data.html',
);
