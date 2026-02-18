<!--Start footer-->
<footer class="footer">
  <div class="container">
    <div class="text-center">
      <p class="mb-2">
        <i class="zmdi zmdi-battery-flash" style="color: #FF6B35; font-size: 20px;"></i>
        <strong>Acumuladores Pro</strong> - Sistema de Gestión Integral
      </p>
      <p class="mb-0" style="font-size: 13px; color: #CCCCCC;">
        Copyright © <?= date('Y') ?> 
        <span style="color: #FF6B35; font-weight: 600;">Luis+ Tec</span>. 
        Todos los derechos reservados.
      </p>
      <p class="mt-2 mb-0" style="font-size: 11px; color: #999;">
        Versión 1.0.0 | Energizando tu negocio
      </p>
    </div>
  </div>
</footer>
<!--End footer-->

<style>
/* ============================================
   FOOTER - TEMA ACUMULADORES
   Naranja y Grises
   ============================================ */

.footer {
  background: linear-gradient(135deg, #1E1E1E 0%, #2A2A2A 100%);
  padding: 25px 0;
  border-top: 3px solid #FF6B35;
  margin-top: 50px;
  box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.3);
  position: relative;
}

.footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, 
    transparent 0%, 
    #FF6B35 25%, 
    #FFB84D 50%, 
    #FF6B35 75%, 
    transparent 100%
  );
}

.footer p {
  color: #F5F5F5;
  margin-bottom: 5px;
}

.footer strong {
  color: #FF6B35;
  font-weight: 700;
  letter-spacing: 1px;
}

.footer i.zmdi-battery-flash {
  animation: battery-pulse 2s ease-in-out infinite;
  display: inline-block;
  margin-right: 5px;
}

@keyframes battery-pulse {
  0%, 100% {
    transform: scale(1);
    filter: drop-shadow(0 0 5px rgba(255, 107, 53, 0.5));
  }
  50% {
    transform: scale(1.1);
    filter: drop-shadow(0 0 10px rgba(255, 107, 53, 0.8));
  }
}

/* Links en el footer (si decides agregar) */
.footer a {
  color: #FF6B35;
  text-decoration: none;
  transition: all 0.3s ease;
}

.footer a:hover {
  color: #FF8C61;
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
  .footer {
    padding: 20px 0;
  }
  
  .footer p {
    font-size: 12px !important;
  }
  
  .footer i.zmdi-battery-flash {
    font-size: 18px !important;
  }
}
</style>
