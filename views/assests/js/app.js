document.addEventListener('DOMContentLoaded', function () {

  /* ---------- Mobile sidebar toggle ---------- */
  var toggle = document.querySelector('.menu-toggle');
  var sidebar = document.querySelector('.sidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
      if (window.innerWidth <= 900 && sidebar.classList.contains('open') &&
          !sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  /* ---------- Flash message auto-dismiss ---------- */
  document.querySelectorAll('.flash').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.4s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    }, 5000);
  });

  /* ---------- Simple client-side table search ---------- */
  document.querySelectorAll('[data-table-search]').forEach(function (input) {
    var tableId = input.getAttribute('data-table-search');
    var table = document.getElementById(tableId);
    if (!table) return;
    input.addEventListener('input', function () {
      var term = input.value.trim().toLowerCase();
      table.querySelectorAll('tbody tr').forEach(function (row) {
        row.style.display = row.textContent.toLowerCase().indexOf(term) > -1 ? '' : 'none';
      });
    });
  });

  /* ---------- POS / Sale builder: add & remove line items, live totals ---------- */
  var posBody = document.getElementById('pos-lines');
  var addLineBtn = document.getElementById('add-line-btn');
  var lineTemplate = document.getElementById('pos-line-template');

  function recalcTotals() {
    var subtotal = 0;
    document.querySelectorAll('.pos-item-row').forEach(function (row) {
      var qty = parseFloat(row.querySelector('.line-qty')?.value || 0);
      var price = parseFloat(row.querySelector('.line-price')?.value || 0);
      var lineTotal = qty * price;
      var lineTotalEl = row.querySelector('.line-total');
      if (lineTotalEl) lineTotalEl.textContent = lineTotal.toFixed(2);
      subtotal += lineTotal;
    });
    var taxRate = parseFloat(document.getElementById('tax-rate')?.value || 0) / 100;
    var tax = subtotal * taxRate;
    var total = subtotal + tax;

    var subtotalEl = document.getElementById('sum-subtotal');
    var taxEl = document.getElementById('sum-tax');
    var totalEl = document.getElementById('sum-total');
    if (subtotalEl) subtotalEl.textContent = subtotal.toFixed(2);
    if (taxEl) taxEl.textContent = tax.toFixed(2);
    if (totalEl) totalEl.textContent = total.toFixed(2);
  }

  if (posBody) {
    posBody.addEventListener('input', function (e) {
      if (e.target.classList.contains('line-qty') || e.target.classList.contains('line-price')) {
        recalcTotals();
      }
    });

    posBody.addEventListener('click', function (e) {
      var removeBtn = e.target.closest('.remove-line');
      if (removeBtn) {
        var row = removeBtn.closest('.pos-item-row');
        if (posBody.querySelectorAll('.pos-item-row').length > 1) {
          row.remove();
          recalcTotals();
        }
      }
    });

    if (addLineBtn && lineTemplate) {
      addLineBtn.addEventListener('click', function () {
        var clone = lineTemplate.content.cloneNode(true);
        posBody.appendChild(clone);
        recalcTotals();
      });
    }

    var taxInput = document.getElementById('tax-rate');
    if (taxInput) taxInput.addEventListener('input', recalcTotals);

    recalcTotals();
  }
});