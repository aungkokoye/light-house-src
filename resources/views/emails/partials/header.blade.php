@php
    $companyName     = env('VITE_COMPANY_NAME', 'Light House');
    $companyAddress  = env('VITE_COMPANY_ADDRESS', '');
    $companyPhone    = env('VITE_COMPANY_PHONE', '');
    $companyEmail    = env('VITE_COMPANY_EMAIL', '');
    $companyFacebook = env('VITE_COMPANY_FACEBOOK', '');
@endphp
<tr>
    <td style="padding-bottom:24px;">
        <table cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
            <tr>
                <td style="vertical-align:middle;padding-right:10px;">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ $companyName }}" height="44" style="display:block;" />
                </td>
                <td style="vertical-align:middle;">
                    <p style="margin:0;font-size:15px;font-weight:800;color:#111827;">{{ $companyName }}</p>
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" style="margin-top:6px;">
            <tr>
                <td style="padding:2px 6px 2px 0;vertical-align:middle;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                </td>
                <td style="font-size:11px;color:#6b7280;padding:2px 0;">{{ $companyAddress }}</td>
            </tr>
            <tr>
                <td style="padding:2px 6px 2px 0;vertical-align:middle;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="#9ca3af"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </td>
                <td style="font-size:11px;color:#6b7280;padding:2px 0;">{{ $companyFacebook }}</td>
            </tr>
            <tr>
                <td style="padding:2px 6px 2px 0;vertical-align:middle;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                </td>
                <td style="font-size:11px;color:#6b7280;padding:2px 0;">{{ $companyPhone }}</td>
            </tr>
            <tr>
                <td style="padding:2px 6px 2px 0;vertical-align:middle;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                </td>
                <td style="font-size:11px;color:#6b7280;padding:2px 0;">{{ $companyEmail }}</td>
            </tr>
        </table>
    </td>
</tr>
