<section class="panel">
<h2>Pembayaran dan pekerjaan</h2>
<div class="stats-grid"><div><small>Total tagihan</small><h3>@rupiah($order->total)</h3></div><div><small>Sudah dibayar (bersih)</small><h3>@rupiah($order->paid_amount)</h3></div><div><small>Sisa tagihan</small><h3>@rupiah($order->status==='cancelled'?0:$order->balance)</h3></div></div>
<p>Pembayaran: <strong>{{ $order->status==='cancelled'?'Dibatalkan':($order->balance===0?'Lunas':'DP / belum lunas') }}</strong> · Pekerjaan/barang: <strong>{{ $order->status==='cancelled'?'Dibatalkan — reservasi dilepas':($order->fulfilled_at?'Sudah digunakan/diserahkan':'Diproses — barang disisihkan') }}</strong></p>
@if(auth()->user()->isStaff() && $order->status!=='cancelled')
@if($order->balance>0)
<form class="form-grid" method="post" action="/manage/orders/{{ $order->id }}/payments">@csrf<input type="hidden" name="token" value="{{ (string) Illuminate\Support\Str::uuid() }}"><label>Terima pembayaran (Rp)<input type="number" name="amount" min="1" max="{{ $order->balance }}" step="1" value="{{ $order->balance }}" required></label><label>Metode<select name="method"><option value="cash">Tunai</option><option value="transfer">Transfer</option></select></label><div class="align-end"><button>Catat pembayaran</button></div></form>
@endif
@if(!$order->fulfilled_at)
<form method="post" action="/manage/orders/{{ $order->id }}/fulfill" data-confirm="Semua barang sudah digunakan/diserahkan dan pekerjaan selesai? Stok fisik akan dikurangi sekali.">@csrf<button>Barang digunakan/diserahkan & pekerjaan selesai</button></form><p class="muted">Tekan setelah seluruh barang pada transaksi digunakan/diserahkan dan pekerjaan selesai. Pelunasan bisa dicatat setelahnya.</p>
<details class="cancellation-panel"><summary>Batalkan pengerjaan / Kembalikan DP</summary>
<form class="cancellation-form" data-paid="{{ $order->paid_amount }}" method="post" action="/manage/orders/{{ $order->id }}/cancel-staged" data-confirm="Batalkan transaksi dan konfirmasi uang pengembalian sudah diserahkan sesuai jumlah yang ditampilkan?">@csrf
<p>Pembayaran yang sudah diterima: <strong>@rupiah($order->paid_amount)</strong></p>
<label>Pilihan pengembalian<select name="refund_mode" class="refund-mode"><option value="full">Kembalikan seluruh DP — tanpa biaya</option><option value="partial">Kembalikan sebagian DP — potong biaya pengerjaan</option></select></label>
<label class="refund-fee-field" hidden>Biaya pengerjaan yang disepakati (Rp)<input class="refund-fee" name="fee" type="number" min="1" max="{{ max(0,$order->paid_amount-1) }}" step="1" disabled><small>Isi biaya pemeriksaan atau pengerjaan yang sudah disepakati pelanggan.</small></label>
<p>Uang dikembalikan: <strong class="refund-preview" aria-live="polite">@rupiah($order->paid_amount)</strong></p>
<label>Metode pengembalian<select name="method"><option value="cash">Tunai</option><option value="transfer">Transfer</option></select></label>
<label>Alasan pembatalan dan rincian pekerjaan<textarea name="note" required maxlength="500" placeholder="Contoh: pelanggan batal setelah pemeriksaan, biaya pemeriksaan disepakati Rp50.000."></textarea></label>
<p class="muted">Transaksi dibatalkan seluruhnya. Pilihan sebagian berarti sebagian DP dikembalikan; sisanya dicatat sebagai pendapatan jasa. Reservasi barang dilepas.</p>
<button>Konfirmasi pembatalan & pengembalian</button></form></details>
@endif
@endif
@if($order->status==='cancelled')
@php($cancellation=Illuminate\Support\Facades\DB::table('sale_cancellations')->join('users','users.id','=','sale_cancellations.user_id')->where('order_id',$order->id)->select('sale_cancellations.*','users.name')->first())
@if($cancellation)<div class="notice"><strong>{{ $cancellation->fee>0?'Dibatalkan — DP dikembalikan sebagian':'Dibatalkan — DP dikembalikan penuh' }}</strong><p>Pembayaran awal: @rupiah($cancellation->paid_before) · Biaya pengerjaan: @rupiah($cancellation->fee) · Pengembalian: @rupiah($cancellation->refund)</p><p>{{ $cancellation->note }}</p><small>{{ $cancellation->created_at }} · {{ $cancellation->name }}</small>@if($cancellation->fee_order_id)<p><a href="/orders/{{ $cancellation->fee_order_id }}">Lihat transaksi biaya pengerjaan →</a></p>@endif</div>@endif
@endif
<h3>Riwayat pembayaran</h3><div class="table-wrap"><table><thead><tr><th>Waktu</th><th>Metode</th><th>Jumlah</th><th>Keterangan</th></tr></thead><tbody>@foreach($order->payments()->orderBy('id')->get() as $payment)<tr><td>{{ $payment->received_at->format('d M Y H:i') }}</td><td>{{ $payment->method==='cash'?'Tunai':'Transfer' }}</td><td>@rupiah($payment->amount)</td><td>{{ $payment->note }}</td></tr>@endforeach</tbody></table></div>
</section>
