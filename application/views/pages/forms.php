<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4>Forms</h4>
            </div>
            <div class="card-body">
                <p>Esta es la página de formularios. Cargada dinámicamente sin recargar la página.</p>
                
                <form>
                    <div class="form-group">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Ingresa tu nombre">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="correo@ejemplo.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" placeholder="+1 234 567 8900">
                    </div>
                    
                    <div class="form-group">
                        <label for="mensaje">Mensaje</label>
                        <textarea class="form-control" id="mensaje" rows="4" placeholder="Escribe tu mensaje aquí"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="pais">País</label>
                        <select class="form-control" id="pais">
                            <option>Selecciona un país</option>
                            <option>México</option>
                            <option>Estados Unidos</option>
                            <option>España</option>
                            <option>Colombia</option>
                            <option>Argentina</option>
                        </select>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="terminos">
                        <label class="form-check-label" for="terminos">
                            Acepto los términos y condiciones
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-3">Enviar</button>
                    <button type="reset" class="btn btn-secondary mt-3">Limpiar</button>
                </form>
            </div>
        </div>
    </div>
</div>
