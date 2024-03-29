<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Utils\SecurityUtil;
use App\Managers\UserManager;
use App\Managers\ErrorManager;
use App\Managers\ConnectionManager;

/**
 * Class ContactAddController
 *
 * Controller handling the addition of contacts.
 *
 * @package App\Http\Controllers
 */
class ContactAddController extends Controller
{
    /**
     * The UserManager instance for managing user-related operations.
     *
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * The SecurityUtil instance for handling security-related tasks.
     *
     * @var SecurityUtil
     */
    private SecurityUtil $securityUtil;

    /**
     * The ErrorManager instance for handling errors.
     *
     * @var ErrorManager
     */
    private ErrorManager $errorManager;

    /**
     * The ConnectionManager instance for managing user connections.
     *
     * @var ConnectionManager
     */
    private ConnectionManager $connectionManager;

    /**
     * ContactAddController constructor.
     *
     * @param UserManager $userManager
     * @param SecurityUtil $securityUtil
     * @param ErrorManager $errorManager
     * @param ConnectionManager $connectionManager
     */
    public function __construct(UserManager $userManager, SecurityUtil $securityUtil, ErrorManager $errorManager, ConnectionManager $connectionManager)
    {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->connectionManager = $connectionManager;
    }
        
    /**
     * Add a contact.
     *
     * @return mixed
     */
    public function contactAdd(): mixed
    {
        if ($this->userManager->isLoggedin()) {
            
            // get current logged username
            $logged_username = $this->userManager->getLoggedUsername();
            
            // get username form url (query)
            $username = request('name');
            
            // check if username seted
            if ($username == null) {
                $this->errorManager->handleError('name get (query string) parameter is null', 400);
            } 

            // escape username
            $username = $this->securityUtil->escapeString($username);

            // check if username is not self user
            if ($logged_username == $username) {
                return view('error/error-custom', ['error_msg' => "You can't add yourself"]);
            }

            // check if user exist
            if (!$this->userManager->isUserExist($username)) {
                $this->errorManager->handleError($username.' not exist in database', 400);
            }
            
            // get connecton status
            $connection_status = $this->connectionManager->getConnectionStatus($logged_username, $username);

            // check status
            if ($connection_status == null) {

                // add contact to status
                $this->connectionManager->addContact($username);
                return redirect('/profile?name='.$username);
            } else {
                return view('error/error-custom', ['error_msg' => 'This connection status is already '.$connection_status]);
            }
        } else {
            return view('error/error-403');
        }
    }
}
