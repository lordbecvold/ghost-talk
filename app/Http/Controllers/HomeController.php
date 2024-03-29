<?php

namespace App\Http\Controllers;

use App\Http\Controller;
use App\Utils\SecurityUtil;
use App\Managers\UserManager;
use App\Managers\ConnectionManager;
use Illuminate\Contracts\View\View;

/**
 * Class HomeController
 *
 * Controller handling the home page and related functionality.
 *
 * @package App\Http\Controllers
 */
class HomeController extends Controller
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
     * The ConnectionManager instance for managing user connections.
     *
     * @var ConnectionManager
     */
    private ConnectionManager $connectionManager;

    /**
     * HomeController constructor.
     *
     * @param UserManager $userManager
     * @param SecurityUtil $securityUtil
     * @param ConnectionManager $connectionManager
     */
    public function __construct(UserManager $userManager, SecurityUtil $securityUtil, ConnectionManager $connectionManager)
    {
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
        $this->connectionManager = $connectionManager;
    }

    /**
     * Display the home page.
     *
     * @return View
     */
    public function homePage(): View
    {
        // get login state
        $is_loggedin = $this->userManager->isLoggedin();
        
        // check if user logged in
        if ($is_loggedin == true) {
               
            // get username
            $username = $this->userManager->getLoggedUsername();
            
            // get count of pending connections
            $pending_connections = $this->connectionManager->getPendingCount($username);

            // get connection list
            $connections = $this->connectionManager->getConnections($username, 'active');

            // get chat id (from query strings)
            $chat_id = request('chat');

            // escape chat id
            if ($chat_id != null) {
                $chat_id = $this->securityUtil->escapeString($chat_id);
            } else {
                $chat_id = $this->connectionManager->getFirstChatIdForLoggedUser();
            }

            // check if chat is accessable by logged user
            if ($chat_id != null) {
                if (!$this->connectionManager->isChatAccessable($chat_id)) {
                    return view('error/error-403');
                } else {
                    $chat_user = $this->connectionManager->getConnectionChatUser($chat_id);
                }
            } else {
                $chat_user = null;
            }

            // return main chat box (main component for logged-in users)
            return view('components/main-box', [
                'is_loggedin' => $is_loggedin,
                'username' => $username,

                'pending_connections' => $pending_connections,
                'connections' => $connections,
                'chat_username' => $chat_user,
                'chat_id' => $chat_id
            ]);
        } else {

            // return non main component (for non logged-in users)
            return view('components/home', [
                'is_loggedin' => $is_loggedin
            ]);
        }
    }
}
