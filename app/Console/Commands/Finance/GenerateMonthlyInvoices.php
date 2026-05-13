<?php

namespace App\Console\Commands\Finance;

use App\Helpers\SiteHelper;
use App\Models\School;
use App\Services\Finance\MonthlyInvoiceGeneratorService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'finance:generate-invoices
                            {--month= : Month number (1-12), defaults to current month}
                            {--year= : Year, defaults to current year}
                            {--school= : Specific school ID to generate invoices for}
                            {--dry-run : Show what would be generated without creating invoices}';

    protected $description = 'Generate monthly fee invoices for all students or a specific school';

    protected MonthlyInvoiceGeneratorService $invoiceGenerator;

    public function __construct(MonthlyInvoiceGeneratorService $invoiceGenerator)
    {
        parent::__construct();
        $this->invoiceGenerator = $invoiceGenerator;
    }

    public function handle(): int
    {
        $month = (int) ($this->option('month') ?? now()->month);
        $year = (int) ($this->option('year') ?? now()->year);
        $schoolId = $this->option('school') ? (int) $this->option('school') : null;
        $dryRun = $this->option('dry-run');

        $targetDate = Carbon::createFromDate($year, $month, 1);
        $this->info("Generating monthly invoices for: {$targetDate->format('F Y')}");

        if ($dryRun) {
            $this->warn('DRY RUN - No invoices will be created');
        }

        $schools = $schoolId
            ? School::where('id', $schoolId)->get()
            : School::where('status', 1)->get();

        if ($schools->isEmpty()) {
            $this->error('No schools found.');
            return 1;
        }

        $this->info("Processing {$schools->count()} school(s)...");

        $totalGenerated = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        $progressBar = $this->output->createProgressBar($schools->count());
        $progressBar->start();

        foreach ($schools as $school) {
            try {
                $academicYear = SiteHelper::getAcademicYear($school->id);

                if (!$academicYear) {
                    $this->newLine();
                    $this->warn("  [{$school->name}] No active academic year found. Skipping.");
                    $progressBar->advance();
                    $totalSkipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->newLine();
                    $this->info("  [{$school->name}] Would generate for academic year: {$academicYear->name}");
                    $studentCount = \App\Models\User::where('school_id', $school->id)
                        ->where('usergroup_id', \App\Models\User::STUDENT_USERGROUP_ID)
                        ->where('status', 1)
                        ->count();
                    $this->info("  Student count: {$studentCount}");
                } else {
                    $result = $this->invoiceGenerator->generateAllStudentInvoices(
                        $school->id,
                        $academicYear->id,
                        $targetDate
                    );

                    $totalGenerated += $result['generated'];
                    $totalSkipped += $result['skipped'];
                    $totalErrors += count($result['errors']);

                    if ($result['generated'] > 0) {
                        $this->newLine();
                        $this->info("  [{$school->name}] Generated: {$result['generated']}, Skipped: {$result['skipped']}");
                    }
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  [{$school->name}] Error: {$e->getMessage()}");
                $totalErrors++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if (!$dryRun) {
            $this->info('=== Generation Summary ===');
            $this->info("Invoices Generated: {$totalGenerated}");
            $this->info("Students Skipped: {$totalSkipped}");
            $this->info("Errors: {$totalErrors}");
        } else {
            $this->warn('Dry run completed. Run without --dry-run to create invoices.');
        }

        return $totalErrors > 0 ? 1 : 0;
    }
}