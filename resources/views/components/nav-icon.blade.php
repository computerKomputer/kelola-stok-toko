@props(['name'])
<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
 @switch($name)
  @case('dashboard')<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>@break
  @case('orders')<rect x="4" y="4" width="16" height="17" rx="2"/><path d="M8 4.5h8M8 10h8M8 14h8M8 18h5"/>@break
  @case('sale')<path d="M12 4v16M4 12h16"/>@break
  @case('expense')<path d="M3 7h18v13H3zM3 10h18M7 16h4"/>@break
  @case('expense-category')<path d="M4 6h16M4 12h16M4 18h16"/>@break
  @case('product')<path d="m12 2 9 5-9 5-9-5 9-5ZM3 7v10l9 5 9-5V7M12 12v10"/>@break
  @case('category')<path d="M4 6h16M4 12h16M4 18h16"/>@break
  @case('stock')<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 10h18M9 4v17M15 4v17"/>@break
  @case('supplier')<path d="M3 21V7l9-4 9 4v14M3 21h18M9 21v-6h6v6M7 10h2m6 0h2"/>@break
  @case('report')<path d="M4 20h16M7 17V9M12 17V4M17 17v-6"/>@break
  @case('article')<path d="M5 3h14a2 2 0 0 1 2 2v16H7a4 4 0 0 1 0-8h14M5 3a2 2 0 0 0-2 2v12a4 4 0 0 1 4-4M8 7h9M8 10h7"/>@break
  @case('users')<circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M16 6a3 3 0 0 1 0 6M18 15a5 5 0 0 1 3 5"/>@break
  @case('settings')<circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3M4.9 4.9 7 7m10 10 2.1 2.1M19.1 4.9 17 7M7 17l-2.1 2.1"/>@break
  @case('profile')<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>@break
  @case('cart')<path d="M3 4h2l2 12h12l2-9H6M9 20h.01M18 20h.01"/>@break
  @case('external')<path d="M13 5h6v6M19 5l-9 9M19 13v6H5V5h6"/>@break
 @endswitch
</svg>
