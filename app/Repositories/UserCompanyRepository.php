<?php

namespace App\Repositories;

use App\Models\UserCompany;

class UserCompanyRepository {
    public function getByFilter($filters) {
        $users = UserCompany::with(['user_member_order_active']);
        if (!empty($filters['title'])) {
            $users = $users->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['package_name'])) {
            $users = $users->where('package_name',  $filters['package_name']);
        }

        $users = $users->orderBy('id', 'desc');
        return $users->paginate(10);
    }
}