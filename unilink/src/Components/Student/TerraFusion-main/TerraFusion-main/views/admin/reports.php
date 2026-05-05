<?php
$pageTitle = 'Reports - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">Reports & Analytics</h1>

<!-- Date Range Filter -->
<div class="card bg-card border-gold mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('admin/reports') ?>">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-gold w-100">Generate Report</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Sales Summary -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-card border-gold">
            <div class="card-body text-center">
                <h3 class="text-gold"><?= $salesSummary->total_orders ?? 0 ?></h3>
                <p class="text-muted mb-0">Total Orders</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-card border-gold">
            <div class="card-body text-center">
                <h3 class="text-gold"><?= format_currency($salesSummary->total_revenue ?? 0) ?></h3>
                <p class="text-muted mb-0">Total Revenue</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-card border-gold">
            <div class="card-body text-center">
                <h3 class="text-gold"><?= format_currency($salesSummary->average_order_value ?? 0) ?></h3>
                <p class="text-muted mb-0">Avg Order Value</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-card border-gold">
            <div class="card-body text-center">
                <h3 class="text-gold"><?= $salesSummary->unique_customers ?? 0 ?></h3>
                <p class="text-muted mb-0">Unique Customers</p>
            </div>
        </div>
    </div>
</div>

<!-- Popular Items -->
<div class="card bg-card border-gold mb-4">
    <div class="card-header bg-gold text-dark">
        <h5 class="mb-0">Popular Items</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity Sold</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($popularItems as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item->name) ?></td>
                            <td><?= $item->total_quantity ?></td>
                            <td class="text-gold"><?= format_currency($item->total_revenue) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Peak Ordering Times -->
<div class="card bg-card border-gold mb-4">
    <div class="card-header bg-gold text-dark">
        <h5 class="mb-0">Peak Ordering Times</h5>
    </div>
    <div class="card-body">
        <canvas id="peakTimesChart" height="100"></canvas>
    </div>
</div>

<script>
// Peak Times Chart
const ctx = document.getElementById('peakTimesChart').getContext('2d');
const peakTimesChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?= implode(',', array_map(function($t) { return $t->order_hour . ':00'; }, $peakTimes)) ?>],
        datasets: [{
            label: 'Orders',
            data: [<?= implode(',', array_map(function($t) { return $t->order_count; }, $peakTimes)) ?>],
            backgroundColor: '#C8A252',
            borderColor: '#B89232',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { color: '#F0F0F0' }
            }
        },
        scales: {
            x: {
                grid: { color: 'rgba(200, 162, 82, 0.1)' },
                ticks: { color: '#F0F0F0' }
            },
            y: {
                grid: { color: 'rgba(200, 162, 82, 0.1)' },
                ticks: { color: '#F0F0F0' }
            }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

