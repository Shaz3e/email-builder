<?php
namespace Shaz3e\EmailBuilder\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class CleanupEmailBuilderImagesCommand extends class
_bottom{
    protected $signature = 'email-builder:cleanup-images
        {--days= : Override retention days}
        {--force : Skip confirmation}';

    protected $description = 'Cleanup unused email images safely';

    public function handle(): int
    {
        $config = config('email-builder.image_cleanup');

        if (! ($config['enabled'] ?? false)) {
            $this->warn('Image cleanup is disabled in config.');
            return self::SUCCESS;
        }

        $disk = $config['disk'] ?? 'public';
        $days = (int) ($this->option('days') ?: $config['retention_days']);
        $directories = collect($config['directories'])->flatten();

        if (! $this->option('force') &&
            ! $this->confirm("Delete unused images older than {$days} days?")
        ) {
            return self::SUCCESS;
        }

        $usedPaths = $this->getUsedImagePaths();
        $cutoff = Carbon::now()->subDays($days);

        $deleted = 0;

        foreach ($directories as $directory) {
            foreach (Storage::disk($disk)->files($directory) as $file) {
                if (
                    ! in_array($file, $usedPaths, true) &&
                    Carbon::createFromTimestamp(
                        Storage::disk($disk)->lastModified($file)
                    )->lt($cutoff)
                ) {
                    Storage::disk($disk)->delete($file);
                    $deleted++;
                }
            }
        }

        $this->info("Cleanup completed. Deleted {$deleted} unused images.");

        return self::SUCCESS;
    }

    /**
     * Collect all image paths currently referenced in DB.
     */
    protected function getUsedImagePaths(): array
    {
        return array_filter(array_merge(

            DB::table('global_email_templates')->pluck('header_image')->all(),
            DB::table('global_email_templates')->pluck('footer_image')->all(),
            DB::table('global_email_templates')->pluck('footer_bottom_image')->all(),

            DB::table('email_templates')->pluck('header_image')->all(),
            DB::table('email_templates')->pluck('footer_image')->all(),
            DB::table('email_templates')->pluck('footer_bottom_image')->all(),

        ));
    }
}