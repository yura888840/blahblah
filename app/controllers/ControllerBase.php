<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;

use Crm\Assets\Js\JsPreprocessor;

class ControllerBase extends Controller
{
    /**
     * Signalizing to controller to check/ or not permissions
     * Default is false, so check is needed by default
     * @var bool
     */
    protected $skipCheckPermissions = true;

    protected $privateResourceActionsByRole = [
        'login',
    ];

    protected $roleHasAccessToAction = [
        'login' => ['admin', 'user', 'guest'],
    ];

    /**
     * Execute before the router so we can determine if this is a private controller, and must be authenticated, or a
     * public controller that is open to all.
     *
     * @param Dispatcher $dispatcher
     * @return boolean
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        $path = $this->config->custom->assets_path;
        $this->initJs($path);
        $this->initCss($path);
        $this->setupIdentity();
        $this->assignTemplateVars();
    }

    private function multiModuleRedirect($url)
    {
        $response = new \Phalcon\Http\Response;
        $response->redirect('');
        $response->send();
    }

    protected function initJs($path)
    {
        $this->assets
            ->collection('headerJs')
            ->addJs($path . 'js/jsapi', true, false)
            ->addJs($path . 'js/jquery-1.11.2.min.js', true, false)
            ->addJs($path . 'js/jquery-ui.min.js', true, false)
            ->addJs($path . 'js/bootstrap.min.js', true, false)
            ->addJs($path . 'js/jquery.tablesorter.min.js', true, false)
            ->addJs($path . 'js/jquery.tablesorter.widgets.js', true, false)
            ->addJs($path . 'js/jquery.tablesorter.pager.js', true, false)
            ->addJs($path . 'js/jquery.cookie.js', true, false)
            ->addJs($path . 'js/bootstrap-editable.min.js', true, false)
            ->addJs($path . 'js/select2.js', true, false)
            ->addJs($path . 'js/chosen.jquery.js', true, false)
            ->addJs($path . 'js/jquery.validate.min.js', true, false)
            ->addJs($path . 'js/additional-methods.min.js', true, false)
            ->addJs($path . 'js/single-ajax-interface.js', true, false)
            ->addJs($path . 'js/custom-ajax-form.js', true, false)
            ->addJs($path . 'js/custom-ajax-tablesorter.js', true, false)
            ->addJs($path . 'js/widget-filter-formatter-jui.js', true, false)
            ->addJs($path . 'js/bootstrap3-wysihtml5.all.min.js', true, false)
            ->addJs($path . 'js/custom-ajax-widgets.js', true, false)
            ->addJs($path . 'js/custom-ajax-auto-reschanges.js', true, false)
            ->addJs($path . 'js/jq.js', true, false)
            ->addJs($path . 'js/dropzone-custom-core.js', true, false)
            ->addJs($path . 'js/dropzone-custom-dropzones-functions.js', true, false)
            ->addJs($path . 'js/init.js', true, false)
            ->addJs($path . 'js/comments.js', true, false)
            ->addJs($path . 'js/deprecated.js', true, false)
            ->addJs($path . 'js/ractive.js', true, false)
            ->addJs($path . 'js/ractive-load.js', true, false)
            ->addJs($path . 'js/date.format.js', true, false);

        // IE
        $this->assets
            ->collection('headerJsIe')
            ->addJs($path . 'js/html5shiv.js')
            ->addJs($path . 'js/respond.min.js');

        // Calendar
        $this->assets
            ->collection('headerJsCalendar')
            ->addJs($path . 'js/p-calendar.js')
            ->addJs($path . 'js/plus.js')
            ->addJs($path . 'js/client.js')
            ->addJs($path . 'js/moment-with-locales.min.js')
            ->addJs($path . 'js/fullcalendar.min.js')
            ->addJs($path . 'js/lang-all.js');

        // Dropzone
        $this->assets
            ->collection('headerJsViewTicket')
            ->addJs($path . 'js/dropzone.js');

        // ViewTicket
        $this->assets
            ->collection('JsViewTicket')
            ->addJs($path . 'js/p-ticket.js');

        // Tickets
        $this->assets
            ->collection('headerJs')
            ->addJs($path . 'js/tickets.js', true, false)
            ->addJs($path . 'js/tickets-tablesorter.js', true, false);

        $this->assets
            ->collection('headerJs')
            ->addJs($path . 'js/reconnecting-websocket.min.js', true, false)
            ->addJs($path . 'js/web-socket.js', true, false);
    }

    protected function initCss($path)
    {
        $this->assets
            ->collection('headerCss')
            ->addCss($path . 'css/bootstrap.css')
            ->addCss($path . 'css/font-awesome.min.css')
            ->addCss($path . 'css/bootstrap-editable.css')
            ->addCss($path . 'css/filter.formatter.css')
            ->addCss($path . 'css/select2.css')
            ->addCss($path . 'css/select2-bootstrap.css')
            ->addCss($path . 'css/animate.css')
            ->addCss($path . 'css/table.css')
            ->addCss($path . 'css/style.css')
            ->addCss($path . 'css/widget.css');

        // Calendar
         $this->assets
             ->collection('headerCssCalendar')
             ->addCss($path . 'css/fullcalendar.min.css');

        // Dropzone
        $this->assets
            ->collection('headerCssViewTicket')
            ->addCss($path . 'css/dropzone.css');

        //caching js and css in the browser
        if ($this->config->application->cachejscss) {
            Crm\Assets\Services\AddVersion::run($this->assets);
        }
    }

    public function setupIdentity()
    {
        $identity = $this->auth->getIdentity();
        $name = (is_array($identity) && array_key_exists('name', $identity)) ? $identity['name'] : '';

        $this->view->setVar("username", $name);
        $this->view->setVar("userGoogleCalendarId", $identity['google_calendar_id']);

        try {
            $isAdmin = $this->auth->getIsAdmin();
            $this->view->setVar('isAdmin', $isAdmin);
        } catch (\Exception $e) {
            $this->view->setVar('isAdmin', false);
        }
    }

    protected function assignTemplateVars()
    {
        $this->view->setVar('socketDomain', $this->config->webSocket->domain);
        $this->view->setVar('assets_path', $this->config->custom->assets_path);
        $this->view->setVar('assets_path_tpl', $this->config->custom->assets_path_tpl);
        $this->view->setVar('baseuri', $this->url->getBaseUri());

        $newCount = \Crm\Helpers\TicketsHelper::countNew();
        $this->view->setVar('count_new', $newCount);

        $newMsgs = \Crm\Helpers\TicketsHelper::getShortNew();
        $this->view->setVar('new_messages', $newMsgs);

        $ticket = new \Crm\Models\Tickets();
        $ticketForm = new \Crm\Forms\TicketEditForm($ticket);
        $this->view->formTicket = $ticketForm;
    }

    protected function ajaxUnauthorized()
    {
        $this->view->disable();
        header('Content-type: application/json');
        http_response_code(401);
        echo json_encode(['success' => false, 'reason' => 'Unauthorized']);
        exit();
    }

    public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
    {
        if ($this->request->isAjax() == true) {
            $this->view->disableLevel(array(
                View::LEVEL_ACTION_VIEW => true,
                View::LEVEL_LAYOUT => true,
                View::LEVEL_MAIN_LAYOUT => true,
                View::LEVEL_AFTER_TEMPLATE => true,
                View::LEVEL_BEFORE_TEMPLATE => true
            ));

            $this->response->setContentType('application/json', 'UTF-8');

            $data = $dispatcher->getReturnedValue();

            $flashSession = \Phalcon\DI::getDefault()->get('flashSession');
            if ($flashSession->has()) {
                $data['success'] = false;
                $data['messagePermissions'] = $flashSession->getMessages();
            }

            // Set global params if is not set in controller/action
            if (is_array($data)) {
                $data['success'] = isset($data['success']) ? $data['success'] : true;
                $data['message'] = isset($data['message']) ? $data['message'] : '';
                $data = json_encode($data);
            }

            $this->response->setContent($data);
        }

        return $this->response->send();
    }

}
