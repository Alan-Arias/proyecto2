@if(isset($Cliente) && !empty($Cliente->id))
@else
    <form action="{{ url('/registrar') }}" method="POST">
        @csrf
        <table>
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
                <td>Usuario</td>
                <td>
                    <select id="user" name="user">
                        @foreach ($Usuarios as $usuario)
                        @if (strpos($usuario->desc_user, 'cliente') !== false)
                            <option value="{{ $usuario->id }}">{{ $usuario->user }} - {{ $usuario->desc_user }}</option>
                        @endif
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td><input type="submit" class="btn btn-success" value="Guardar"></td>
            </tr>
        </table>
    </form>
    @if (session('agregar'))
        <div class="alert alert-success mt-3">
            <p>{{ session('agregar') }}</p>
        </div>
    @endif
@endif
