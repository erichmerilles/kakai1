<?php include '../includes/links.php'; ?>
<div class="container mt-4">
  <h3>Inventory Movements</h3>
  <table class="table table-striped" id="movTable"><thead><tr><th>ID</th><th>Item</th><th>Type</th><th>Qty</th><th>Remarks</th><th>Date</th></tr></thead><tbody></tbody></table>
</div>

<script>
async function loadMov() {
  const r = await fetch('/backend/inventory/get_movements.php');
  const j = await r.json();
  const tb = document.querySelector('#movTable tbody');
  tb.innerHTML = '';
  if (!j.success) return;
  j.data.forEach(m=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${m.movement_id}</td><td>${m.item_name}</td><td>${m.type}</td><td>${m.quantity}</td><td>${m.remarks}</td><td>${m.created_at}</td>`;
    tb.appendChild(tr);
  });
}
loadMov();
</script>
