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
                <a class="active" href="#">Libros</a>
                <a href="#">Categories</a>
                <a href="#">Autores</a>
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
    <div class="category" style="margin-bottom: 32px; ">
        <label style="font-weight: bold;">Por categoria</label>
        <button class="btn btn-default" id="addBook">Añadir libro</button>
    </div>

    <div id="category-container"></div>


    <!-- books -->
    <div id="book-container"></div>

</div>

<div class="content">
    <div class="container">
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
