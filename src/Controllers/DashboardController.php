<?php

namespace Controllers;

/**
 * DashboardController
 * 
 * Obsługuje dashboard użytkownika i admina
 */
class DashboardController
{
    /**
     * Dashboard użytkownika (wymaga logowania)
     */
    public function index(): void
    {
        require_once __DIR__ . '/../../views/dashboard/user.php';
    }
    
    /**
     * Dashboard admina (wymaga roli admin)
     */
    public function admin(): void
    {
        require_once __DIR__ . '/../../views/dashboard/admin.php';
    }
}
