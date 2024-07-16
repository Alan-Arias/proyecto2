@if(isset($Usuarios) && !empty($Usuarios->id))
@else
    <form action="{{ url('/users/registrarUsuario') }}" method="POST">
        @csrf
        <table>
            <tr>
                <td>Carnet</td>
                <td><input type="text" name="id" id="id" placeholder="Carnet de Identidad"></td>
            </tr>
            <tr>
                <td>Nombre de Usuario</td>
                <td><input type="text" id="user" name="user" placeholder="Nombre de Usuario"></td>
            </tr>
            <tr>
                <td>Contraseña</td>
                <td><input type="password" id="pass" name="pass"></td>
            </tr>
            <tr>
                <td>Tipo de Usuario</td>
                <td>
                    <select name="tipo_user" id="tipo_user">
                        <option value="2">Cliente</option>
                        <option value="1">Trabajador</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Descripcion Usuario</td>
                <td><input type="text" id="desc_user" name="desc_user" placeholder="Descripcion del Usuario"></td>
            </tr>
            <tr>
                <td>Nombres</td>
                <td><input type="text" id="nombres" name="nombres"></td>
            </tr>
            <tr>
                <td>Edad</td>
                <td><input type="text" id="edad" name="edad"></td>
            </tr>
            <tr>
                <td>Sexo</td>
                <td>
                    <select name="sexo" id="sexo">
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><button type="submit" class="btn btn-success">Guardar</button></td>
            </tr>
        </table>
    </form>
    @if (session('agregar'))
        <div class="alert alert-success mt-3">
            <p>{{ session('agregar') }}</p>
        </div>
    @endif
@endif

