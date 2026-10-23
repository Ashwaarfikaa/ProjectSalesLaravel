/* Form item transaksi (penjualan & pembelian).
   Total di sini hanya perkiraan tampilan; server menghitung ulang
   dari harga di database. */
(function () {
    var dataEl = document.getElementById('tx-data');
    if (!dataEl) return;

    var cfg = JSON.parse(dataEl.textContent);
    var mode = cfg.mode;                       // 'sale' atau 'purchase'
    var products = cfg.products || [];
    var byId = {};
    products.forEach(function (p) { byId[p.id] = p; });

    var rows = document.getElementById('rows');
    var grand = document.getElementById('grand');
    var addBtn = document.getElementById('addRow');
    var index = 0;

    function rupiah(n) {
        return 'Rp ' + Math.round(n || 0).toLocaleString('id-ID');
    }

    function el(tag, attrs, text) {
        var node = document.createElement(tag);
        Object.keys(attrs || {}).forEach(function (k) { node.setAttribute(k, attrs[k]); });
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function cell(child, cls) {
        var td = el('td');
        if (cls) td.className = cls;
        if (child) td.appendChild(child);
        return td;
    }

    function addRow(item) {
        item = item || {};
        var i = index++;
        var tr = el('tr');

        var select = el('select', { name: 'items[' + i + '][product_id]', required: 'required', 'aria-label': 'Produk' });
        select.appendChild(el('option', { value: '' }, 'Pilih produk'));
        products.forEach(function (p) {
            var opt = el('option', { value: p.id }, p.sku + ' - ' + p.name);
            if (String(item.product_id) === String(p.id)) opt.selected = true;
            select.appendChild(opt);
        });

        var qty = el('input', { type: 'number', name: 'items[' + i + '][quantity]', min: '1', step: '1', required: 'required', 'aria-label': 'Jumlah' });
        qty.value = item.quantity || 1;

        var priceCell, cost;
        if (mode === 'purchase') {
            cost = el('input', { type: 'number', name: 'items[' + i + '][unit_cost]', min: '0', step: '0.01', required: 'required', 'aria-label': 'Harga beli' });
            cost.value = item.unit_cost !== undefined ? item.unit_cost : '';
            priceCell = cell(cost);
        } else {
            priceCell = cell(el('span', { 'data-price': '' }, '-'), 'num');
        }

        var stockCell = cell(el('span', { 'data-stock': '' }, '-'), 'num');
        var subCell = cell(el('strong', { 'data-subtotal': '' }, rupiah(0)), 'num');

        var remove = el('button', { type: 'button', class: 'btn btn-danger btn-sm', 'aria-label': 'Hapus baris' }, 'Hapus');
        remove.addEventListener('click', function () {
            if (rows.children.length > 1) {
                tr.remove();
                recalc();
            }
        });

        tr.appendChild(cell(select));
        tr.appendChild(priceCell);
        tr.appendChild(stockCell);
        tr.appendChild(cell(qty));
        tr.appendChild(subCell);
        tr.appendChild(cell(remove, 'actions'));
        rows.appendChild(tr);

        function refresh(fillCost) {
            var p = byId[select.value];
            tr.querySelector('[data-stock]').textContent = p ? p.stock : '-';
            if (mode === 'sale') {
                tr.querySelector('[data-price]').textContent = p ? rupiah(p.price) : '-';
                if (p) qty.max = p.stock; else qty.removeAttribute('max');
            } else if (fillCost && p) {
                cost.value = p.price;
            }
            recalc();
        }

        select.addEventListener('change', function () { refresh(true); });
        qty.addEventListener('input', recalc);
        if (cost) cost.addEventListener('input', recalc);
        refresh(false);
    }

    function recalc() {
        var total = 0;
        Array.prototype.forEach.call(rows.children, function (tr) {
            var select = tr.querySelector('select');
            var qty = parseInt(tr.querySelector('input[name$="[quantity]"]').value, 10) || 0;
            var p = byId[select.value];
            var price = 0;
            if (mode === 'purchase') {
                price = parseFloat(tr.querySelector('input[name$="[unit_cost]"]').value) || 0;
            } else if (p) {
                price = p.price;
            }
            var sub = p ? price * qty : 0;
            tr.querySelector('[data-subtotal]').textContent = rupiah(sub);
            total += sub;
        });
        grand.textContent = rupiah(total);
    }

    addBtn.addEventListener('click', function () { addRow(); });

    var old = (cfg.old && cfg.old.length) ? cfg.old : [{}];
    old.forEach(addRow);
})();