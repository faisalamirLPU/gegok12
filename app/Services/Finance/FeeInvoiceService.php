<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\StudentFeeAssignment;
use Illuminate\Support\Facades\DB;

class FeeInvoiceService
{
    public function generateFromAssignment(
        StudentFeeAssignment $assignment
    ): Fee {

        return DB::transaction(function () use ($assignment) {

            /*
            |----------------------------------------------------------------------
            | Load Fee Structure
            |----------------------------------------------------------------------
            */

            $structure = FeeStructure::with('items.feeCategory')

                ->findOrFail($assignment->fee_id);

            /*
            |----------------------------------------------------------------------
            | Generate Invoice Number
            |----------------------------------------------------------------------
            */

            $invoiceNo = 'INV-' . now()->format('Ymd') . '-' . rand(1000, 9999);

            /*
            |----------------------------------------------------------------------
            | Create Fee Invoice
            |----------------------------------------------------------------------
            */

            $fee = Fee::create([

                'school_id' => $assignment->school_id,

                'academic_year_id' => $assignment->academic_year_id,

                'student_fee_assignment_id' => $assignment->id,

                'user_id' => $assignment->user_id,

                'invoice_no' => $invoiceNo,

                'total_amount' => $assignment->assigned_amount,

                'paid_amount' => 0,

                'balance' => $assignment->assigned_amount,

                'due_date' => $assignment->due_date,

                'generated_on' => now(),

                'status' => 0,
            ]);

            /*
            |----------------------------------------------------------------------
            | Create Invoice Items
            |----------------------------------------------------------------------
            */

            foreach ($structure->items as $item) {

                FeeItem::create([

                    'fee_id' => $fee->id,

                    'fee_category_id' => $item->fee_category_id,

                    'amount' => $item->amount,

                    'fine_amount' => 0,

                    'total' => $item->amount,
                ]);
            }

            return $fee;
        });
    }
}
