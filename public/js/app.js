(function (){
    var require = function (file, callback) {
        callback = callback ||
            function () {
            };
        var filenode;
        var jsfile_extension = /(.js)$/i;
        var cssfile_extension = /(.css)$/i;

        if (jsfile_extension.test(file)) {
            filenode = document.createElement('script');
            filenode.src = file;
            // IE
            filenode.onreadystatechange = function () {
                if (filenode.readyState === 'loaded' || filenode.readyState === 'complete') {
                    filenode.onreadystatechange = null;
                    callback();
                }
            };
            // others
            filenode.onload = function () {
                callback();
            };
            document.head.appendChild(filenode);
        } else if (cssfile_extension.test(file)) {
            filenode = document.createElement('link');
            filenode.rel = 'stylesheet';
            filenode.type = 'text/css';
            filenode.href = file;
            document.head.appendChild(filenode);
            callback();
        } else {
            console.log("Unknown file type to load.")
        }
    };

    var requireFiles = function () {
        var index = 0;
        return function (files, callback) {
            index += 1;
            require(files[index - 1], callBackCounter);

            function callBackCounter() {
                if (index === files.length) {
                    index = 0;
                    callback();
                } else {
                    requireFiles(files, callback);
                }
            };
        };
    }();

    requireFiles(["/js/modal.js", "/js/api.js", "/js/ui.js", "/js/vanillaSelectBox.js"], function () {

        window.active_actions = {
            editing_form: false,
            editing_book: false,
            fetching_book: false

        };

        MODAL.onClose = function () {
            active_actions.editing_form = false;
            active_actions.editing_book = false;
        };


        MODAL.onOpen = function () {
            active_actions.editing_form = true;
        };


        /**
         * Genre selector listener
         */
        UI.listenOnClick("#category-container", '.category-item', function (event){
            let target = event.target.closest('.category-item');
            /* Remove the current active clase*/
            UI.removeClass('.category-item.active','active');
            UI.addClass(target,'active');
            updateBooks(target.dataset.id);
        },true);

        UI.listenOnClick("body","#addBook",function (event){
            UI.form.dataset.bookId = '*';
            MODAL.openModal();
        });
        UI.listenOnClick(".modal", '.btn.btn-success', submitForm);

        API.get('/api/author', {all: false}).then(function (resp) {
            let authors = resp.response_data.authors;

            [].forEach.call(authors, function (author) {
                UI.addAuthorForm(author);
            })
            UI.authorFromSelector = new vanillaSelectBox("[name=authors]");
        }).catch(console.warn)




        window.updateGenres = function updateGenres(){
            API.get('/api/genre', {all: false}).then(function (resp){
                let genres = resp.response_data.genres;
                genres.unshift(DEFAULT_GENRE);

                [].forEach.call(genres, function (genre){
                    let isAll = genre.id === '*';
                    document.querySelector("#category-container").appendChild(UI.createGenre(genre, isAll));
                    if (!isAll){
                        UI.addGenreForm(genre);
                    }
                });
                UI.genreFromSelector = new vanillaSelectBox("[name=genres]");
            }).catch(console.warn)
            updateBooks('*');
        }

        function updateBooks(genre){
            genre = genre || '*';
            let data = {};//{all: false, genres: [1, 3]}

            if (genre !== '*'){
                data.genres = [genre];
            }

            API.get('/api/book', data).then(function (resp){
                UI.removeAllChilds("#book-container");
                let books = resp.response_data.books;

                [].forEach.call(books, function (book) {
                    document.querySelector("#book-container").appendChild(UI.createBook(book))
                });

                /**
                 * three dots listener
                 */
                UI.listenOnClick("#book-container", '.dots', function (event) {
                    event.target.parentElement.querySelector(".edit-dropdown").classList.toggle("show");
                });



                /**
                 * Edit book
                 */
                UI.listenOnClick("#book-container", '.drop-btn.edit', editBook);


                /**
                 * Remove book
                 */
                UI.listenOnClick("#book-container", '.drop-btn.delete', removeBook);
            }).catch(console.warn)
        }

        function editBook(event) {
            let id = event.target.closest(".card").dataset.id;
            if (active_actions.fetching_book){
                return;
            }
            active_actions.fetching_book = true;

            API.get(`/api/book/${id}`, {all:true}).then(function (resp) {
                active_actions.fetching_book = false;
                let data = resp.response_data.book;

                let genres = data.genres.map(
                    function (e) {
                        return e.id.toString();
                    }
                );
                let authors = data.authors.map(
                    function (e) {
                        return e.id.toString();
                    }
                );
                UI.form.dataset.bookId = data.id;
                UI.form.querySelector("[name='name']").value = data.name;
                UI.form.querySelector("[name='description']").value = data.description;
                UI.form.querySelector("[name='edition']").value = data.edition;
                UI.genreFromSelector.setValue(genres);
                UI.authorFromSelector.setValue(authors);
                MODAL.openModal();
            });
        }

        function submitForm(){
            let book_id = UI.form.dataset.bookId,
                url = '/api/book' + (book_id !== '*' ? `/${book_id}` : ''),
                book_data = UI.getBookFormData(),
                emptyValues = [];

            Object.entries(book_data)
                .filter(([, value]) => (!Array.isArray(value) && ["", null].indexOf(typeof value === 'string' ? value.trim() : value) !== -1) || (Array.isArray(value) && !value.length))
                .forEach(([key, value]) => (emptyValues.push(key)));

            UI.removeClass("form :is(input,textarea, .vsb-main)", "warn")

            if (emptyValues.length){
                emptyValues.forEach(function (elname){
                    let domele = document.querySelector(`[name="${elname}"]`);

                    if (domele.nodeName === "SELECT"){
                        domele = document.getElementById(`btn-group-name=${elname}]`);
                    }
                    UI.addClass(domele, "warn")

                })
                return;
            }
            API.post(url, book_data).then(function (resp){
                MODAL.closeModal();
                updateBooks(document.querySelector(".category-item.active").dataset.id)

            }).catch(console.warn)

        }

        function removeBook(event) {
            let id = event.target.closest(".card").dataset.id;
            API.delete(`/api/book/${id}`).then(function (resp) {
                event.target.closest(".card").remove();
            });
        }
        updateGenres();

    })
})();
