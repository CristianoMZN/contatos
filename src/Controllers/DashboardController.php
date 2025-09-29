<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Contact;

/**
 * Dashboard Controller
 * Handles user dashboard and account overview
 */
class DashboardController extends Controller
{
    private $userModel;
    private $contactModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->contactModel = new Contact();
    }
    
    public function index(): void
    {
        $this->requireAuth();
        
        $userId = $this->session->getUserId();
        
        // Get user contacts with pagination
        $page = (int) ($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? null;
        $contacts = $this->contactModel->getUserContacts($userId, $page, 12, $search);
        
        // Get user statistics
        $stats = $this->getUserStats($userId);
        
        $this->view('dashboard/index', [
            'title' => 'Meus Contatos - Dashboard',
            'contacts' => $contacts,
            'stats' => $stats,
            'currentPage' => $page,
            'search' => $search,
            'viewType' => 'my-contacts'
        ]);
    }
    
    private function getUserStats(int $userId): array
    {
        // Total contacts
        $totalContacts = $this->contactModel->all(['user_id' => $userId]);
        
        // Count by type
        $personalContacts = $this->contactModel->all(['user_id' => $userId, 'type' => 'person']);
        $businessContacts = $this->contactModel->all(['user_id' => $userId, 'type' => 'company']);
        
        // Count public contacts
        $publicContacts = $this->contactModel->all(['user_id' => $userId, 'is_public' => 1]);
        
        return [
            'total' => count($totalContacts),
            'personal' => count($personalContacts),
            'business' => count($businessContacts),
            'public' => count($publicContacts)
        ];
    }
}