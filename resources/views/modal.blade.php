<div class="container-modal"></div>
<div class="modal">
    <div class="header">
        <a href="#" class="cancel">X</a>
    </div>
    <div class="content">
        <form data-book-id="*">
            <label for="name">Book name</label>
            <input name="name" type="text" placeholder="Book name"/>


            <label for="edition">Book edition</label>
            <input name="edition" type="text" placeholder="Book edition"/>

            <label for="description">Book description</label>
            <textarea name="description"  placeholder="Book description"> </textarea>

            <label for="photo">Book photo</label>
            <input type="file" name="photo" accept="image/*"/>

            <label for="genres">Book genres</label>
            <select name="genres" multiple></select>

            <label for="authors">Book authors</label>
            <select name="authors" multiple></select>


        </form>
    </div>
    <div class="footer">
        <button class="btn btn-success">Guardar cambios</button>
        <button class="btn btn-danger cancel">Cancelar</button>
    </div>
</div>
