/* --------------- Sample Data --------------- */
let orders = [
    { id: "ORD-001", customer: "John Doe", items: 3, total: 89.50, status: "Completed", date: "2024-01-15" },
    { id: "ORD-002", customer: "Jane Smith", items: 2, total: 45.00, status: "Pending", date: "2024-01-20" },
    { id: "ORD-003", customer: "Mike Johnson", items: 5, total: 156.75, status: "Shipped", date: "2024-01-22" }
];

let users = [
    { id: "USR-001", name: "John Doe", email: "john@example.com", phone: "+1 555-0123", role: "Customer", joinDate: "2023-06-15", status: "Active" },
    { id: "USR-002", name: "Jane Smith", email: "jane@example.com", phone: "+1 555-0456", role: "Admin", joinDate: "2023-01-20", status: "Active" },
    { id: "USR-003", name: "Mike Johnson", email: "mike@example.com", phone: "+1 555-0789", role: "Moderator", joinDate: "2023-08-10", status: "Active" }
];

let menuItems = [
    { id: "MENU-001", name: "Cappuccino", category: "Coffee", price: 25, availability: "Available" },
    { id: "MENU-002", name: "Cheeseburger", category: "Fast Food", price: 40, availability: "Out of Stock" }
];

/* --------------- UI helpers --------------- */
const toastContainer = document.getElementById('toast-container') || createToastContainer();

function createToastContainer() {
    const cont = document.createElement('div');
    cont.id = 'toast-container';
    cont.className = 'toast-container';
    document.body.appendChild(cont);
    return cont;
}
function showToast(message, type = 'success', timeout = 2800) {
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.textContent = message;
    toastContainer.appendChild(t);
    setTimeout(() => t.style.opacity = '0', timeout - 300);
    setTimeout(() => t.remove(), timeout);
}

/* Modal open/close helpers */
function openModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.setAttribute('aria-hidden', 'false');
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.setAttribute('aria-hidden', 'true');
}

/* Sidebar collapse */
const sidebar = document.getElementById('sidebar');
document.getElementById('collapse-btn').addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    // change icon direction
    const icon = document.querySelector('#collapse-btn i');
    if (sidebar.classList.contains('collapsed')) icon.setAttribute('data-lucide','chevrons-right');
    else icon.setAttribute('data-lucide','chevrons-left');
    lucide.createIcons();
});

/* Quick add — opens order modal */
function quickAddOrder(){
    openModal('order-modal');
    // focus first input after short delay for animation
    setTimeout(()=>document.getElementById('customer-name').focus(), 200);
}

/* --------------- Page Navigation --------------- */
function showPage(page, event) {
    document.querySelectorAll('.page').forEach(p => p.style.display = 'none');
    const pageEl = document.getElementById(`${page}-page`);
    if (!pageEl) return;
    pageEl.style.display = 'block';

    document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
    if (event && event.currentTarget) event.currentTarget.classList.add('active');

    // render page-specific content
    if (page === 'orders') { renderOrders(); updateOrderStats(); }
    else if (page === 'users') { renderUsers(); updateUserStats(); }
    else if (page === 'menu') { renderMenu(); updateMenuStats(); populateMenuCategories(); }
    else if (page === 'reports') { renderReports(); renderAllCharts(); }
    lucide.createIcons();
}

/* --------------- ORDERS --------------- */
let editingOrderId = null;

function saveOrder(){
    const customer = document.getElementById('customer-name').value.trim();
    const items = parseInt(document.getElementById('order-items').value) || 0;
    const totalRaw = document.getElementById('order-total').value.trim();
    const total = parseFloat(totalRaw) || 0;
    const status = document.getElementById('order-status').value;

    if (!customer || !items || total <= 0) {
        showToast('Please complete all order fields', 'warn');
        return;
    }

    if (editingOrderId) {
        // edit existing
        const o = orders.find(x=>x.id===editingOrderId);
        if (o){
            o.customer = customer; o.items = items; o.total = total; o.status = status;
            showToast('Order updated', 'success');
        }
        editingOrderId = null;
    } else {
        const id = `ORD-${String(orders.length+1).padStart(3,'0')}`;
        orders.push({ id, customer, items, total, status, date: new Date().toISOString().split('T')[0]});
        showToast('Order added', 'success');
    }

    closeModal('order-modal'); clearOrderForm();
    renderOrders(); updateOrderStats(); renderReports(); renderAllCharts();
}

