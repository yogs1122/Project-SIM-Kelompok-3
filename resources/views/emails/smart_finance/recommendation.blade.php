<div style="font-family: sans-serif; line-height:1.5; color:#222">
    <h2>Halo {{ $rec->user->name }},</h2>

    <p>Admin mengirim rekomendasi untuk akun Smart Finance Anda:</p>

    <div style="padding:12px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; margin:12px 0">
        {!! nl2br(e($rec->rendered_message ?? $rec->message)) !!}
    </div>

    <p style="font-size:0.95rem; color:#555">Jika Anda ingin mendiskusikan rekomendasi ini, kunjungi dashboard Smart Finance Anda.</p>

    <p style="margin-top:18px">Salam,<br/>Tim Smart Finance</p>
</div>
