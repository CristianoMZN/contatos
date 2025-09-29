<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contact;
use App\Models\CompanyCategory;

/**
 * Home Controller
 * Handles the main homepage and public features
 */
class HomeController extends Controller
{
    private $contactModel;
    private $categoryModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->contactModel = new Contact();
        $this->categoryModel = new CompanyCategory();
    }
    
    public function index(): void
    {
        // Get recent public contacts
        $recentContacts = $this->contactModel->getPublicContacts(1, 8);
        
        // Get categories with contact count
        $categories = $this->categoryModel->getWithContactCount();
        
        $this->view('home/index', [
            'title' => 'Contatos - Sistema de Agenda Moderna',
            'recentContacts' => $recentContacts,
            'categories' => $categories,
            'seoData' => [
                'title' => 'Sistema de Contatos - Gerencie sua agenda de forma moderna',
                'description' => 'Sistema completo para gerenciar contatos pessoais e comerciais. Interface moderna, segura e responsiva.',
                'image' => '/assets/img/og-image.jpg',
                'url' => '/'
            ]
        ]);
    }
}