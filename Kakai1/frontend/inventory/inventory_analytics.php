<?php include '../includes/links.php'; ?>
<div class="container mt-4">
  <h3>Inventory Analytics</h3>
  <div class="row">
    <div class="col-md-6">
      <h5>Low Stock</h5>
      <ul id="lowStock" class="list-group"></ul>
    </div>
    <div class="col-md-6">
      <h5>Stock Levels</h5>
      <canvas id="stockChart" height="200"></canvas>
    </div>
  </div>
  <hr>
  <h5>Forecast (avg monthly out)</h5>
  <div id="forecastArea"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
async function loadLow() {
  const r = await fetch('/backend/inventory/low_stock_alerts.php');
  const j = await r.json();
  const ul = document.getElementById('lowStock');
  ul.innerHTML = '';
  if (j.success) {
    j.data.forEach(it=>{
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.innerHTML = `<div><strong>${it.item_name}</strong><br/><small>Cat ID: ${it.category_id}</small></div><span class="badge bg-danger rounded-pill">${it.quantity}</span>`;
      ul.appendChild(li);
    });
  }
}

async function loadChart() {
  const r = await fetch('/backend/inventory/get_inventory.php');
  const j = await r.json();
  if (!j.success) return;
  const labels = j.data.map(x=>x.item_name);
  const data = j.data.map(x=>x.quantity);
  const ctx = document.getElementById('stockChart').getContext('2d');
  new Chart(ctx, {type:'bar', data:{labels, datasets:[{label:'Quantity', data}]}, options:{responsive:true}});
}

async function loadForecast() {
  const r = await fetch('/backend/inventory/forecast_inventory.php');
  const j = await r.json();
  const area = document.getElementById('forecastArea');
  if (!j.success) { area.innerText = 'No forecast data'; return; }
  area.innerHTML = '';
  for (const [itemId, info] of Object.entries(j.forecast)) {
    const div = document.createElement('div');
    div.className = 'card p-2 mb-2';
    div.innerHTML = `<strong>${itemId}</strong>
      <div>Avg monthly out: ${info.avg_monthly_out}</div>
      <div>Forecast next 3 months: ${info.forecast_next_3_months.join(', ')}</div>`;
    area.appendChild(div);
  }
}

loadLow(); loadChart(); loadForecast();
</script>
