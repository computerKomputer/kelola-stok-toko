document.querySelector('.menu-toggle')?.addEventListener('click', function () {
  const open = document.body.classList.toggle('menu-open');
  this.setAttribute('aria-expanded', String(open));
});
function closeMenu() {
  document.body.classList.remove('menu-open');
  document.querySelector('.menu-toggle')?.setAttribute('aria-expanded', 'false');
}
document.querySelector('.menu-close')?.addEventListener('click', () => {
  closeMenu();
  document.querySelector('.menu-toggle')?.focus();
});
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
document.addEventListener('click', event => {
  if (!event.target.closest('.sidebar, .menu-toggle')) closeMenu();
});
document.querySelectorAll('form[data-confirm]').forEach(form => {
  form.addEventListener('submit', event => {
    if (!window.confirm(form.dataset.confirm)) event.preventDefault();
  });
});
document.querySelectorAll('.product-gallery-thumb').forEach(button => {
  button.addEventListener('click', () => {
    const main = document.getElementById('product-gallery-main');
    if (!main) return;
    main.src = button.dataset.galleryUrl;
    main.alt = button.dataset.galleryAlt;
    document.querySelectorAll('.product-gallery-thumb').forEach(thumb => {
      const selected = thumb === button;
      thumb.classList.toggle('selected', selected);
      thumb.setAttribute('aria-pressed', String(selected));
    });
  });
});
document.querySelectorAll('[data-photo-input]').forEach(input => {
  const preview = document.querySelector(`[data-photo-preview="${input.dataset.photoInput}"]`);
  if (!preview) return;
  const original = preview.getAttribute('src');
  let objectUrl;
  input.addEventListener('change', () => {
    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = input.files?.[0] ? URL.createObjectURL(input.files[0]) : null;
    if (objectUrl) preview.src = objectUrl;
    else if (original) preview.src = original;
    else preview.removeAttribute('src');
    preview.hidden = !objectUrl && !original;
  });
});
document.querySelectorAll('form').forEach(form => {
  form.addEventListener('submit', event => {
    if (event.defaultPrevented || !form.checkValidity()) return;
    const button = event.submitter;
    if (button) setTimeout(() => { button.disabled = true; }, 0);
  });
});
const stockAddition = document.getElementById('stock-addition');
if (stockAddition) {
  const current = Number(document.getElementById('current-stock').value);
  const total = document.getElementById('stock-total');
  const updateTotal = () => {
    const extra = Number(stockAddition.value);
    const result = current + (document.getElementById('stock-operation')?.value === 'subtract' ? -extra : extra);
    total.textContent = Number.isInteger(extra) && extra >= 0 && result >= 0 && result <= 1000000 ? String(result) : '—';
  };
  stockAddition.addEventListener('input', updateTotal);
  document.getElementById('stock-operation')?.addEventListener('change', updateTotal);
  updateTotal();
}
const saleLines = document.getElementById('sale-lines');
if (saleLines) {
  let lineIndex = Math.max(...Array.from(saleLines.querySelectorAll('.line-type'), el => Number(el.name.match(/\[(\d+)\]/)[1]))) + 1;
  const currency = value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
  const recalculateSale = () => {
    let total = 0;
    const rows = saleLines.querySelectorAll('.sale-line');
    rows.forEach(row => {
      const service = row.querySelector('.line-type').value === 'service';
      const search = row.querySelector('.product-search');
      const productSelect = row.querySelector('.line-product');
      if (!search.dataset.initialized) { search.value = productSelect.value ? productSelect.selectedOptions[0].textContent : ''; search.dataset.initialized = 'true'; }
      search.setCustomValidity(!service && !productSelect.value ? 'Pilih produk dari hasil pencarian.' : '');
      row.querySelectorAll('.product-field,.service-field').forEach(field => {
        const enabled = field.classList.contains('service-field') === service;
        field.hidden = !enabled;
        field.querySelectorAll('input,select').forEach(input => { input.disabled = !enabled; input.required = enabled && !input.classList.contains('line-product'); });
      });
      const price = service ? Number(row.querySelector('.line-price').value) : Number(row.querySelector('.line-product').selectedOptions[0]?.dataset.price || 0);
      const quantity = Number(row.querySelector('.line-quantity').value);
      const subtotal = Number.isFinite(price * quantity) ? price * quantity : 0;
      row.querySelector('.line-subtotal').textContent = currency(subtotal);
      row.querySelector('.remove-line').disabled = rows.length === 1;
      total += subtotal;
    });
    document.getElementById('sale-total').textContent = currency(total);
    const depositMode = document.getElementById('payment-mode')?.value === 'deposit';
    const deposit = document.getElementById('deposit-amount');
    if (deposit) { deposit.disabled = !depositMode; deposit.required = depositMode; deposit.max = Math.max(0,total-1); document.getElementById('deposit-field').hidden = !depositMode; document.getElementById('deposit-balance').hidden = !depositMode; document.getElementById('remaining-total').textContent = currency(Math.max(0,total-Number(deposit.value || 0))); }
    document.getElementById('add-sale-line').disabled = rows.length >= 50;
  };
  document.getElementById('add-sale-line').addEventListener('click', () => {
    if (saleLines.children.length >= 50) return;
    const fragment = document.getElementById('sale-line-template').content.cloneNode(true);
    fragment.querySelectorAll('[name]').forEach(input => { input.name = input.name.replace('[ROW]', '[' + lineIndex + ']'); });
    lineIndex++;
    saleLines.appendChild(fragment);
    recalculateSale();
  });
    saleLines.addEventListener('input', event => {
    if (event.target.matches('.product-search')) {
      const search = event.target;
      const field = search.closest('.product-field');
      const select = field.querySelector('.line-product');
      const results = field.querySelector('.product-results');
      select.value = '';
      results.replaceChildren();
      const query = search.value.trim().toLocaleLowerCase('id-ID');
      results.hidden = !query;
      if (query) {
        const matches = Array.from(select.options).filter(option => option.value && (option.textContent + ' ' + option.dataset.sku).toLocaleLowerCase('id-ID').includes(query)).slice(0,20);
        matches.forEach(option => {
          const button = document.createElement('button');
          button.type = 'button'; button.className = 'product-result';
          button.textContent = option.textContent + ' · ' + option.dataset.sku;
          button.addEventListener('click', () => { select.value = option.value; search.value = option.textContent; results.hidden = true; recalculateSale(); search.focus(); });
          results.appendChild(button);
        });
        if (!matches.length) { const message = document.createElement('p'); message.textContent = 'Produk tidak ditemukan atau stok tidak tersedia.'; results.appendChild(message); }
      }
    }
    recalculateSale();
  });
  saleLines.addEventListener('keydown', event => {
    const field = event.target.closest('.product-field');
    if (!field) return;
    const results = field.querySelector('.product-results');
    if (event.key === 'Escape') { results.hidden = true; field.querySelector('.product-search').focus(); }
    if (event.key === 'ArrowDown' && !results.hidden) { event.preventDefault(); if(event.target.matches('.product-search')) results.querySelector('button')?.focus(); else event.target.nextElementSibling?.focus(); }
    if (event.key === 'ArrowUp' && !results.hidden) { event.preventDefault(); (event.target.previousElementSibling || field.querySelector('.product-search')).focus(); }
  });
  saleLines.addEventListener('change', recalculateSale);
  document.getElementById('payment-mode')?.addEventListener('change', recalculateSale);
  document.getElementById('deposit-amount')?.addEventListener('input', recalculateSale);
  saleLines.addEventListener('click', event => { const button = event.target.closest('.remove-line'); if(button && saleLines.children.length > 1){button.closest('.sale-line').remove();recalculateSale();} });
  recalculateSale();
}

