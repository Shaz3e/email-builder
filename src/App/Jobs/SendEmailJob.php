<?php

namespace Shaz3e\EmailBuilder\App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Shaz3e\EmailBuilder\App\Mail\EmailTemplateMail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $templateName;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $templateName, array $data = [])
    {
        $this->user = $user;
        $this->templateName = $templateName;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Mail::to($this->user->email)
            ->send(new EmailTemplateMail($this->user, $this->templateName, $this->data));

    }
}
