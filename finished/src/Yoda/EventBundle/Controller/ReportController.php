<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Yoda\EventBundle\Reporting\EventReportManager;

class ReportController extends Controller
{
    public function updatedEventsAction()
    {
        $manager = $this->container->get('yoda_event.reporting.event_report_manager');
        $content = $manager->getRecentlyUpdatedReport();

        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }
}