<?php

namespace App\Services\Finance;

use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use Illuminate\Support\Facades\DB;

class FeeStructureService
{
    public function create(array $data): FeeStructure
    {
        return DB::transaction(function () use ($data) {

            $structure = FeeStructure::create([
                'school_id' => $data['school_id'],
                'academic_year_id' => $data['academic_year_id'],
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'installment_type' => $data['installment_type'],
                'due_type' => $data['due_type'],
                'due_day' => $data['due_day'] ?? null,
                'status' => true,
            ]);

            foreach ($data['items'] as $item) {

                FeeStructureItem::create([
                    'fee_structure_id' => $structure->id,
                    'fee_category_id' => $item['fee_category_id'],
                    'fine_rule_id' => $item['fine_rule_id'] ?? null,
                    'amount' => $item['amount'],
                    'due_date' => $item['due_date'] ?? null,
                    'is_optional' => $item['is_optional'] ?? false,
                    'sort_order' => $item['sort_order'] ?? 0,
                ]);
            }

            return $structure->load('items');
        });
    }
}
