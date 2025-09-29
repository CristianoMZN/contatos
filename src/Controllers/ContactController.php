<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contact;
use App\Models\CompanyCategory;

/**
 * Contact Controller
 * Handles contact CRUD operations and display
 */
class ContactController extends Controller
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
        $page = (int) ($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? null;
        
        // Get public contacts for visitors, user contacts for authenticated users
        if ($this->session->isLoggedIn()) {
            $contacts = $this->contactModel->getUserContacts($this->session->getUserId(), $page, 12, $search);
            $viewType = 'my-contacts';
        } else {
            $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
            $contacts = $this->contactModel->getPublicContacts($page, 12, $categoryId, $search);
            $viewType = 'public-contacts';
        }
        
        $categories = $this->categoryModel->all();
        
        $this->view('contacts/index', [
            'title' => 'Contatos',
            'contacts' => $contacts,
            'categories' => $categories,
            'viewType' => $viewType,
            'currentPage' => $page,
            'search' => $search,
            'selectedCategory' => $_GET['category'] ?? null
        ]);
    }
    
    public function show(string $slug): void
    {
        $contact = $this->contactModel->findBySlug($slug);
        
        if (!$contact) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }
        
        // Check permissions
        if (!$contact['is_public'] && !$this->session->isLoggedIn()) {
            http_response_code(403);
            $this->view('errors/403');
            return;
        }
        
        if (!$contact['is_public'] && $contact['user_id'] !== $this->session->getUserId()) {
            http_response_code(403);
            $this->view('errors/403');
            return;
        }
        
        // Get related data
        $phones = $this->contactModel->getPhones($contact['id']);
        $emails = $this->contactModel->getEmails($contact['id']);
        $images = $this->contactModel->getImages($contact['id']);
        
        // SEO data for public contacts
        $seoData = null;
        if ($contact['is_public']) {
            $seoData = [
                'title' => $contact['name'] . ' - ' . ($contact['category_name'] ?? 'Contato'),
                'description' => $contact['description'] ? substr(strip_tags($contact['description']), 0, 160) : '',
                'image' => $contact['main_image'] ? '/uploads/contacts/' . $contact['main_image'] : '/assets/img/default-contact.jpg',
                'url' => '/contato/' . $contact['slug']
            ];
        }
        
        $this->view('contacts/show', [
            'title' => $contact['name'],
            'contact' => $contact,
            'phones' => $phones,
            'emails' => $emails,
            'images' => $images,
            'seoData' => $seoData,
            'canEdit' => $this->session->isLoggedIn() && $contact['user_id'] === $this->session->getUserId()
        ]);
    }
    
    public function create(): void
    {
        $this->requireAuth();
        
        $categories = $this->categoryModel->all();
        
        $this->view('contacts/create', [
            'title' => 'Novo Contato',
            'categories' => $categories,
            'csrf_token' => $this->session->generateCsrfToken(),
            'error' => $this->getError(),
            'success' => $this->getSuccess(),
            'old' => $_SESSION // For old input values
        ]);
    }
    
    public function store(): void
    {
        $this->requireAuth();
        
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF inválido');
            $this->redirect('/contacts/create');
        }
        
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? 'person';
        $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
        $description = trim($_POST['description'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $isPublic = isset($_POST['is_public']) && $_POST['is_public'] === '1';
        
        // Validation
        $errors = [];
        
        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Nome deve ter pelo menos 2 caracteres';
        }
        
        if (!in_array($type, ['person', 'company'])) {
            $errors[] = 'Tipo de contato inválido';
        }
        
        // Only companies can be public
        if ($isPublic && $type !== 'company') {
            $errors[] = 'Apenas empresas podem ser públicas';
        }
        
        // Companies should have a category
        if ($type === 'company' && !$categoryId) {
            $errors[] = 'Categoria é obrigatória para empresas';
        }
        
        if (!empty($errors)) {
            $this->withError(implode('<br>', $errors));
            $this->withOldInput();
            $this->redirect('/contacts/create');
        }
        
        try {
            // Generate slug
            $slug = $this->contactModel->generateSlug($name);
            
            // Create contact
            $contactId = $this->contactModel->create([
                'user_id' => $this->session->getUserId(),
                'type' => $type,
                'category_id' => $categoryId,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'address' => $address,
                'website' => $website,
                'is_public' => $isPublic
            ]);
            
            // Add phones
            if (!empty($_POST['phones'])) {
                foreach ($_POST['phones'] as $phoneData) {
                    if (!empty($phoneData['phone'])) {
                        $this->contactModel->addPhone(
                            $contactId,
                            $phoneData['phone'],
                            $phoneData['department'] ?? null,
                            isset($phoneData['is_whatsapp'])
                        );
                    }
                }
            }
            
            // Add emails
            if (!empty($_POST['emails'])) {
                foreach ($_POST['emails'] as $emailData) {
                    if (!empty($emailData['email']) && filter_var($emailData['email'], FILTER_VALIDATE_EMAIL)) {
                        $this->contactModel->addEmail(
                            $contactId,
                            $emailData['email'],
                            $emailData['department'] ?? null
                        );
                    }
                }
            }
            
            $this->clearOldInput();
            $this->withSuccess('Contato criado com sucesso!');
            $this->redirect("/contato/{$slug}");
            
        } catch (\Exception $e) {
            $this->withError('Erro ao criar contato. Tente novamente.');
            $this->withOldInput();
            $this->redirect('/contacts/create');
        }
    }
    
    public function edit(string $slug): void
    {
        $this->requireAuth();
        
        $contact = $this->contactModel->findBySlug($slug);
        
        if (!$contact || !$this->contactModel->belongsToUser($contact['id'], $this->session->getUserId())) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }
        
        $categories = $this->categoryModel->all();
        $phones = $this->contactModel->getPhones($contact['id']);
        $emails = $this->contactModel->getEmails($contact['id']);
        $images = $this->contactModel->getImages($contact['id']);
        
        $this->view('contacts/edit', [
            'title' => 'Editar ' . $contact['name'],
            'contact' => $contact,
            'categories' => $categories,
            'phones' => $phones,
            'emails' => $emails,
            'images' => $images,
            'csrf_token' => $this->session->generateCsrfToken(),
            'error' => $this->getError(),
            'success' => $this->getSuccess()
        ]);
    }
    
    public function update(string $slug): void
    {
        $this->requireAuth();
        
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF inválido');
            $this->redirect("/contacts/{$slug}/edit");
        }
        
        $contact = $this->contactModel->findBySlug($slug);
        
        if (!$contact || !$this->contactModel->belongsToUser($contact['id'], $this->session->getUserId())) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }
        
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? 'person';
        $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
        $description = trim($_POST['description'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $isPublic = isset($_POST['is_public']) && $_POST['is_public'] === '1';
        
        // Validation (same as create)
        $errors = [];
        
        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Nome deve ter pelo menos 2 caracteres';
        }
        
        if (!in_array($type, ['person', 'company'])) {
            $errors[] = 'Tipo de contato inválido';
        }
        
        if ($isPublic && $type !== 'company') {
            $errors[] = 'Apenas empresas podem ser públicas';
        }
        
        if ($type === 'company' && !$categoryId) {
            $errors[] = 'Categoria é obrigatória para empresas';
        }
        
        if (!empty($errors)) {
            $this->withError(implode('<br>', $errors));
            $this->redirect("/contacts/{$slug}/edit");
        }
        
        try {
            // Generate new slug if name changed
            $newSlug = $slug;
            if ($name !== $contact['name']) {
                $newSlug = $this->contactModel->generateSlug($name);
            }
            
            // Update contact
            $this->contactModel->update($contact['id'], [
                'type' => $type,
                'category_id' => $categoryId,
                'name' => $name,
                'slug' => $newSlug,
                'description' => $description,
                'address' => $address,
                'website' => $website,
                'is_public' => $isPublic
            ]);
            
            $this->withSuccess('Contato atualizado com sucesso!');
            $this->redirect("/contato/{$newSlug}");
            
        } catch (\Exception $e) {
            $this->withError('Erro ao atualizar contato. Tente novamente.');
            $this->redirect("/contacts/{$slug}/edit");
        }
    }
    
    public function destroy(string $slug): void
    {
        $this->requireAuth();
        
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF inválido');
            $this->redirect("/contato/{$slug}");
        }
        
        $contact = $this->contactModel->findBySlug($slug);
        
        if (!$contact || !$this->contactModel->belongsToUser($contact['id'], $this->session->getUserId())) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }
        
        try {
            $this->contactModel->delete($contact['id']);
            $this->withSuccess('Contato excluído com sucesso!');
            $this->redirect('/dashboard');
            
        } catch (\Exception $e) {
            $this->withError('Erro ao excluir contato. Tente novamente.');
            $this->redirect("/contato/{$slug}");
        }
    }
}