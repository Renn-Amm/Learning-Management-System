<div class="daily-quote">
    @if($loading)
        <div style="background: white; padding: 15px; border: 1px solid #ddd; text-align: center; color: #666;">
            Loading quote...
        </div>
    @elseif($quote)
        <div style="background: white; padding: 20px; border: 1px solid #ddd; border-left: 4px solid #333;">
            <p style="margin: 0 0 10px 0; font-size: 16px; font-style: italic; color: black;">
                "{{ $quote['text'] }}"
            </p>
            <p style="margin: 0; font-size: 14px; color: #666; text-align: right;">
                — {{ $quote['author'] }}
            </p>
        </div>
    @endif
</div>
