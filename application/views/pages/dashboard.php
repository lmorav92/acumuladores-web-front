<!-- Dashboard Principal - Acumuladores Pro -->
<div class="row">
  <div class="col-12">
    <h1 class="page-title">
      <i class="zmdi zmdi-view-dashboard" style="color: #FF6B35;"></i> 
      Panel de Control
    </h1>
    <p style="color: #CCCCCC; margin-bottom: 30px;">
      Bienvenido, <strong style="color: #FF6B35;"><?= isset($user['nombre']) ? $user['nombre'] : 'Administrador' ?></strong>
      | Fecha: <?= date('d/m/Y') ?>
    </p>
  </div>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row">
  <!-- Ventas del Día -->
  <div class="col-12 col-md-6 col-lg-3">
    <div class="stats-card">
      <div class="stats-icon">
        <i class="zmdi zmdi-money"></i>
      </div>
      <div class="stats-value">$12,450</div>
      <div class="stats-label">Ventas Hoy</div>
      <small style="color: #28A745;">
        <i class="zmdi zmdi-trending-up"></i> +15% vs ayer
      </small>
    </div>
  </div>

  <!-- Productos en Stock -->
  <div class="col-12 col-md-6 col-lg-3">
    <div class="stats-card">
      <div class="stats-icon" style="background: linear-gradient(135deg, #2196F3 0%, #0D47A1 100%);">
        <i class="zmdi zmdi-storage"></i>
      </div>
      <div class="stats-value" style="color: #2196F3;">1,234</div>
      <div class="stats-label">Productos en Stock</div>
      <small style="color: #FFC107;">
        <i class="zmdi zmdi-alert-triangle"></i> 15 con stock bajo
      </small>
    </div>
  </div>

  <!-- Ventas del Mes -->
  <div class="col-12 col-md-6 col-lg-3">
    <div class="stats-card">
      <div class="stats-icon" style="background: linear-gradient(135deg, #4CAF50 0%, #1B5E20 100%);">
        <i class="zmdi zmdi-chart"></i>
      </div>
      <div class="stats-value" style="color: #4CAF50;">$156,780</div>
      <div class="stats-label">Ventas del Mes</div>
      <small style="color: #28A745;">
        <i class="zmdi zmdi-trending-up"></i> +23% vs mes anterior
      </small>
    </div>
  </div>

  <!-- Clientes Activos -->
  <div class="col-12 col-md-6 col-lg-3">
    <div class="stats-card">
      <div class="stats-icon" style="background: linear-gradient(135deg, #9C27B0 0%, #4A148C 100%);">
        <i class="zmdi zmdi-accounts"></i>
      </div>
      <div class="stats-value" style="color: #9C27B0;">348</div>
      <div class="stats-label">Clientes Activos</div>
      <small style="color: #28A745;">
        <i class="zmdi zmdi-plus"></i> 12 nuevos esta semana
      </small>
    </div>
  </div>
</div>

<!-- Gráficos y Tablas -->
<div class="row mt-4">
  <!-- Ventas por Categoría -->
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header">
        <i class="zmdi zmdi-chart-donut"></i> Ventas por Categoría
      </div>
      <div class="card-body">
        <canvas id="ventasCategoria" height="100"></canvas>
      </div>
    </div>
  </div>

  <!-- Productos Más Vendidos -->
  <div class="col-12 col-lg-4">
    <div class="card">
      <div class="card-header">
        <i class="zmdi zmdi-star"></i> Top 5 Productos
      </div>
      <div class="card-body">
        <div class="list-group list-group-flush">
          <div class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-color: rgba(255, 107, 53, 0.2);">
            <div>
              <strong style="color: #F5F5F5;">Batería 12V 45Ah</strong>
              <br><small style="color: #999;">Categoría: Automóviles</small>
            </div>
            <span class="badge badge-primary badge-pill">125</span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-color: rgba(255, 107, 53, 0.2);">
            <div>
              <strong style="color: #F5F5F5;">Batería 12V 65Ah</strong>
              <br><small style="color: #999;">Categoría: Camionetas</small>
            </div>
            <span class="badge badge-primary badge-pill">98</span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-color: rgba(255, 107, 53, 0.2);">
            <div>
              <strong style="color: #F5F5F5;">Batería 6V Moto</strong>
              <br><small style="color: #999;">Categoría: Motos</small>
            </div>
            <span class="badge badge-primary badge-pill">76</span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-color: rgba(255, 107, 53, 0.2);">
            <div>
              <strong style="color: #F5F5F5;">Batería AGM 12V</strong>
              <br><small style="color: #999;">Categoría: Premium</small>
            </div>
            <span class="badge badge-primary badge-pill">54</span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-color: rgba(255, 107, 53, 0.2);">
            <div>
              <strong style="color: #F5F5F5;">Batería Gel 12V</strong>
              <br><small style="color: #999;">Categoría: Solar</small>
            </div>
            <span class="badge badge-primary badge-pill">42</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Alertas de Stock y Ventas Recientes -->
