<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\SmartFinanceRecommendation;

class SmartFinanceRecommendationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public SmartFinanceRecommendation $recommendation;

    public function __construct(SmartFinanceRecommendation $recommendation)
    {
        $this->recommendation = $recommendation;
    }

    public function build()
    {
        return $this->subject('Rekomendasi Smart Finance untuk Anda')
                    ->view('emails.smart_finance.recommendation')
                    ->with(['rec' => $this->recommendation]);
    }
}
