<?php
// Footer include: contains modal, scripts and closing tags
?>

  <!-- modal add/edit product -->
  <!-- login modal -->
  <div id="loginModal" style="display:none" class="modal">
    <div class="modal-card card">
      <h3>Login Admin</h3>
      <div style="display:flex;flex-direction:column;gap:8px;margin-top:12px">
        <input id="login_user" placeholder="Username" />
        <input id="login_pass" placeholder="Password" type="password" />
      </div>
      <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px">
        <button class="btn ghost" onclick="closeLogin()">Batal</button>
        <button class="btn" onclick="login()">Login</button>
      </div>
    </div>
  </div>

  <div id="modal" style="display:none" class="modal">
    <div class="modal-card card">
      <h3 id="modalTitle">Tambah Produk</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:12px">
        <input id="p_name" placeholder="Nama produk" />
        <input id="p_code" placeholder="Kode / SKU" />
        <input id="p_price" placeholder="Harga (numeric)" />
        <input id="p_stock" placeholder="Stok" />
        <input id="p_category" placeholder="Kategori" />
        <input id="p_unit" placeholder="Satuan (pcs/kg)" />
      </div>
      <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px">
        <button class="btn ghost" onclick="closeModal()">Batal</button>
        <button class="btn" onclick="saveProduct()">Simpan</button>
      </div>
    </div>
  </div>

  <script>
    // --- Simple local data store ---
    const LS_KEY = 'pos_warung_v1';
    let state = {
      products: [],
      cart: [],
      members: [],
      sales: [],
      kas: 0,
      mode: 'TUNAI',
      role: 'customer' // or 'admin'
    };

    // init sample data if empty
    function init(){
      const saved = localStorage.getItem(LS_KEY);
      if(saved){ state = JSON.parse(saved); }
      if(state.products.length===0) {
        state.products = [
          {id:1,name:'Beras 5kg',code:'BR05',price:65000,stock:20,category:'Beras',unit:'pack'},
          {id:2,name:'Gula 1kg',code:'GL01',price:15000,stock:50,category:'Bumbu',unit:'pack'},
          {id:3,name:'Minyak 2L',code:'MK02',price:30000,stock:30,category:'Minyak',unit:'botol'},
          {id:4,name:'Sarden Kaleng',code:'SD01',price:12000,stock:60,category:'Makanan',unit:'pcs'},
          {id:5,name:'Rokok A',code:'RK01',price:20000,stock:80,category:'Rokok',unit:'pcs'},
          {id:6,name:'Kopi 250g',code:'KP25',price:22000,stock:40,category:'Minuman',unit:'pack'}
        ];
      }
      renderAll();
    }

    // --- Auth / Role UI ---
    function openLogin(){ document.getElementById('loginModal').style.display='flex'; }
    function closeLogin(){ document.getElementById('loginModal').style.display='none'; }
    async function login(){
      const u = document.getElementById('login_user').value;
      const p = document.getElementById('login_pass').value;
      const fd = new FormData(); fd.append('action','login'); fd.append('username',u); fd.append('password',p);
      const res = await fetch('auth.php',{method:'POST',body:fd});
      const j = await res.json();
      if(j.ok){ state.role = j.role; updateRoleUI(); closeLogin(); alert('Login sukses: ' + state.role); renderAll(); } else { alert('Login gagal'); }
    }
    async function logout(){ const res = await fetch('auth.php?action=logout'); const j = await res.json(); state.role = j.role || 'customer'; updateRoleUI(); renderAll(); }
    async function checkAuth(){ try{ const res = await fetch('auth.php'); const j = await res.json(); state.role = j.role || 'customer'; }catch(e){ state.role = 'customer'; } updateRoleUI(); }
    function updateRoleUI(){ document.querySelectorAll('.admin-only').forEach(el=> el.style.display = state.role==='admin'? 'inline-block':'none'); document.getElementById('authBtn').innerText = state.role==='admin'? 'Logout Admin' : 'Login Admin'; if(state.role==='admin'){ document.getElementById('authBtn').onclick = logout; } else { document.getElementById('authBtn').onclick = openLogin; } }

    function showProducts(){ // decide which product view to show based on role
      document.querySelectorAll('main section').forEach(s=>s.style.display='none');
      if(state.role === 'admin'){
        document.getElementById('produk_admin').style.display = 'block';
        document.getElementById('pageTitle').innerText = 'Manajemen Produk (Admin)';
      } else {
        document.getElementById('produk_customer').style.display = 'block';
        document.getElementById('pageTitle').innerText = 'Produk';
      }
      renderProductsCustomer();
      renderProductTable();
    }

    function saveState(){ localStorage.setItem(LS_KEY, JSON.stringify(state)); renderAll(); }

    // --- UI navigation ---
    function show(id){ document.querySelectorAll('main section').forEach(s=>s.style.display='none'); document.getElementById(id).style.display='block'; document.getElementById('pageTitle').innerText = (id==='pos'? 'POS & Kasir': id==='produk'? 'Manajemen Produk' : id==='laporan'? 'Laporan' : id==='member'?'Member & Promo':'Pengaturan'); }

    // --- Products ---
    function renderProducts(){
      const q = document.getElementById('globalSearch').value.toLowerCase();
      const cat = document.getElementById('filterCat').value.toLowerCase();
      const container = document.getElementById('products'); container.innerHTML='';
      const list = state.products.filter(p=> (p.name.toLowerCase().includes(q) || p.code.toLowerCase().includes(q)) && (cat? p.category.toLowerCase().includes(cat):true));
      document.getElementById('totalProducts').innerText = list.length;
      list.forEach(p=>{
        const el = document.createElement('div'); el.className='product card';
        const imgHtml = p.image ? `<img src="${p.image}" style="width:100%;height:120px;object-fit:cover;border-radius:6px;margin-bottom:8px;">` : '';
        el.innerHTML = `${imgHtml}<div class='title'>${p.name}</div><div class='meta'>${p.code} • Rp ${format(p.price)} • ${p.stock} ${p.unit}</div>`;
        el.onclick = ()=> addToCart(p.id);
        container.appendChild(el);
      })
      renderProductTable();
    }

    function renderProductsCustomer(){
      const q = document.getElementById('globalSearch').value.toLowerCase();
      const cat = document.getElementById('filterCat') ? document.getElementById('filterCat').value.toLowerCase() : '';
      const container = document.getElementById('products_customer'); if(!container) return; container.innerHTML='';
      const list = state.products.filter(p=> (p.name.toLowerCase().includes(q) || p.code.toLowerCase().includes(q)) && (cat? p.category.toLowerCase().includes(cat):true));
      list.forEach(p=>{
        const el = document.createElement('div'); el.className='product card';
        const imgHtml = p.image ? `<img src="${p.image}" style="width:100%;height:120px;object-fit:cover;border-radius:6px;margin-bottom:8px;">` : '';
        el.innerHTML = `${imgHtml}<div class='title'>${p.name}</div><div class='meta'>${p.code} • Rp ${format(p.price)} • ${p.stock} ${p.unit}</div>`;
        el.onclick = ()=> { if(confirm('Tambah ke keranjang?')) addToCart(p.id); };
        container.appendChild(el);
      })
    }

    function renderProductTable(){
      const t = document.getElementById('productTable');
      let html = `<table style="width:100%;border-collapse:collapse"><thead><tr style='text-align:left'><th>Nama</th><th>Kode</th><th>Harga</th><th>Stok</th><th>Kategori</th><th>Aksi</th></tr></thead><tbody>`;
      state.products.forEach(p=>{
        if(state.role === 'admin'){
          html+=`<tr><td>${p.name}</td><td>${p.code}</td><td>Rp ${format(p.price)}</td><td>${p.stock}</td><td>${p.category}</td><td><button class='btn ghost' onclick='editProduct(${p.id})'>Edit</button> <button class='btn red' onclick='removeProduct(${p.id})'>Hapus</button></td></tr>`
        } else {
          html+=`<tr><td>${p.name}</td><td>${p.code}</td><td>Rp ${format(p.price)}</td><td>${p.stock}</td><td>${p.category}</td><td>-</td></tr>`
        }
      })
      html += '</tbody></table>';
      t.innerHTML = html;
      renderMemberTable();
    }

    function openAddProduct(editId){ document.getElementById('modal').style.display='flex'; document.getElementById('modalTitle').innerText = editId? 'Edit Produk' : 'Tambah Produk'; if(editId){ const p = state.products.find(x=>x.id===editId); document.getElementById('p_name').value=p.name; document.getElementById('p_code').value=p.code; document.getElementById('p_price').value=p.price; document.getElementById('p_stock').value=p.stock; document.getElementById('p_category').value=p.category; document.getElementById('p_unit').value=p.unit; document.getElementById('modal').dataset.edit=editId; } else { document.getElementById('p_name').value='';document.getElementById('p_code').value='';document.getElementById('p_price').value='';document.getElementById('p_stock').value='';document.getElementById('p_category').value='';document.getElementById('p_unit').value=''; delete document.getElementById('modal').dataset.edit; } }
    function closeModal(){ document.getElementById('modal').style.display='none' }
    function saveProduct(){ const id = document.getElementById('modal').dataset.edit; const p = { id: id? Number(id): Date.now(), name:document.getElementById('p_name').value || 'Tanpa Nama', code:document.getElementById('p_code').value || ('SKU'+Date.now()), price:Number(document.getElementById('p_price').value)||0, stock:Number(document.getElementById('p_stock').value)||0, category:document.getElementById('p_category').value||'Umum', unit:document.getElementById('p_unit').value||'pcs' };
      if(id){ state.products = state.products.map(x=> x.id==id? p : x); } else { state.products.push(p); }
      closeModal(); saveState(); }
    function editProduct(id){ openAddProduct(id) }
    function removeProduct(id){ if(confirm('Hapus produk?')){ state.products = state.products.filter(p=>p.id!==id); saveState(); } }

    // --- Cart ---
    function addToCart(id){ const p = state.products.find(x=>x.id===id); if(!p) return; const existing = state.cart.find(c=>c.id===id); if(existing){ existing.qty++; } else { state.cart.push({id:p.id,name:p.name,price:p.price,qty:1}); } saveState(); }
    function renderCart(){ const c = document.getElementById('cartItems'); c.innerHTML=''; state.cart.forEach(item=>{ const el=document.createElement('div'); el.className='cart-item'; el.innerHTML = `<div><div style='font-weight:700'>${item.name}</div><div class='small'>Rp ${format(item.price)} • Qty: <input type='number' min='1' value='${item.qty}' style='width:60px' onchange='updateQty(${item.id},this.value)' /></div></div><div style='text-align:right'><div style='font-weight:700'>Rp ${format(item.price*item.qty)}</div><div style='margin-top:6px'><button class='btn ghost' onclick='removeFromCart(${item.id})'>-</button></div></div>`; c.appendChild(el); }); updateTotals(); }
    function updateQty(id,val){ state.cart = state.cart.map(i=> i.id===id? {...i, qty: Number(val) } : i); saveState(); }
    function removeFromCart(id){ state.cart = state.cart.filter(i=> i.id!==id); saveState(); }
    function clearCart(){ if(confirm('Kosongkan keranjang?')){ state.cart=[]; saveState(); } }

    function updateTotals(){ const sub = state.cart.reduce((s,i)=> s + i.price * i.qty, 0); const disc = 0; const tot = sub - disc; document.getElementById('subtotal').innerText = format(sub); document.getElementById('diskon').innerText = format(disc); document.getElementById('total').innerText = format(tot); }

    function checkout(){ if(state.cart.length===0){ alert('Keranjang masih kosong'); return; } const tot = state.cart.reduce((s,i)=> s + i.price * i.qty, 0); // simple sale
      const sale = { id: Date.now(), date: new Date().toISOString(), items: JSON.parse(JSON.stringify(state.cart)), total: tot, mode: state.mode };
      state.sales.push(sale); state.kas += tot; // reduce stock
      sale.items.forEach(it=>{ const p = state.products.find(x=>x.id===it.id); if(p) p.stock = Math.max(0, p.stock - it.qty); });
      state.cart = []; saveState(); alert('Terima kasih! Transaksi tersimpan.'); renderAll(); }

    function printReceipt(){ if(state.sales.length===0){ alert('Tidak ada transaksi untuk dicetak'); return;} const s = state.sales[state.sales.length-1]; let out = '--- NOTA ---\nPOS Warung\n'; out += 'ID: '+s.id+'\nTanggal: '+(new Date(s.date)).toLocaleString()+'\n'; s.items.forEach(i=> out += `${i.name} x${i.qty} - Rp ${format(i.price*i.qty)}\n` ); out += 'TOTAL: Rp '+format(s.total)+'\nTerima Kasih\n'; const w = window.open('','_blank'); w.document.write('<pre>'+out+'</pre>'); w.print(); }

    // --- Reports ---
    function renderCharts(){ const ctx = document.getElementById('salesChart').getContext('2d'); const labels = state.sales.slice(-7).map(s=> new Date(s.date).toLocaleDateString()); const values = state.sales.slice(-7).map(s=> s.total);
      if(window._salesChart) window._salesChart.destroy(); window._salesChart = new Chart(ctx,{type:'line',data:{labels, datasets:[{label:'Penjualan (Rp)',data:values,fill:true}]}});
      const ctx2 = document.getElementById('topProductsChart').getContext('2d'); const map = {}; state.sales.flatMap(s=>s.items).forEach(i=> map[i.name] = (map[i.name]||0) + i.qty); const labels2 = Object.keys(map).slice(0,6); const data2 = labels2.map(k=> map[k]); if(window._topChart) window._topChart.destroy(); window._topChart = new Chart(ctx2,{type:'bar',data:{labels:labels2, datasets:[{label:'Terjual (qty)',data:data2}]}});
    }

    // --- Member ---
    function renderMemberTable(){ const t = document.getElementById('memberTable'); let html = '<table style="width:100%;border-collapse:collapse"><thead><tr style="text-align:left"><th>Nama</th><th>Kode</th><th>Point</th></tr></thead><tbody>';
      state.members.forEach(m=> html += `<tr><td>${m.name}</td><td>${m.code}</td><td>${m.points||0}</td></tr>`);
      html += '</tbody></table>'; t.innerHTML = html;
    }

    // --- Utilities ---
    function format(n){ return Number(n).toLocaleString('id-ID'); }
    function clearData(){ if(confirm('Reset semua data?')){ localStorage.removeItem(LS_KEY); location.reload(); } }
    function changeStore(v){ alert('Switch toko: '+v); }
    function toggleMode(){ state.mode = state.mode==='TUNAI'?'NON-TUNAI':'TUNAI'; document.getElementById('mode').innerText = state.mode; saveState(); }

    function exportCSV(){ // export sales
      let csv = 'id,date,total\n'; state.sales.forEach(s=> csv += `${s.id},"${s.date}",${s.total}\n`);
      const blob = new Blob([csv],{type:'text/csv'}); const url = URL.createObjectURL(blob); const a=document.createElement('a'); a.href=url; a.download='sales.csv'; a.click(); URL.revokeObjectURL(url);
    }

    function downloadBackup(){ const blob = new Blob([JSON.stringify(state)],{type:'application/json'}); const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='pos_backup.json'; a.click(); }
    function restoreBackup(e){ const f = e.target.files[0]; const r = new FileReader(); r.onload = ()=>{ try{ state = JSON.parse(r.result); saveState(); alert('Restore selesai'); } catch(err){ alert('File tidak valid'); } }; r.readAsText(f);
    }

    function removeProduct(id){ if(confirm('Hapus produk?')){ state.products = state.products.filter(p=>p.id!==id); saveState(); } }

    // --- init render ---
    function renderAll(){ document.getElementById('kas').innerText = format(state.kas); renderProducts(); renderProductsCustomer(); renderCart(); renderCharts(); renderProductTable(); }

    // hooks
    document.getElementById('globalSearch').addEventListener('input',()=>{ renderProducts(); renderProductsCustomer(); });

    // check auth first, then init
    checkAuth().then(()=> init());
  </script>
</body>
</html>
