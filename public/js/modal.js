(function (_w) {

    function Modal(modal) {
        this.modal = modal;
        this.showClass = 'show';
        this.container = document.querySelector(".container-modal");
        this.onClose = null;
        this.onOpen = null

        this.openModal = function () {
            this.modal.classList.add(this.showClass);
            this.container.classList.add(this.showClass);
            if (typeof this.onOpen == 'function') {
                this.onOpen();
            }
        }

        this.closeModal = function () {
            UI.resetForms();
            this.modal.classList.remove(this.showClass);
            this.container.classList.remove(this.showClass);
            if (typeof this.onClose == 'function') {
                this.onClose();
            }
        }
        var _this = this;

        document.querySelector(".modal a.cancel").addEventListener("click", function (e) {
            _this.closeModal();
        });

        document.querySelector(".modal button.cancel").addEventListener("click", function (e) {
            _this.closeModal()
        });
        document.querySelector(".container-modal").addEventListener("click", function (e) {
            if (e.target === _this.container)
                _this.closeModal();
        });

    }



    _w.Modal = Modal;
})(window);