<div class="row mt-4">
  <!-- Alertas de Stock Bajo -->
  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-header">
        <i class="zmdi zmdi-alert-triangle"></i> Alertas de Stock
      </div>
      <div class="card-body" style="max-height: 400px; overflow-y: auto;">
        <div class="alert alert-warning">
          <strong><i class="zmdi zmdi-battery-alert"></i> Batería 12V 45Ah Duralast</strong>
          <br>Stock actual: 5 unidades (Mínimo: 20)
          <br><a href="#" class="btn btn-sm btn-primary mt-2">Realizar Pedido</a>
        </div>
        <div class="alert alert-warning">
          <strong><i class="zmdi zmdi-battery-alert"></i> Batería 12V 65Ah Premium</strong>
          <br>Stock actual: 8 unidades (Mínimo: 15)
          <br><a href="#" class="btn btn-sm btn-primary mt-2">Realizar Pedido</a>
        </div>
        <div class="alert alert-danger">
          <strong><i class="zmdi zmdi-battery-unknown"></i> Batería Moto 6V YTX</strong>
          <br>Stock actual: 2 unidades (Mínimo: 10)
          <br><a href="#" class="btn btn-sm btn-primary mt-2">Realizar Pedido URGENTE</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Últimas Ventas -->
  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-header">
        <i class="zmdi zmdi-time-restore"></i> Últimas Ventas
      </div>
      <div class="card-body" style="max-height: 400px; overflow-y: auto;">
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Producto</th>
                <th>Total</th>
                <th>Hora</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1245</td>
                <td>Juan Pérez</td>
                <td>Batería 12V 45Ah</td>
                <td style="color: #FF6B35; font-weight: 600;">$89.99</td>
                <td>10:35 AM</td>
              </tr>
              <tr>
                <td>1244</td>
                <td>María López</td>
                <td>Batería AGM 12V 65Ah</td>
                <td style="color: #FF6B35; font-weight: 600;">$145.00</td>
                <td>10:28 AM</td>
              </tr>
              <tr>
                <td>1243</td>
                <td>Carlos Mendez</td>
                <td>Batería Moto 6V</td>
                <td style="color: #FF6B35; font-weight: 600;">$45.50</td>
                <td>10:15 AM</td>
              </tr>
              <tr>
                <td>1242</td>
                <td>Ana García</td>
                <td>Batería Gel Solar 12V</td>
                <td style="color: #FF6B35; font-weight: 600;">$195.00</td>
                <td>9:58 AM</td>
              </tr>
              <tr>
                <td>1241</td>
                <td>Roberto Silva</td>
                <td>Batería 12V 65Ah x2</td>
                <td style="color: #FF6B35; font-weight: 600;">$320.00</td>
                <td>9:45 AM</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Script para gráficos -->
<script>
$(document).ready(function() {
  // Gráfico de Ventas por Categoría
  var ctx = document.getElementById('ventasCategoria').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Automóviles', 'Camionetas', 'Motos', 'Solar', 'Marina', 'Industrial'],
      datasets: [{
        label: 'Ventas ($)',
        data: [45000, 38000, 15000, 22000, 12000, 28000],
        backgroundColor: [
          'rgba(255, 107, 53, 0.8)',
          'rgba(255, 140, 97, 0.8)',
          'rgba(255, 184, 77, 0.8)',
          'rgba(33, 150, 243, 0.8)',
          'rgba(76, 175, 80, 0.8)',
          'rgba(156, 39, 176, 0.8)'
        ],
        borderColor: [
          '#FF6B35',
          '#FF8C61',
          '#FFB84D',
          '#2196F3',
          '#4CAF50',
          '#9C27B0'
        ],
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: '#F5F5F5',
            callback: function(value) {
              return '$' + value.toLocaleString();
            }
          },
          grid: {
            color: 'rgba(255, 107, 53, 0.1)'
          }
        },
        x: {
          ticks: {
            color: '#F5F5F5'
          },
          grid: {
            color: 'rgba(255, 107, 53, 0.1)'
          }
        }
      },
      plugins: {
        legend: {
          labels: {
            color: '#F5F5F5'
          }
        }
      }
    }
  });
});
</script>

<style>
/* Estilos adicionales para el dashboard */
.list-group-item {
  transition: all 0.3s ease;
}

.list-group-item:hover {
  background: rgba(255, 107, 53, 0.1) !important;
  transform: translateX(5px);
}

.badge-pill {
  padding: 0.5em 0.8em;
  font-size: 12px;
  font-weight: 600;
}

/* Animación de las tarjetas de stats */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.stats-card {
  animation: fadeInUp 0.6s ease-out;
}

.stats-card:nth-child(1) { animation-delay: 0.1s; }
.stats-card:nth-child(2) { animation-delay: 0.2s; }
.stats-card:nth-child(3) { animation-delay: 0.3s; }
.stats-card:nth-child(4) { animation-delay: 0.4s; }

/* Responsive */
@media (max-width: 768px) {
  .stats-card {
    margin-bottom: 15px;
  }
  
  .page-title {
    font-size: 22px;
  }
}
</style>
