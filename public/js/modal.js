(function (_w) {

    const MODAL = {
        showClass : 'show',
        modal: document.querySelector(".modal"),
        container: document.querySelector(".container-modal"),
        openModal,
        closeModal,
        onClose:null,
        onOpen: null
    };

    function openModal(){
        MODAL.modal.classList.add(MODAL.showClass);
        MODAL.container.classList.add(MODAL.showClass);
        if (typeof MODAL.onOpen == 'function') {
            MODAL.onOpen();
        }
    }
    function closeModal(){
        UI.resetForm();
        MODAL.modal.classList.remove(MODAL.showClass);
        MODAL.container.classList.remove(MODAL.showClass);
        if (typeof MODAL.onClose == 'function') {
            MODAL.onClose();
        }
    }


    document.querySelector(".modal a.cancel").addEventListener("click", function (e) {
        closeModal();
    });

    document.querySelector(".modal button.cancel").addEventListener("click", function (e) {
        closeModal()
    });
    document.querySelector(".container-modal").addEventListener("click", function (e) {
        if (e.target === MODAL.container)
            closeModal();
    });
    _w.MODAL = MODAL;

})(window);
