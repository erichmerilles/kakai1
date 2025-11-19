<?php include '../includes/links.php'; ?>
<div class="container mt-4">
  <h3>Inventory Manage</h3>
  <a href="inventory_analytics.php" class="btn btn-secondary mb-2">Analytics</a>
  <button class="btn btn-primary mb-2" onclick="showAdd()">Add Item</button>
  <table class="table table-bordered" id="invTable"><thead><tr><th>Name</th><th>Qty</th><th>Reorder</th><th>Actions</th></tr></thead><tbody></tbody></table>
</div>

<!-- Item Modal -->
<div class="modal fade" id="itemModal"><div class="modal-dialog"><form id="itemForm" class="modal-content p-3">
  <h5 id="modalTitle">Item</h5>
  <input type="hidden" name="item_id" />
  <label>Name</label><input name="item_name" class="form-control mb-2" required/>
  <label>Quantity</label><input name="quantity" type="number" class="form-control mb-2" value="0"/>
  <label>Unit Price</label><input name="unit_price" type="number" step="0.01" class="form-control mb-2" value="0"/>
  <label>Reorder Level</label><input name="reorder_level" type="number" class="form-control mb-2" value="10"/>
  <div class="text-end"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button><button class="btn btn-primary">Save</button></div>
</form></div></div>

<!-- Movement Modal -->
<div class="modal fade" id="moveModal"><div class="modal-dialog"><form id="moveForm" class="modal-content p-3">
  <h5>Stock Movement</h5>
  <input type="hidden" name="item_id" />
  <label>Type</label><select name="type" class="form-control mb-2"><option value="IN">IN</option><option value="OUT">OUT</option></select>
  <label>Quantity</label><input name="quantity" type="number" class="form-control mb-2" value="1"/>
  <label>Remarks</label><input name="remarks" class="form-control mb-2"/>
  <div class="text-end"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button><button class="btn btn-primary">Apply</button></div>
</form></div></div>

<script>
const base = '/backend/inventory';
async function load() {
  const r = await fetch(base + '/get_inventory.php');
  const j = await r.json();
  const tb = document.querySelector('#invTable tbody');
  tb.innerHTML = '';
  if (!j.success) return;
  j.data.forEach(it=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${it.item_name}</td><td>${it.quantity}</td><td>${it.reorder_level}</td>
      <td>
        <button class="btn btn-sm btn-primary" onclick='openEdit(${it.item_id}, ${JSON.stringify(it)})'>Edit</button>
        <button class="btn btn-sm btn-warning" onclick="openMove(${it.item_id})">Move</button>
        <button class="btn btn-sm btn-danger" onclick="del(${it.item_id})">Delete</button>
      </td>`;
    tb.appendChild(tr);
  });
}

function showAdd(){
  const f = document.getElementById('itemForm');
  f.reset(); f.item_id.value = '';
  document.getElementById('modalTitle').innerText = 'Add Item';
  new bootstrap.Modal(document.getElementById('itemModal')).show();
}

function openEdit(id, obj){
  const f = document.getElementById('itemForm');
  f.item_id.value = id;
  f.item_name.value = obj.item_name;
  f.quantity.value = obj.quantity;
  f.unit_price.value = obj.unit_price;
  f.reorder_level.value = obj.reorder_level;
  document.getElementById('modalTitle').innerText = 'Edit Item';
  new bootstrap.Modal(document.getElementById('itemModal')).show();
}

document.getElementById('itemForm').addEventListener('submit', async e=>{
  e.preventDefault();
  const fd = Object.fromEntries(new FormData(e.target).entries());
  const url = fd.item_id ? base + '/update_item.php' : base + '/add_item.php';
  const res = await fetch(url, {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(fd)});
  const j = await res.json();
  if (j.success) { bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide(); load(); }
  else alert(j.message);
});

function openMove(id){
  const f = document.getElementById('moveForm');
  f.reset(); f.item_id.value = id;
  new bootstrap.Modal(document.getElementById('moveModal')).show();
}

document.getElementById('moveForm').addEventListener('submit', async e=>{
  e.preventDefault();
  const fd = Object.fromEntries(new FormData(e.target).entries());
  const res = await fetch(base + '/movement.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(fd)});
  const j = await res.json();
  if (j.success) { bootstrap.Modal.getInstance(document.getElementById('moveModal')).hide(); load(); }
  else alert(j.message);
});

async function del(id){
  if (!confirm('Delete?')) return;
  const r = await fetch(base + '/delete_item.php?item_id=' + id);
  const j = await r.json();
  if (j.success) load(); else alert(j.message);
}

load();
</script>