function clearOrderForm(){
    document.getElementById('customer-name').value='';
    document.getElementById('order-items').value='';
    document.getElementById('order-total').value='';
    document.getElementById('order-status').value='Pending';
}

function deleteOrder(orderId){
    if (!confirm('Are you sure you want to delete this order?')) return;
    orders = orders.filter(o=>o.id !== orderId);
    renderOrders(); updateOrderStats();
    renderReports(); renderAllCharts();
    showToast('Order deleted', 'danger');
}

function editOrder(orderId){
    const o = orders.find(x=>x.id===orderId);
    if(!o) return;
    editingOrderId = o.id;
    document.getElementById('customer-name').value = o.customer;
    document.getElementById('order-items').value = o.items;
    document.getElementById('order-total').value = o.total;
    document.getElementById('order-status').value = o.status;
    openModal('order-modal');
}

function renderOrders(){
    const tbody = document.getElementById('orders-table-body');
    tbody.innerHTML = '';

    const q = (document.getElementById('order-search')?.value || '').toLowerCase();
    const from = document.getElementById('orders-from')?.value;
    const to = document.getElementById('orders-to')?.value;

    orders
      .filter(o => {
        if (q){
          const s = `${o.customer} ${o.status} ${o.id}`.toLowerCase();
          if (!s.includes(q)) return false;
        }
        if (from && o.date < from) return false;
        if (to && o.date > to) return false;
        return true;
      })
      .forEach(order => {
        const row = document.createElement('tr');
        row.className = 'draggable-row';
        row.innerHTML = `
            <td style="color: var(--brand); font-weight: 700;">${order.id}</td>
            <td>${order.customer}</td>
            <td>${order.items}</td>
            <td style="font-weight:700;">E£${order.total.toFixed(2)}</td>
            <td><span class="status-badge status-${order.status.toLowerCase()}">${order.status}</span></td>
            <td class="text-gray-600">${order.date}</td>
            <td>
                <div class="flex gap-2">
                    <button class="action-btn" title="View"><i data-lucide="eye" style="color:var(--brand)"></i></button>
                    <button class="action-btn" title="Edit" onclick="editOrder('${order.id}')"><i data-lucide="edit-2" style="color:#d09f30"></i></button>
                    <button class="action-btn" title="Delete" onclick="deleteOrder('${order.id}')"><i data-lucide="trash-2" style="color:#e55353"></i></button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
      });

    lucide.createIcons();
}

let ordersSortKey = null;
function sortOrders(key){
    if (ordersSortKey === key) orders.reverse();
    else {
        orders.sort((a,b)=>{
            if (key==='date') return a.date.localeCompare(b.date);
            if (key==='customer') return a.customer.localeCompare(b.customer);
            if (key==='items' || key==='total') return a[key]-b[key];
            if (key==='status') return a.status.localeCompare(b.status);
            if (key==='id') return a.id.localeCompare(b.id);
            return 0;
        });
        ordersSortKey = key;
    }
    renderOrders();
}

/* --------------- Orders Stats --------------- */
function updateOrderStats(){
    document.getElementById('total-orders').textContent = orders.length;
    document.getElementById('pending-orders').textContent = orders.filter(o => o.status === 'Pending').length;
    document.getElementById('completed-orders').textContent = orders.filter(o => o.status === 'Completed').length;
    document.getElementById('shipped-orders').textContent = orders.filter(o => o.status === 'Shipped').length;
}

/* --------------- USERS --------------- */
let editingUserId = null;
function saveUser(){
    const name = document.getElementById('user-name').value.trim();
    const email = document.getElementById('user-email').value.trim();
    const phone = document.getElementById('user-phone').value.trim();
    const role = document.getElementById('user-role').value;

    if (!name || !email || !phone) { showToast('Complete all fields', 'warn'); return; }

    if (editingUserId){
        const u = users.find(x=>x.id===editingUserId);
        if (u){ u.name=name; u.email=email; u.phone=phone; u.role=role; }
        showToast('User updated', 'success'); editingUserId=null;
    } else {
        const id = `USR-${String(users.length+1).padStart(3,'0')}`;
        users.push({ id, name, email, phone, role, joinDate: new Date().toISOString().split('T')[0], status:'Active' });
        showToast('User added', 'success');
    }

    closeModal('user-modal');
    clearUserForm();
    renderUsers(); updateUserStats();
}

function clearUserForm(){
    document.getElementById('user-name').value='';
    document.getElementById('user-email').value='';
    document.getElementById('user-phone').value='';
    document.getElementById('user-role').value='Customer';
}

function deleteUser(userId){
    if (!confirm('Delete user?')) return;
    users = users.filter(u=>u.id !== userId);
    renderUsers(); updateUserStats();
    showToast('User deleted', 'danger');
}

function editUser(userId){
    const u = users.find(x=>x.id===userId);
    if(!u) return;
    editingUserId = u.id;
    document.getElementById('user-name').value = u.name;
    document.getElementById('user-email').value = u.email;
    document.getElementById('user-phone').value = u.phone;
    document.getElementById('user-role').value = u.role;
    openModal('user-modal');
}

function renderUsers(){
    const tbody = document.getElementById('users-table-body');
    tbody.innerHTML = '';
    users.forEach(user=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="font-weight:700;">${user.name}</td>
            <td><div class="flex items-center gap-2 text-gray-600"><i data-lucide="mail" style="color:var(--brand)"></i>${user.email}</div></td>
            <td><div class="flex items-center gap-2 text-gray-600"><i data-lucide="phone" style="color:var(--brand)"></i>${user.phone}</div></td>
            <td><span class="role-badge role-${user.role.toLowerCase()}">${user.role}</span></td>
            <td class="text-gray-600">${user.joinDate}</td>
            <td><span class="status-badge ${user.status==='Active' ? 'status-completed' : 'status-pending'}">${user.status}</span></td>
            <td>
                <div class="flex gap-2">
                    <button class="action-btn" onclick="editUser('${user.id}')"><i data-lucide="edit-2" style="color:#d09f30"></i></button>
                    <button class="action-btn" onclick="deleteUser('${user.id}')"><i data-lucide="trash-2" style="color:#e55353"></i></button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
    lucide.createIcons();
}

function updateUserStats(){
    document.getElementById('total-users').textContent = users.length;
    document.getElementById('active-users').textContent = users.filter(u=>u.status==='Active').length;
    document.getElementById('admin-users').textContent = users.filter(u=>u.role==='Admin').length;
}

/* --------------- MENU --------------- */
let editingMenuId = null;

function populateMenuCategories(){
    const select = document.getElementById('menu-filter-category');
    const categories = Array.from(new Set(menuItems.map(m=>m.category))).sort();
    select.innerHTML = '<option value="">All Categories</option>';
    categories.forEach(c=>{
        const opt = document.createElement('option'); opt.value = c; opt.textContent = c; select.appendChild(opt);
    });
}

function saveMenuItem(){
    const name = document.getElementById('menu-name').value.trim();
    const category = document.getElementById('menu-category').value.trim() || 'General';
    const price = parseFloat(document.getElementById('menu-price').value) || 0;
    const availability = document.getElementById('menu-availability').value;

    if(!name || price <= 0){ showToast('Complete item name and price', 'warn'); return; }

    if (editingMenuId){
        const it = menuItems.find(x=>x.id===editingMenuId);
        if(it){ it.name=name; it.category=category; it.price=price; it.availability=availability; }
        editingMenuId = null;
        showToast('Menu item updated', 'success');
    } else {
        const id = `MENU-${String(menuItems.length+1).padStart(3,'0')}`;
        menuItems.push({ id, name, category, price, availability });
        showToast('Menu item added', 'success');
    }

    closeModal('menu-modal');
    clearMenuForm();
    renderMenu(); updateMenuStats(); populateMenuCategories(); renderReports(); renderAllCharts();
}

function clearMenuForm(){
    document.getElementById('menu-name').value='';
    document.getElementById('menu-category').value='';
    document.getElementById('menu-price').value='';
    document.getElementById('menu-availability').value='Available';
}

function deleteMenuItem(menuId){
    if (!confirm('Delete menu item?')) return;
    menuItems = menuItems.filter(i=>i.id !== menuId);
    renderMenu(); updateMenuStats(); populateMenuCategories(); renderReports(); renderAllCharts();
    showToast('Menu item deleted', 'danger');
}

function editMenuItem(menuId){
    const it = menuItems.find(x=>x.id===menuId);
    if(!it) return;
    editingMenuId = it.id;
    document.getElementById('menu-name').value = it.name;
    document.getElementById('menu-category').value = it.category;
    document.getElementById('menu-price').value = it.price;
    document.getElementById('menu-availability').value = it.availability;
    openModal('menu-modal');
}

function renderMenu(){
    const tbody = document.getElementById('menu-table-body');
    tbody.innerHTML = '';

    const q = (document.getElementById('menu-search')?.value || '').toLowerCase();
    const cat = document.getElementById('menu-filter-category')?.value;
    const avail = document.getElementById('menu-filter-availability')?.value;

    menuItems
      .filter(item=>{
        if (q && !(`${item.name} ${item.category}`.toLowerCase().includes(q))) return false;
        if (cat && item.category !== cat) return false;
        if (avail && item.availability !== avail) return false;
        return true;
      })
      .forEach(item=>{
        const tr = document.createElement('tr');
        tr.draggable = true;
        tr.dataset.id = item.id;
        tr.className = 'draggable-row';
        tr.innerHTML = `
            <td contenteditable="false" data-field="name" style="font-weight:700;color:var(--brand)">${item.name}</td>
            <td><span class="category-chip" style="${categoryColorStyle(item.category)}">${item.category}</span></td>
            <td style="font-weight:700;">E£${item.price.toFixed(2)}</td>
            <td>
                <span class="${item.availability==='Available' ? 'avail-available' : 'avail-out'} status-badge">${item.availability}</span>
            </td>
            <td>
                <div class="flex gap-2">
                    <button class="action-btn" title="Inline edit" onclick="inlineEditMenu(this)"><i data-lucide="edit-2" style="color:#d09f30"></i></button>
                    <button class="action-btn" title="Edit modal" onclick="editMenuItem('${item.id}')"><i data-lucide="settings" style="color:var(--brand)"></i></button>
                    <button class="action-btn" title="Delete" onclick="deleteMenuItem('${item.id}')"><i data-lucide="trash-2" style="color:#e55353"></i></button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
      });

    // attach drag handlers for reorder
    attachDragHandlers();
    lucide.createIcons();
}

