<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BorrowingLateMail extends Mailable
{
    public $borrowing;

    public function __construct($borrowing)
    {
        $this->borrowing = $borrowing;
    }

    public function build()
    {
        return $this->subject('Peringatan Keterlambatan Pengembalian Barang')
            ->view('emails.borrowing_late');
    }
}

