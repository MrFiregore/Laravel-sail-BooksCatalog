(function (_w){
    /**
     * @typedef {Object} API
     * @this API
     */
    const API = {
        get,
        post,
        put,
        delete: _delete
    };

    function get(url, parameters) {
        parameters = parameters || {};
        const requestOptions = {
            method: 'GET'
        };
        var _url = new URL(location.origin+url),
            add_string = "";

        Object.keys(parameters).forEach(function (key) {
            var tmp_var = parameters[key];
            if (Array.isArray(tmp_var)){
                add_string += tmp_var.map(function (el, idx) {
                    return key +'[' + idx + ']=' + el;
                }).join('&')
            }
            else{
                _url.searchParams.append(key, tmp_var);
            }
        });
        add_string = (!!_url.search.length && !!add_string.length ? '' : '?') + add_string;
        return fetch(_url.toString() + (!!_url.search.length && !!add_string.length ? '&' : '') + add_string, requestOptions).then(handleResponse);
    }
    function _getBodyFromJSON(json){
        var formData = new FormData();
        for (const [name, value] of Object.entries(json)) {
            _name = name + (Array.isArray(value) ? '[]' : '');
            if (Array.isArray(value)) {
                value.forEach(function (ele) {
                    formData.append(_name, ele);
                })
            } else {
                formData.append(_name, value);
            }
        }
        return formData;
    }
    function post(url, body) {

        const requestOptions = {
            method: 'POST',
            body: _getBodyFromJSON(body)
        };
        return fetch(url, requestOptions).then(handleResponse);
    }

    function put(url, body) {
        const requestOptions = {
            method: 'PUT',
            body: _getBodyFromJSON(body)
        };
        return fetch(url, requestOptions).then(handleResponse);
    }

    function _delete(url) {
        const requestOptions = {
            method: 'DELETE'
        };
        return fetch(url, requestOptions).then(handleResponse);
    }


    function handleResponse(response) {
        return response.text().then(text => {
            const data = text && JSON.parse(text);
            if (!response.ok || data.result.status !== 'OK') {
                const error = (data && data.message) || response.statusText;
                return Promise.reject(error);
            }

            return data;
        });
    }
    _w.API = API;
})(window)
