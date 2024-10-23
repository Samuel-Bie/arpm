<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;



namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;

class Collection
{

    // write elegant code using collections to generate the $output array.

    public function transform()
    {

        $employees = [
            ['name' => 'John', 'city' => 'Dallas'],
            ['name' => 'Jane', 'city' => 'Austin'],
            ['name' => 'Jake', 'city' => 'Dallas'],
            ['name' => 'Jill', 'city' => 'Dallas'],
        ];

        $offices = [
            ['office' => 'Dallas HQ', 'city' => 'Dallas'],
            ['office' => 'Dallas South', 'city' => 'Dallas'],
            ['office' => 'Austin Branch', 'city' => 'Austin'],
        ];

        $offices = collect($offices)->groupBy('city');
        $employees = collect($employees)->groupBy('city');



        return $offices->map(function ($value, $key) use ($employees) {
            $possibleEmployees = collect($employees[$key])
                ->pluck('name')->toArray();

            return collect($value)->groupBy('office')->map(function ($off) use ($possibleEmployees) {
                return $possibleEmployees;
            });
        })->toArray();
    }
}
