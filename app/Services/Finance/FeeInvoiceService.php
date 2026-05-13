<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\StudentFeeAssignment;
use App\Models\StudentSpecialFee;
use App\Models\StudentAcademic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FeeInvoiceService
{
    public function generateFromAssignment(
        StudentFeeAssignment $assignment
    ): Fee {
        throw new \Exception("Legacy FeeInvoiceService::generateFromAssignment is deprecated. Use MonthlyInvoiceGeneratorService instead.");
    }

    public function appendSpecialFee(StudentSpecialFee $specialFee): Fee
    {
        throw new \Exception("Legacy FeeInvoiceService::appendSpecialFee is deprecated. Use MonthlyInvoiceGeneratorService instead.");
    }
}
