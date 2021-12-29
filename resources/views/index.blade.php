<!DOCTYPE html>
<html lang="es">
<head>
    @include('header')
</head>
<body>
<div id="navbar">
    <div class="container">
        <div class="navInner">
            <div class="menuItems">
                <div class="title" href="#" style="">
                    <span>books</span>
                    <span>catalog</span>
                </div>
                <a class="active" href="#" data-type="book">Libros</a>
                <a href="#" data-type="genre">Categorias</a>
                <a href="#" data-type="author">Autores</a>
            </div>
            <div class="avatar">
                <img src="{{ asset('imgs/avatar.png') }}" alt="profile img">
                <label>Hi,</label>
                <strong>Name Surname</strong>
            </div>
        </div>
    </div>
</div>


<div class="container">

    <div class="sub-container active" data-target="book">
        <div class="sub-header" >
            <label>Por categoria</label>
            <button class="btn btn-default add-element">Añadir libro</button>
        </div>

        <!-- categories -->
        <div id="category-container"></div>
        <!-- books -->
        <div class="card-container"></div>
    </div>

    <div class="sub-container" data-target="author">
        <div class="sub-header">
            <label>Autores</label>

            <button class="btn btn-default add-element">Añadir autor</button>
        </div>
        <div class="card-container"></div>

    </div>

    <div class="sub-container" data-target="genre">
        <div class="sub-header">
            <label>Categorias</label>
            <button class="btn btn-default add-element">Añadir categoría</button>
        </div>
        <div class="card-container"></div>

    </div>

</div>

@include('modal')
<script type="application/javascript">
    const DEFAULT_GENRE = {
        id: '*',
        name: "Cualquiera",
        photo: "{{ asset("imgs/genre/Cualquiera.png") }}"
    };
</script>

<script src="{{ asset('/js/app.js')  }}"></script>
</body>
</html>
