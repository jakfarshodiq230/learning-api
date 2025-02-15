<?php

namespace App\Mail;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmissionCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $assignment;
    public $score;

    public function __construct(Assignment $assignment, $score)
    {
        $this->assignment = $assignment;
        $this->score = $score;
    }

    public function build()
    {
        return $this->subject('New Submission Created')
            ->view('emails.submission_created'); // Blade template untuk email
    }
}
