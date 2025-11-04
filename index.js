function init() {}

var imgIcono = document.getElementById("imgIcono");
  var userRolUsuario = document.getElementById("user_rol_usuario");
  var lblTitulo = document.getElementById("lbltitulo");
  var usuario = document.getElementById("usuario");

  document.getElementById('role-select').addEventListener('change', function() {
    var selectedRole = this.value;

    switch (selectedRole) {
      case "coordinador":
        userRolUsuario.value = "2";
        lblTitulo.innerHTML = "Acceso Coordinador";
        usuario.placeholder = "Usuario Coordinador";
        imgIcono.src = "public/img/iconoCoor.png";
        break;
      case "conductor":
        userRolUsuario.value = "1";
        lblTitulo.innerHTML = "Acceso Operador";
        usuario.placeholder = "Usuario Operador";
        imgIcono.src = "public/img/iconocond.png";
        break;
      case "verificador":
        userRolUsuario.value = "3";
        lblTitulo.innerHTML = "Acceso Verificador";
        usuario.placeholder = "Usuario Verificador";
        imgIcono.src = "public/img/iconoVer.png";
        break;
      case "administrador":
        userRolUsuario.value = "4";
        lblTitulo.innerHTML = "Acceso Administrador";
        usuario.placeholder = "Usuario Administrador";
        imgIcono.src = "public/img/admin.png";
        break;
      case "rrhh":
        userRolUsuario.value = "5";
        lblTitulo.innerHTML = "Acceso RRHH";
        usuario.placeholder = "Usuario RRHH";
        imgIcono.src = "public/img/iconoHHRR.png";
        break;
      case "gerencia":
        userRolUsuario.value = "6";
        lblTitulo.innerHTML = "Acceso Gerencia";
        usuario.placeholder = "Usuario Gerencia";
        imgIcono.src = "public/img/iconoGerencia.png";
        break;
      case "residente":
        userRolUsuario.value = "7";
        lblTitulo.innerHTML = "Acceso Residente";
        usuario.placeholder = "Usuario Residente";
        imgIcono.src = "public/img/image.png";
        break;
      case "inspector":
        userRolUsuario.value = "8";
        lblTitulo.innerHTML = "Acceso Inspector";
        usuario.placeholder = "Usuario Inspector";
        imgIcono.src = "public/img/inspector.png";
        break;
      case "almacen":
        userRolUsuario.value = "9";
        lblTitulo.innerHTML = "Acceso Almacen";
        usuario.placeholder = "Usuario Almacen";
        imgIcono.src = "public/img/iconoAlmacen.png";
        break;
        case "conductor_inspector":
          userRolUsuario.value = "10";
          lblTitulo.innerHTML = "Acceso Cond-Inspec";
          usuario.placeholder = "Usuario Conductor-Inspctor";
          imgIcono.src = "public/img/iconoAlmacen.png";
          break;
      default:
        userRolUsuario.value = "";
        lblTitulo.innerHTML = "Selecciona un rol del menú desplegable";
        usuario.placeholder = "";
        imgIcono.src = "";
        break;
    }
  });

  

init();
