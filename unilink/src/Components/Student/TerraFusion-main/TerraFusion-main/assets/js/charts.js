// Chart.js helper for TerraFusion admin reports
// Assumes Chart.js is already loaded on the page

/**
 * Render a line chart
 * @param {string} canvasId
 * @param {Array<string>} labels
 * @param {Array<number>} data
 * @param {string} label
 */
function renderLineChart(canvasId, labels, data, label = 'Series') {
  const ctx = document.getElementById(canvasId);
  if (!ctx || typeof Chart === 'undefined') return;

  new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label,
          data,
          borderColor: '#C8A252',
          backgroundColor: 'rgba(200, 162, 82, 0.2)',
          tension: 0.3,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { labels: { color: '#F0F0F0' } },
      },
      scales: {
        x: { grid: { color: 'rgba(200, 162, 82, 0.1)' }, ticks: { color: '#F0F0F0' } },
        y: { grid: { color: 'rgba(200, 162, 82, 0.1)' }, ticks: { color: '#F0F0F0' } },
      },
    },
  });
}

/**
 * Render a bar chart
 * @param {string} canvasId
 * @param {Array<string>} labels
 * @param {Array<number>} data
 * @param {string} label
 */
function renderBarChart(canvasId, labels, data, label = 'Series') {
  const ctx = document.getElementById(canvasId);
  if (!ctx || typeof Chart === 'undefined') return;

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label,
          data,
          backgroundColor: '#C8A252',
          borderColor: '#B89232',
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { labels: { color: '#F0F0F0' } },
      },
      scales: {
        x: { grid: { color: 'rgba(200, 162, 82, 0.1)' }, ticks: { color: '#F0F0F0' } },
        y: { grid: { color: 'rgba(200, 162, 82, 0.1)' }, ticks: { color: '#F0F0F0' } },
      },
    },
  });
}

// Export to global scope
window.renderLineChart = renderLineChart;
window.renderBarChart = renderBarChart;