// Scrolling the page must not accidentally change a focused numeric field.
// Keyboard arrows and the input's spinner buttons retain their native behavior.
document.addEventListener('wheel', event => {
  const active = document.activeElement;
  if (active instanceof HTMLInputElement && active.type === 'number') {
    active.blur();
  }
}, { capture: true, passive: true });

document.querySelectorAll('.cancellation-form').forEach(form => {
  const mode = form.querySelector('.refund-mode');
  const fee = form.querySelector('.refund-fee');
  const paid = Number(form.dataset.paid);
  const update = () => {
    const partial = mode.value === 'partial';
    form.querySelector('.refund-fee-field').hidden = !partial;
    fee.disabled = !partial; fee.required = partial;
    const deduction = partial ? Number(fee.value || 0) : 0;
    const valid = Number.isInteger(deduction) && deduction >= 0 && deduction <= paid;
    form.querySelector('.refund-preview').textContent = valid ? 'Rp ' + new Intl.NumberFormat('id-ID').format(paid - deduction) : 'Periksa biaya pengerjaan';
  };
  mode.addEventListener('change',update);fee.addEventListener('input',update);update();
});

const expenseForm = document.getElementById('expense-form');
if (expenseForm) {
  const category = document.getElementById('expense-category');
  const action = document.getElementById('expense-stock-action');
  const sync = () => {
    Array.from(action.options).forEach(option => { option.disabled = (option.value === 'add' && category.value !== 'purchase') || (option.value === 'remove' && category.value !== 'internal'); });
    if (action.selectedOptions[0]?.disabled) action.value = 'none';
    const supplier = document.getElementById('expense-supplier');
    supplier.hidden = category.value !== 'purchase';
    supplier.querySelector('select').disabled = supplier.hidden;
    supplier.querySelector('select').required = !supplier.hidden;
    const fields = document.getElementById('expense-stock-fields');
    fields.hidden = action.value === 'none';
    fields.querySelectorAll('input,select').forEach(input => {input.disabled = fields.hidden;input.required = !fields.hidden;});
  };
  category.addEventListener('change',sync);action.addEventListener('change',sync);sync();
}
