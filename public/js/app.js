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
        var modal = new Modal(document.querySelector(".modal"));

        window.active_actions = {
            editing_form: false,
            editing_book: false,
            fetching_book: false,
            actual_type: 'book'

        };

        modal.onClose = function () {
            active_actions.editing_form = false;
            active_actions.editing_book = false;
        };


        modal.onOpen = function () {
            active_actions.editing_form = true;
        };
        /**
         * three dots listener
         */
        UI.listenOnClick('body', '.dots', function (event) {
            event.target.parentElement.querySelector(".edit-dropdown").classList.toggle("show");
        });

        /**
         * Edit book
         */
        UI.listenOnClick('body', '.drop-btn.edit', editElement);

        /**
         * Remove book
         */
        UI.listenOnClick('body', '.drop-btn.delete', removeElement);


        UI.listenOnClick("body", ".menuItems a", function (event) {
            let target = event.target;

            active_actions.actual_type = target.dataset.type;

            UI.removeClass('.category-item.active', 'active');
            UI.addClass(target, 'active');

            /**
             * Change bold titles in menus
             */
            UI.removeClass('.menuItems a', 'active');
            UI.addClass(document.querySelector(`.menuItems a[data-type="${active_actions.actual_type}"]`), 'active');

            /**
             * Change visibility to sub-containers
             */
            UI.removeClass('.sub-container.active', 'active');
            UI.addClass(document.querySelector(`.sub-container[data-target="${active_actions.actual_type}"]`), 'active');

            /**
             * Change form type
             */

            UI.removeClass('form.active', 'active');
            UI.addClass(document.querySelector(`form[data-target="${active_actions.actual_type}"]`), 'active');
        });

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

        UI.listenOnClick("body",".add-element",function (event){

            /**
             * @type {string}
             */
            let _form = document.querySelector(`form[data-target="${active_actions.actual_type}"]`);
            _form.dataset.id = '*';
            modal.openModal();
        });

        UI.listenOnClick(".modal", '.btn.btn-success', submitForm);
        UI.listenOnClick(".modal", '.btn.btn-success', submitForm);
        UI.listenOnClick(".modal", 'form', function (event){
            event.preventDefault();
            submitForm();
        },false,'submit');


        window.updateAuthors =function updateAuthors(){
            API.get('/api/author', {all: false}).then(function (resp) {
                UI.removeAllChilds(UI.authorCardContainer);

                if (UI.authorFromSelector instanceof vanillaSelectBox){
                    UI.authorFromSelector.destroy();
                    UI.authorFromSelector = null;
                }

                let authors = resp.response_data.authors;

                [].forEach.call(authors, function (author) {
                    UI.addAuthorForm(author);
                    UI.authorCardContainer.appendChild(UI.createAuthor(author))
                })

                if (typeof UI.authorFromSelector == "undefined" || UI.authorFromSelector === null){
                    UI.authorFromSelector = new vanillaSelectBox("[name=authors]");
                }
            }).catch(console.warn)
        }




        window.updateGenres = function updateGenres(){
            UI.removeAllChilds("#category-container");
            UI.removeAllChilds(UI.genreCardContainer);

            API.get('/api/genre', {all: false}).then(function (resp){
                UI.removeAllChilds(UI.genreCardContainer);

                if (UI.genreFromSelector instanceof vanillaSelectBox) {
                    UI.genreFromSelector.destroy();
                    UI.genreFromSelector = null;
                }

                let genres = resp.response_data.genres;
                genres.unshift(DEFAULT_GENRE);

                [].forEach.call(genres, function (genre){
                    let isAll = genre.id === '*';
                    document.querySelector("#category-container").appendChild(UI.createGenreMenu(genre, isAll));
                    if (!isAll){
                        UI.addGenreForm(genre);
                        UI.genreCardContainer.appendChild(UI.createGenre(genre))
                    }

                });

                if (typeof UI.genreFromSelector == "undefined" || UI.genreFromSelector === null) {
                    UI.genreFromSelector = new vanillaSelectBox("[name=genres]");
                }


            }).catch(console.warn)
            updateBooks('*');
        }

        window.updateBooks = function updateBooks(genre){
            genre = genre || '*';
            let data = {};//{all: false, genres: [1, 3]}

            if (genre !== '*'){
                data.genres = [genre];
            }

            API.get('/api/book', data).then(function (resp){
                UI.removeAllChilds(UI.bookCardContainer);
                let books = resp.response_data.books;

                [].forEach.call(books, function (book) {
                    UI.bookCardContainer.appendChild(UI.createBook(book))
                });





            }).catch(console.warn)
        }

        function editElement(event) {
            let id = event.target.closest(".card").dataset.id,
                _type = event.target.closest(".card").dataset.type,
                _fetching_type = "fetching_"+_type;

            if (active_actions[_fetching_type]){
                return;
            }
            active_actions[_fetching_type] = true;

            API.get(`/api/${_type}/${id}`, {all:true}).then(function (resp) {
                let data = resp.response_data[_type];

                var _form =  document.querySelector(`form[data-target="${active_actions.actual_type}"]`);
                _form.dataset.id = data.id;
                _form.querySelector("[name='name']").value = data.name;

                if (data.hasOwnProperty("genres")){
                    let genres = data.genres.map(
                        function (e) {
                            return e.id.toString();
                        }
                    );
                    UI.genreFromSelector.setValue(genres);
                }
                if (data.hasOwnProperty("authors")) {
                    let authors = data.authors.map(
                        function (e) {
                            return e.id.toString();
                        }
                    );
                    UI.authorFromSelector.setValue(authors);
                }
                if (data.hasOwnProperty("description")) {
                    _form.querySelector("[name='description']").value = data.description;
                }
                if (data.hasOwnProperty("edition")) {
                    _form.querySelector("[name='edition']").value = data.edition;
                }
                modal.openModal();
            }).finally(function (){
                active_actions[_fetching_type] = false;
            });
        }

        function submitForm(){
            let _form = document.querySelector(`form[data-target="${active_actions.actual_type}"]`),
                book_id = _form.dataset.id,
                url = '/api/' + active_actions.actual_type + (book_id !== '*' ? `/${book_id}` : ''),
                data = UI.getFormData(document.querySelector(`form[data-target="${active_actions.actual_type}"]`)),
                emptyValues = [];

            Object.entries(data)
                .filter(([, value]) => (!Array.isArray(value) && ["", null].indexOf(typeof value === 'string' ? value.trim() : value) !== -1) || (Array.isArray(value) && !value.length))
                .forEach(([key, value]) => (emptyValues.push(key)));

            UI.removeClass("form :is(input,textarea, .vsb-main)", "warn")

            if (emptyValues.length){
                emptyValues.forEach(function (elname){
                    let domele = document.querySelector(`form[data-target="${active_actions.actual_type}"] [name="${elname}"]`);

                    if (domele.nodeName === "SELECT"){
                        domele = document.getElementById(`btn-group-name=${elname}]`);
                    }
                    UI.addClass(domele, "warn")

                })
                return;
            }
            API.post(url, data).then(function (resp){
                updateActualInfo();
                modal.closeModal();
            }).catch(console.warn)

        }

        function removeElement(event) {
            let id = event.target.closest(".card").dataset.id;
            API.delete(`/api/${active_actions.actual_type}/${id}`).then(function (resp) {
                event.target.closest(".card").remove();
                updateActualInfo();
            });
        }

        function updateActualInfo(){
            var fn = window["update" + (active_actions.actual_type.charAt(0).toUpperCase() + active_actions.actual_type.slice(1)) + 's'];

            if (typeof fn === 'function') {
                fn();
            }
        }
        updateGenres();
        updateAuthors();


    })
})();
