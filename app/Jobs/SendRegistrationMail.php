<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRegistrationMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::debug(json_encode($this->user));
            $sent = Mail::send(['text'=>'mail.registration'], $this->user, function($message) {
                $message->to($this->user->email, $this->user->name)->subject('Register Successfully.');
            });
            if($sent)
                Log::info('Mail send successfully.');
            else
                Log::error('Mail sending failed.');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
    }
}
