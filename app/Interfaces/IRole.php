<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 *
 */
interface IRole
{
    /**
     *
     */
    const SuperAdminRole = "Super Admin";
    /**
     *
     */
    const AdminRole = "Admin";
    /**
     *
     */
    const ProjectManagerRole = "Project Manager";
    /**
     *
     */
    const SettlementAndReconciliationRole = "Settlement And Reconciliation";
    /**
     *
     */
    const MerchantRole = "Merchant";
}
