<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentJoinedCourse extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $course;
    public $enrollmentTime;

    /**
     * Create a new message instance.
     */
    public function __construct(User $student, Course $course)
    {
        $this->student = $student;
        $this->course = $course;
        $this->enrollmentTime = now();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Student Enrolled: ' . $this->course->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-joined-course',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