function categoryColorStyle(category){
    // simple deterministic color mapping for categories
    const colors = ['#8B5CF6','#06B6D4','#F97316','#10B981','#EF4444','#6366F1','#F59E0B'];
    let sum = 0; for(let i=0;i<category.length;i++) sum += category.charCodeAt(i);
    const c = colors[sum % colors.length];
    return `background:${c};`;
}

function inlineEditMenu(btn){
    const tr = btn.closest('tr');
    if(!tr) return;
    const nameCell = tr.querySelector('[data-field="name"]');
    if(nameCell.isContentEditable){
        // save inline
        const id = tr.dataset.id;
        const item = menuItems.find(x=>x.id===id);
        item.name = nameCell.textContent.trim();
        showToast('Menu item updated', 'success');
        nameCell.contentEditable = 'false';
        btn.title = 'Inline edit';
    } else {
        nameCell.contentEditable = 'true';
        nameCell.focus();
        btn.title = 'Save inline';
    }
    lucide.createIcons();
}

function updateMenuStats(){
    document.getElementById('total-menu').textContent = menuItems.length;
    document.getElementById('available-menu').textContent = menuItems.filter(m=>m.availability==='Available').length;
    document.getElementById('outofstock-menu').textContent = menuItems.filter(m=>m.availability==='Out of Stock').length;
}

