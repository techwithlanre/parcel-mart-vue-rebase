<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class QuoteForm extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private $request)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Request Quote Form',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.request-quote',
            with: [
                'name'=>$this->request->name,
                'email'=>$this->request->email,
                'phone'=>$this->request->phone,
                'from_country'=>getCountry('id', $this->request->country_from)->name,
                'to_country'=>getCountry('id', $this->request->country_to)->name,
                'address_from'=>$this->request->address_from,
                'address_to'=>$this->request->address_to,
                'quantity'=>$this->request->quantity,
                'weight'=>$this->request->weight,
                'length'=>$this->request->length,
                'width'=>$this->request->width,
                'height'=>$this->request->height,
                'metric'=>$this->request->metric,
            ]
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
