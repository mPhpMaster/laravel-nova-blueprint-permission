<?php

namespace App\Interfaces;
/**
 *
 */
interface IRole
{/**
     *
     */
    const SuperAdminRole = "admin";

    /**
     *
     */
    const AdminRole = "admin";
    const UserRole = "user";
    /**
     *
     */
    const SupervisorRole = "supervisor";

    /**
     *
     */
    const ForemanRole = "foreman";
    /**
     *
     */
    const EmployeeRole = "employee";
}