/* Drag & drop reorder */
function attachDragHandlers(){
    const tbody = document.getElementById('menu-table-body');
    let dragSrc = null;

    tbody.querySelectorAll('tr').forEach(row=>{
        row.addEventListener('dragstart', (e)=>{
            dragSrc = row;
            row.style.opacity = '0.6';
            e.dataTransfer.effectAllowed = 'move';
        });
        row.addEventListener('dragend', ()=>{
            row.style.opacity = '';
        });
        row.addEventListener('dragover', (e)=>{ e.preventDefault(); e.dataTransfer.dropEffect='move'; row.classList.add('drag-over'); });
        row.addEventListener('dragleave', ()=>{ row.classList.remove('drag-over'); });
        row.addEventListener('drop', (e)=>{
            e.preventDefault(); row.classList.remove('drag-over');
            if (!dragSrc || dragSrc === row) return;
            const tbody = row.parentElement;
            // swap positions in DOM
            if ([...tbody.children].indexOf(dragSrc) < [...tbody.children].indexOf(row)) {
                row.after(dragSrc);
            } else {
                row.before(dragSrc);
            }
            // update menuItems order to match DOM
            const newOrder = [...tbody.querySelectorAll('tr')].map(r => r.dataset.id);
            menuItems = newOrder.map(id => menuItems.find(it => it.id === id));
            showToast('Menu items reordered', 'success');
            renderMenu(); updateMenuStats(); renderReports(); renderAllCharts();
        });
    });
}

