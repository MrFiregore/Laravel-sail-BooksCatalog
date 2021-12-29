(function (_w) {
    /**
     * @typedef {Object} UI
     * @this UI
     */
    const UI = {
        genreFromSelector: null,
        authorFromSelector: null,
        genresForm: document.querySelector("select[name=genres]"),
        authorsForm: document.querySelector("select[name=authors]"),
        bookCardContainer: document.querySelector('.sub-container[data-target="book"] .card-container'),
        genreCardContainer: document.querySelector('.sub-container[data-target="genre"] .card-container'),
        authorCardContainer: document.querySelector('.sub-container[data-target="author"] .card-container'),
        listenOnClick,
        createGenre,
        createGenreMenu,
        createBook,
        createAuthor,
        removeClass,
        addClass,
        removeAllChilds,
        addGenreForm,
        addAuthorForm,
        addOptionToSelect,
        resetForms,
        getFormData
    };


    if (typeof Element.prototype.clearChildren === 'undefined') {
        Object.defineProperty(Element.prototype, 'clearChildren', {
            configurable: true,
            enumerable: false,
            value: function () {
                while (this.firstChild) this.removeChild(this.lastChild);
            }
        });
    }

    function createElementFromHTML(htmlString) {
        var div = document.createElement('div');
        div.innerHTML = htmlString.trim();

        // Change this to div.childNodes to support multiple top-level nodes
        return div.firstChild;
    }


    function removeAllChilds(selector) {
        var node = isElement(selector) ? selector : document.querySelector(selector);
        node.clearChildren();
    }

    function isNode(o) {
        return (
            typeof Node === "object" ? o instanceof Node :
                o && typeof o === "object" && typeof o.nodeType === "number" && typeof o.nodeName === "string"
        );
    }

    function isElement(o) {
        return (
            typeof HTMLElement === "object" ? o instanceof HTMLElement : //DOM2
                o && typeof o === "object" && o !== null && o.nodeType === 1 && typeof o.nodeName === "string"
        );
    }

    function isNodeList(o) {
        return NodeList.prototype.isPrototypeOf(o);
    }

    function removeClass(selector, classToRemove) {
        var elems = isElement(selector) ? selector : document.querySelectorAll(selector);
        elems = isNodeList(elems) ? elems : [elems];
        [].forEach.call(elems, function (el) {
            el.classList.remove(classToRemove);
        });
        return elems;
    }

    function addClass(selector, classToAdd) {
        var elems = isElement(selector) ? selector : document.querySelectorAll(selector);
        elems = isNodeList(elems) ? elems : [elems];

        [].forEach.call(elems, function (el) {
            el.classList.add(classToAdd);
        });
        return elems;
    }

    function addOptionToSelect(select, text, key) {
        select = isElement(select) ? select : document.querySelector(select);
        select.options[select.options.length] = new Option(text, key);
    }

    function addGenreForm(genre) {
        addOptionToSelect(UI.genresForm, genre.name, genre.id);
    }

    function addAuthorForm(author) {
        addOptionToSelect(UI.authorsForm, author.name, author.id);
    }

    function listenOnClick(base, target, callback, childs, _event) {
        base = isElement(base) ? base : document.querySelector(base);
        childs = childs || false;
        _event = _event || 'click';
        base.addEventListener(_event, function (event) {
            if ((target && event.target.matches(target)) || (childs && event.target.closest(target) !== null)){
                event.preventDefault();
                callback(event);
            }
            return;
        }, false);
    }

    function createGenreMenu(data, active) {
        active = active || false;
        return createElementFromHTML(`
        <div class="category-item ${active ? 'active' : ''}" data-id="${data.id}">
                    <a href="#" style="text-decoration: none; ">
                        <img src="${data.photo}" alt="image" class="category-container-icon"> <br>
                        <label class="category-container-label">${data.name}</label>
                    </a>
                </div>
        `);
    }

    function createCardElement(data, _type) {
        _type = _type || 'book';
        _isBook = _type === 'book';
        if (_isBook){
            var genres = data.genres.map(
                function (e) {
                    return e.name;
                }
            ).join();
            var authors = data.authors.map(
                function (e) {
                    return e.name;
                }
            ).join();
        }

        return createElementFromHTML(`
            <div class="card" data-id="${data.id}" data-type="${_type}">
                <div class="thumbnail">
                    <img class="left" src="${data.photo}"/>
                </div>
                <div class="right">
                    <div class="dots"></div>
                    <div class="edit-dropdown">
                        <button class="drop-btn edit">Edit</button>
                        <hr>
                        <button class="drop-btn delete">Delete</button>
                    </div>
                    <h5>${data.name}</h5>
                    `
                        +
                            (
                                _isBook ? `<p class="author">${authors}</p>` : ''
                            )
                        +
                            (
                                _isBook ? `<p class="description text-grey">${data.description}</p>` : ''
                            )
                        +
                            (
                                _isBook ? `<p class="text-grey genres">${genres}</p>` : ''
                            )
                        +
                    `
                </div>
            </div>
        `);
    }

    function createBook(data) {
        return createCardElement(data, 'book');
    }

    function createAuthor(data) {
        return createCardElement(data, 'author');
    }

    function createGenre(data) {
        return createCardElement(data, 'genre');
    }



    function getSelectValues(select) {
        var result = [];
        var options = select && select.options;
        var opt;

        for (var i = 0, iLen = options.length; i < iLen; i++) {
            opt = options[i];

            if (opt.selected) {
                result.push(opt.value || opt.text);
            }
        }
        return result;
    }

    function getFormData(form) {
        let formData = new FormData(form),
            _type = form.dataset.target;

        // Create an object to hold the name/value pairs
        return _type === 'book' ?
            {
                name: formData.get("name"),
                description: formData.get("description"),
                edition: formData.get("edition"),
                photo: formData.get("photo"),
                genres: getSelectValues(UI.genresForm),
                authors: getSelectValues(UI.authorsForm)
            }
                :
            {
                name: formData.get("name"),
                photo: formData.get("photo"),
            };

    }

    function resetForms(){
        [].forEach.call(document.querySelectorAll("form"), function (form) {
            form.reset();
        });
        UI.removeClass("form :is(input,textarea, .vsb-main)", "warn")
        UI.genreFromSelector.empty();
        UI.authorFromSelector.empty();
    }
    _w.UI = UI;
})(window)