/* --------------- REPORTS & CHARTS --------------- */
let salesLineChart, topBarChart, statusPieChart;

function renderReports(){
    const tbody = document.getElementById('reports-table-body');
    tbody.innerHTML = '';

    // compute total sales from orders (we assume orders.total numeric)
    const filteredOrders = filterOrdersByReportDate();

    let totalSales = 0;
    filteredOrders.forEach(o => totalSales += o.total);

    // compute item sale counts from order.items (simplified model)
    // Here we assume order.items counts as quantity; we allocate quantities to items proportionally if needed.
    const itemSales = {};
    menuItems.forEach(mi => itemSales[mi.name] = 0);
    filteredOrders.forEach(o => {
        // simple approach: attribute all items to first menu item if no mapping
        // Better: store order.items as array in real app
        // we'll just sum 'items' to total sold count distributed evenly
        if (menuItems.length === 0) return;
        // distribute quantity to menu items proportionally by availability (simple equal distribution)
        const per = Math.max(1, Math.round(o.items / menuItems.length));
        menuItems.forEach(mi => itemSales[mi.name] += per);
    });

    // prepare table rows
    menuItems.forEach(item => {
        const soldQty = itemSales[item.name] || 0;
        const revenue = soldQty * item.price;
        const avg = soldQty ? (revenue / soldQty) : 0;
        const row = document.createElement('tr');
        row.innerHTML = `<td>${item.name}</td><td>${item.category}</td><td>${soldQty}</td><td>E£${revenue.toFixed(2)}</td><td>E£${avg.toFixed(2)}</td>`;
        tbody.appendChild(row);
    });

    // stats
    document.getElementById('total-sales').textContent = `E£${totalSales.toFixed(2)}`;
    document.getElementById('total-report-orders').textContent = filteredOrders.length;

    // top item
    let topItem = '-'; let max = 0;
    Object.keys(itemSales).forEach(k=>{
        if (itemSales[k] > max){ max = itemSales[k]; topItem = k; }
    });
    document.getElementById('top-item').textContent = topItem || '-';
}

function filterOrdersByReportDate(){
    const from = document.getElementById('report-from')?.value;
    const to = document.getElementById('report-to')?.value;
    return orders.filter(o=>{
        if (from && o.date < from) return false;
        if (to && o.date > to) return false;
        return true;
    });
}

/* Charts rendering */
function renderAllCharts(){
    renderSalesLineChart();
    renderTopBarChart();
    renderStatusPieChart();
}

function renderSalesLineChart(){
    const ctx = document.getElementById('sales-line-chart').getContext('2d');
    const filteredOrders = filterOrdersByReportDate().sort((a,b)=>a.date.localeCompare(b.date));
    const labels = filteredOrders.map(o=>o.date);
    const data = filteredOrders.map(o=>o.total);

    if (salesLineChart) salesLineChart.destroy();
    salesLineChart = new Chart(ctx, {
        type:'line',
        data:{
            labels,
            datasets:[{
                label:'Sales Over Time (E£)',
                data,
                fill:true,
                borderColor: getComputedStyle(document.documentElement).getPropertyValue('--brand').trim() || '#86987e',
                backgroundColor: 'rgba(134,152,126,0.14)',
                tension:0.25,
                pointRadius:4
            }]
        },
        options:{
            responsive:true,
            plugins:{ legend:{display:true, position:'top'}},
            scales:{ x:{ title:{display:true, text:'Date'} }, y:{ title:{display:true, text:'E£'}, beginAtZero:true } }
        }
    });
}

function renderTopBarChart(){
    const ctx = document.getElementById('top-bar-chart').getContext('2d');
    // compute itemSales
    const filteredOrders = filterOrdersByReportDate();
    const itemSales = {};
    menuItems.forEach(mi => itemSales[mi.name] = 0);
    filteredOrders.forEach(o => {
        if (menuItems.length === 0) return;
        const per = Math.max(1, Math.round(o.items / menuItems.length));
        menuItems.forEach(mi => itemSales[mi.name] += per);
    });
    // take top 6
    const entries = Object.entries(itemSales).sort((a,b)=>b[1]-a[1]).slice(0,6);
    const labels = entries.map(e=>e[0]); const data = entries.map(e=>e[1]);

    if (topBarChart) topBarChart.destroy();
    topBarChart = new Chart(ctx, {
        type:'bar',
        data:{ labels, datasets:[{ label:'Top Selling Items (qty)', data, backgroundColor: labels.map(()=>randomRGBA(0.7)) }]},
        options:{ responsive:true, plugins:{legend:{display:false}}, scales:{ y:{ beginAtZero:true } } }
    });
}

function renderStatusPieChart(){
    const ctx = document.getElementById('status-pie-chart').getContext('2d');
    const filteredOrders = filterOrdersByReportDate();
    const counts = { Pending:0, Shipped:0, Completed:0 };
    filteredOrders.forEach(o => { counts[o.status] = (counts[o.status]||0)+1; });

    if (statusPieChart) statusPieChart.destroy();
    statusPieChart = new Chart(ctx, {
        type:'pie',
        data:{
            labels: Object.keys(counts),
            datasets:[{ data: Object.values(counts), backgroundColor: ['#d09f30','#2b9bd6','#2ea44f'] }]
        },
        options:{ responsive:true, plugins:{ legend:{position:'bottom'} } }
    });
}

function randomRGBA(alpha=0.6){
    const r = Math.floor(Math.random()*200+20), g = Math.floor(Math.random()*200+20), b = Math.floor(Math.random()*200+20);
    return `rgba(${r},${g},${b},${alpha})`;
}

/* --------------- Reports filter helpers --------------- */
function filterReports(){
    renderReports();
    renderAllCharts();
    showToast('Reports updated', 'success');
}
function clearReportFilter(){
    document.getElementById('report-from').value=''; document.getElementById('report-to').value='';
    filterReports();
}

/* --------------- Init --------------- */
document.addEventListener('DOMContentLoaded', () => {
    // default open orders page
    showPage('orders');

    // render data
    renderOrders(); updateOrderStats();
    renderUsers(); updateUserStats();
    renderMenu(); updateMenuStats(); populateMenuCategories();
    renderReports(); renderAllCharts();

    // init lucide icons again (for dynamism)
    lucide.createIcons();

    // clicking outside modal on backdrop handled by markup
});
